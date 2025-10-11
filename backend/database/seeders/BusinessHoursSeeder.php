<?php

namespace Database\Seeders;

use App\Infrastructure\WhatsApp\Models\BusinessHoursModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BusinessHoursSeeder extends Seeder
{
    public function run(): void
    {
        $businessHours = [
            [
                'day_of_week' => 'monday',
                'open_time' => '08:00:00',
                'close_time' => '18:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 'tuesday',
                'open_time' => '08:00:00',
                'close_time' => '18:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 'wednesday',
                'open_time' => '08:00:00',
                'close_time' => '18:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 'thursday',
                'open_time' => '08:00:00',
                'close_time' => '18:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 'friday',
                'open_time' => '08:00:00',
                'close_time' => '18:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 'saturday',
                'open_time' => '09:00:00',
                'close_time' => '14:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 'sunday',
                'open_time' => null,
                'close_time' => null,
                'is_closed' => true,
            ],
        ];

        foreach ($businessHours as $hours) {
            BusinessHoursModel::create(array_merge($hours, [
                'id' => Str::uuid(),
                'timezone' => 'America/Bogota',
            ]));
        }
    }
}