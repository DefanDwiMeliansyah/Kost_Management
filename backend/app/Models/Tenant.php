<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'full_name',
        'id_card_number',
        'phone_number',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'occupation',
        'institution_name',
        'check_in_date',
        'check_out_date',
        'rent_duration',
        'monthly_rent',
        'deposit_amount',
        'status',
        'id_card_photo',
        'tenant_photo',
        'notes'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'monthly_rent' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'rent_duration' => 'integer'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Room
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Scope untuk tenant aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope untuk tenant yang sudah pindah
    public function scopeMovedOut($query)
    {
        return $query->where('status', 'moved_out');
    }
}