<?php

namespace App\Services;

class SeatGeneratorService
{
    public function generate($aircraft, $count = 3, $occupiedSeats = [])
    {
        $seats = [];

        if (strtoupper($aircraft) === "ATR") {
            $rows = range(1, 18);
            $letters = ['A', 'C', 'D', 'F'];
        } else {
            $rows = range(1, 32);
            $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
        }

        foreach ($rows as $row) {
            foreach ($letters as $letter) {
                $seat = $row . $letter;

                if (!in_array($seat, $occupiedSeats)) {
                    $seats[] = $seat;
                }
            }
        }

        shuffle($seats);

        return array_slice($seats, 0, $count);
    }
}
