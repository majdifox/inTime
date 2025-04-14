<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FareSetting;

class FareSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear any existing fare settings
        FareSetting::query()->delete();

        // Insert fare settings based on the provided pricing structure
        $fareSettings = [
            [
                'vehicle_type' => 'share',
                'base_fare' => 50.00,
                'per_km_price' => 15.00,
                'per_minute_price' => 0.50,
                'minimum_fare' => 50.00
            ],
            [
                'vehicle_type' => 'comfort',
                'base_fare' => 80.00,
                'per_km_price' => 20.00,
                'per_minute_price' => 0.75,
                'minimum_fare' => 80.00
            ],
            [
                'vehicle_type' => 'women',
                'base_fare' => 100.00,
                'per_km_price' => 25.00,
                'per_minute_price' => 1.00,
                'minimum_fare' => 100.00
            ],
            [
                'vehicle_type' => 'wav',
                'base_fare' => 120.00,
                'per_km_price' => 30.00,
                'per_minute_price' => 1.25,
                'minimum_fare' => 120.00
            ],
            [
                'vehicle_type' => 'black',
                'base_fare' => 140.00,
                'per_km_price' => 35.00,
                'per_minute_price' => 1.50,
                'minimum_fare' => 140.00
            ],
        ];

        foreach ($fareSettings as $setting) {
            FareSetting::create($setting);
        }
    }
}