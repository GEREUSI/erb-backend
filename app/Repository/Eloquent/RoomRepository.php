<?php

declare(strict_types=1);

namespace App\Repository\Eloquent;

use App\Models\Room\Room;
use App\Repository\RoomRepositoryInterface;
use JetBrains\PhpStorm\Pure;

class RoomRepository extends BaseRepository implements RoomRepositoryInterface
{
    #[Pure]
    public function __construct(
        Room $model
    ) {
        $this->model = $model;

        parent::__construct($model);
    }
}
