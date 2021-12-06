<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account\User;
use App\Models\Room\Room;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Routing\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportsController extends Controller
{
    public function __construct(
        private ResponseFactory $responseFactory
    ) {
    }

    public function exportBooked(Room $room): StreamedResponse
    {
        return $this->exportCsv('booked_rooms.csv', $room->reservations()->active()->get());
    }

    public function exportReservations(User $user): StreamedResponse
    {
        return $this->exportCsv('user_reservations.csv', $user->reservations()->get());
    }

    private function exportCsv(
        string     $fileName,
        Collection $items
    ): StreamedResponse {

        $columns = [
            'id',
            'reservation_date',
            'status',
        ];

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($items as $item) {
                $row = [];

                foreach ($columns as $column) {
                    $row[$column] = $item->{$column};
                }

                fputcsv($file, $row);
            }

        };

        return $this->responseFactory->stream($callback, Response::HTTP_CREATED, $headers);
    }
}
