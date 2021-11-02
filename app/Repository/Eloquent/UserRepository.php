<?php

declare(strict_types=1);

namespace App\Repository\Eloquent;

use App\Models\Account\User;
use App\Repository\UserRepositoryInterface;
use JetBrains\PhpStorm\Pure;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    #[Pure]
    public function __construct(
        User $model
    ) {
        $this->model = $model;

        parent::__construct($model);
    }
}
