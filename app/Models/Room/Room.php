<?php

declare(strict_types=1);

namespace App\Models\Room;

use App\Models\Account\User;
use App\Models\Order\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'title',
        'address',
        'size',
        'description',
        'price',
        'typeId',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function avgRating(): string
    {
        return $this->rates()->avg('rate') ?? '0.0';
    }

    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
