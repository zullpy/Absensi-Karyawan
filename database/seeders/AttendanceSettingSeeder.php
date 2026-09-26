<?php

namespace Database\Seeders;

use App\Models\OfficeSetting;
use App\Models\Shift;
use Illuminate\Database\Seeder;

class AttendanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Office Setting
        OfficeSetting::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Kantor Pusat',
                'latitude' => -6.9217000,
                'longitude' => 107.6071000,
                'radius_meters' => 50,
            ]
        );

        // Default Shifts
        $shifts = [
            [
                'name' => 'Shift 1 (Pagi)',
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
            ],
            [
                'name' => 'Shift 2 (Malam)',
                'start_time' => '19:00:00',
                'end_time' => '02:00:00',
            ],
            [
                'name' => 'Shift Reguler (Full Day)',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(
                ['name' => $shift['name']],
                $shift
            );
        }
    }
}
