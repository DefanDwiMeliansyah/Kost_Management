<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Get all rooms with pagination
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $status = $request->get('status');
            $roomType = $request->get('room_type');

            $query = Room::query();

            if ($status) {
                $query->where('status', $status);
            }

            if ($roomType) {
                $query->where('room_type', $roomType);
            }

            $rooms = $query->orderBy('room_number', 'asc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $rooms
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rooms',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new room
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|string|unique:rooms,room_number',
            'floor' => 'required|integer|min:1',
            'room_type' => 'required|in:single,double,shared',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:available,occupied,maintenance',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $room = Room::create([
                'room_number' => $request->room_number,
                'floor' => $request->floor,
                'room_type' => $request->room_type,
                'price' => $request->price,
                'status' => $request->status ?? 'available',
                'description' => $request->description,
                'facilities' => $request->facilities
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Room created successfully',
                'data' => $room
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single room
     */
    public function show($id)
    {
        try {
            $room = Room::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $room
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update room
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|string|unique:rooms,room_number,' . $id,
            'floor' => 'required|integer|min:1',
            'room_type' => 'required|in:single,double,shared',
            'price' => 'required|numeric|min:0',
            'status' => 'nullable|in:available,occupied,maintenance',
            'description' => 'nullable|string',
            'facilities' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $room = Room::findOrFail($id);
            
            $room->update([
                'room_number' => $request->room_number,
                'floor' => $request->floor,
                'room_type' => $request->room_type,
                'price' => $request->price,
                'status' => $request->status ?? $room->status,
                'description' => $request->description,
                'facilities' => $request->facilities
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Room updated successfully',
                'data' => $room
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete room
     */
    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);
            $room->delete();

            return response()->json([
                'success' => true,
                'message' => 'Room deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room statistics
     */
    public function statistics()
    {
        try {
            $total = Room::count();
            $available = Room::where('status', 'available')->count();
            $occupied = Room::where('status', 'occupied')->count();
            $maintenance = Room::where('status', 'maintenance')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'available' => $available,
                    'occupied' => $occupied,
                    'maintenance' => $maintenance
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}