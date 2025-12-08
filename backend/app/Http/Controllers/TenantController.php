<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * Get all tenants with pagination, search, and filter
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');
            $status = $request->get('status');
            $roomId = $request->get('room_id');

            $query = Tenant::with(['room', 'user']);

            // Search by name, ID card, phone
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'ILIKE', "%{$search}%")
                      ->orWhere('id_card_number', 'ILIKE', "%{$search}%")
                      ->orWhere('phone_number', 'ILIKE', "%{$search}%")
                      ->orWhere('email', 'ILIKE', "%{$search}%");
                });
            }

            // Filter by status
            if ($status) {
                $query->where('status', $status);
            }

            // Filter by room
            if ($roomId) {
                $query->where('room_id', $roomId);
            }

            $tenants = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $tenants
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tenants',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new tenant
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'full_name' => 'required|string|max:255',
            'id_card_number' => 'required|string|unique:tenants,id_card_number|max:50',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'check_in_date' => 'required|date',
            'check_out_date' => 'nullable|date|after:check_in_date',
            'rent_duration' => 'nullable|integer|min:1',
            'monthly_rent' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,moved_out,pending',
            'id_card_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tenant_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if room is available
            $room = Room::findOrFail($request->room_id);
            if ($room->status === 'occupied') {
                return response()->json([
                    'success' => false,
                    'message' => 'Room is already occupied'
                ], 400);
            }

            $data = $request->except(['id_card_photo', 'tenant_photo']);
            $data['user_id'] = auth()->id(); // Get authenticated user

            // Handle ID Card Photo upload
            if ($request->hasFile('id_card_photo')) {
                $idCardFile = $request->file('id_card_photo');
                $idCardName = 'id_card_' . time() . '_' . Str::random(10) . '.' . $idCardFile->getClientOriginalExtension();
                $idCardFile->move(storage_path('app/public/tenants/id_cards'), $idCardName);
                $data['id_card_photo'] = 'tenants/id_cards/' . $idCardName;
            }

            // Handle Tenant Photo upload
            if ($request->hasFile('tenant_photo')) {
                $tenantFile = $request->file('tenant_photo');
                $tenantName = 'tenant_' . time() . '_' . Str::random(10) . '.' . $tenantFile->getClientOriginalExtension();
                $tenantFile->move(storage_path('app/public/tenants/photos'), $tenantName);
                $data['tenant_photo'] = 'tenants/photos/' . $tenantName;
            }

            $tenant = Tenant::create($data);

            // Update room status to occupied if tenant is active
            if ($tenant->status === 'active') {
                $room->update(['status' => 'occupied']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tenant created successfully',
                'data' => $tenant->load(['room', 'user'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single tenant
     */
    public function show($id)
    {
        try {
            $tenant = Tenant::with(['room', 'user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $tenant
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update tenant
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'sometimes|required|exists:rooms,id',
            'full_name' => 'sometimes|required|string|max:255',
            'id_card_number' => 'sometimes|required|string|max:50|unique:tenants,id_card_number,' . $id,
            'phone_number' => 'sometimes|required|string|max:20',
            'email' => 'nullable|email|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'check_in_date' => 'sometimes|required|date',
            'check_out_date' => 'nullable|date|after:check_in_date',
            'rent_duration' => 'nullable|integer|min:1',
            'monthly_rent' => 'sometimes|required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,moved_out,pending',
            'id_card_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tenant_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tenant = Tenant::findOrFail($id);
            $oldRoomId = $tenant->room_id;
            $oldStatus = $tenant->status;

            $data = $request->except(['id_card_photo', 'tenant_photo']);

            // Handle ID Card Photo upload
            if ($request->hasFile('id_card_photo')) {
                // Delete old photo
                if ($tenant->id_card_photo && file_exists(storage_path('app/public/' . $tenant->id_card_photo))) {
                    unlink(storage_path('app/public/' . $tenant->id_card_photo));
                }

                $idCardFile = $request->file('id_card_photo');
                $idCardName = 'id_card_' . time() . '_' . Str::random(10) . '.' . $idCardFile->getClientOriginalExtension();
                $idCardFile->move(storage_path('app/public/tenants/id_cards'), $idCardName);
                $data['id_card_photo'] = 'tenants/id_cards/' . $idCardName;
            }

            // Handle Tenant Photo upload
            if ($request->hasFile('tenant_photo')) {
                // Delete old photo
                if ($tenant->tenant_photo && file_exists(storage_path('app/public/' . $tenant->tenant_photo))) {
                    unlink(storage_path('app/public/' . $tenant->tenant_photo));
                }

                $tenantFile = $request->file('tenant_photo');
                $tenantName = 'tenant_' . time() . '_' . Str::random(10) . '.' . $tenantFile->getClientOriginalExtension();
                $tenantFile->move(storage_path('app/public/tenants/photos'), $tenantName);
                $data['tenant_photo'] = 'tenants/photos/' . $tenantName;
            }

            $tenant->update($data);

            // Update room status logic
            if (isset($data['room_id']) && $data['room_id'] != $oldRoomId) {
                // Free up old room
                if ($oldRoomId) {
                    Room::where('id', $oldRoomId)->update(['status' => 'available']);
                }
                // Occupy new room if tenant is active
                if ($tenant->status === 'active') {
                    Room::where('id', $data['room_id'])->update(['status' => 'occupied']);
                }
            } elseif (isset($data['status']) && $data['status'] != $oldStatus) {
                // Status changed
                if ($data['status'] === 'active') {
                    Room::where('id', $tenant->room_id)->update(['status' => 'occupied']);
                } elseif ($data['status'] === 'moved_out') {
                    Room::where('id', $tenant->room_id)->update(['status' => 'available']);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tenant updated successfully',
                'data' => $tenant->load(['room', 'user'])
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete tenant
     */
    public function destroy($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            
            // Delete photos
            if ($tenant->id_card_photo && file_exists(storage_path('app/public/' . $tenant->id_card_photo))) {
                unlink(storage_path('app/public/' . $tenant->id_card_photo));
            }
            if ($tenant->tenant_photo && file_exists(storage_path('app/public/' . $tenant->tenant_photo))) {
                unlink(storage_path('app/public/' . $tenant->tenant_photo));
            }

            // Free up room
            if ($tenant->room_id) {
                Room::where('id', $tenant->room_id)->update(['status' => 'available']);
            }

            $tenant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tenant deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tenant',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tenant statistics
     */
    public function statistics()
    {
        try {
            $totalTenants = Tenant::count();
            $activeTenants = Tenant::where('status', 'active')->count();
            $movedOutTenants = Tenant::where('status', 'moved_out')->count();
            $pendingTenants = Tenant::where('status', 'pending')->count();
            
            // Monthly revenue
            $monthlyRevenue = Tenant::where('status', 'active')
                ->sum('monthly_rent');

            // Occupancy rate
            $totalRooms = Room::count();
            $occupiedRooms = Room::where('status', 'occupied')->count();
            $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_tenants' => $totalTenants,
                    'active_tenants' => $activeTenants,
                    'moved_out_tenants' => $movedOutTenants,
                    'pending_tenants' => $pendingTenants,
                    'monthly_revenue' => $monthlyRevenue,
                    'occupancy_rate' => $occupancyRate,
                    'available_rooms' => $totalRooms - $occupiedRooms
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tenant statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get tenants expiring soon (check_out_date within 30 days)
     */
    public function expiringSoon()
    {
        try {
            $tenants = Tenant::with(['room', 'user'])
                ->where('status', 'active')
                ->whereNotNull('check_out_date')
                ->whereBetween('check_out_date', [now(), now()->addDays(30)])
                ->orderBy('check_out_date', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tenants
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch expiring tenants',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}