<?php

declare(strict_types=1);

namespace App\Models\Room;

use App\Models\Account\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
