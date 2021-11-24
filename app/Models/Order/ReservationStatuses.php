<?php

declare(strict_types=1);

namespace App\Models\Order;

class ReservationStatuses
{
    public const IN_PROGRESS = 'in_progress';

    public const CANCELED = 'canceled';

    public const CONFIRMED = 'confirmed';
}
