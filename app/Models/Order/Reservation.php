<?php

declare(strict_types=1);

namespace App\Models\Order;

use App\Models\Account\User;
use App\Models\Room\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'room_id',
        'user_id',
        'reservation_date',
        'status',
    ];

    public function scopeActive(Builder $builder): Builder
    {
        return $builder->whereIn('status', [ReservationStatuses::IN_PROGRESS, ReservationStatuses::CONFIRMED]);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
