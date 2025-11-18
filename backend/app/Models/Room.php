<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'floor',
        'room_type',
        'price',
        'status',
        'description',
        'facilities',
        'image'
    ];

    protected $casts = [
        'facilities' => 'array',
        'price' => 'decimal:2'
    ];
}