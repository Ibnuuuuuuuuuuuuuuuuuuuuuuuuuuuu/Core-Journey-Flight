<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'flight_schedule_id',
        'booking_code',
        'full_name',
        'nik',
        'seat_class',
        'passenger_count',
        'ancillary_services',
        'status',
        'total_price',
        'paid_at',
    ];

    protected $casts = [
        'ancillary_services' => 'array',
        'total_price' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function flightSchedule(): BelongsTo
    {
        return $this->belongsTo(FlightSchedule::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
