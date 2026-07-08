<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoadSegment;
use App\Models\InfrastructureAsset;
use App\Models\TrafficAccident;

class InfrastructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Road Segments
        $roads = [
            [
                'name' => 'Autoput E-75 (Deonica kroz Novi Beograd i Gazelu)',
                'category' => 'autoput',
                'length_km' => 7.50,
                'asphalt_type' => 'SMA (Split Matrix Asphalt)',
                'status' => 'prohodno',
                'start_lat' => 44.81000000,
                'start_lng' => 20.42000000,
                'end_lat' => 44.76000000,
                'end_lng' => 20.48000000,
                'installed_at' => '2021-05-15',
                'warranty_until' => '2028-05-15',
            ],
            [
                'name' => 'Brankov most i prilazi',
                'category' => 'lokalni',
                'length_km' => 0.80,
                'asphalt_type' => 'Liveni asfalt',
                'status' => 'radovi',
                'start_lat' => 44.81520000,
                'start_lng' => 20.44970000,
                'end_lat' => 44.81300000,
                'end_lng' => 20.45780000,
                'installed_at' => '2018-09-10',
                'warranty_until' => '2025-09-10',
            ],
            [
                'name' => 'Bulevar kralja Aleksandra',
                'category' => 'lokalni',
                'length_km' => 4.20,
                'asphalt_type' => 'Asfalt beton',
                'status' => 'prohodno',
                'start_lat' => 44.81260000,
                'start_lng' => 20.46310000,
                'end_lat' => 44.79320000,
                'end_lng' => 20.50550000,
                'installed_at' => '2020-04-18',
                'warranty_until' => '2026-04-18',
            ],
            [
                'name' => 'Ulica Kneza Miloša',
                'category' => 'lokalni',
                'length_km' => 1.60,
                'asphalt_type' => 'Asfalt beton',
                'status' => 'zatvoreno',
                'start_lat' => 44.80990000,
                'start_lng' => 20.45710000,
                'end_lat' => 44.79970000,
                'end_lng' => 20.46740000,
                'installed_at' => '2019-10-05',
                'warranty_until' => '2024-10-05',
            ]
        ];

        foreach ($roads as $road) {
            RoadSegment::updateOrCreate(['name' => $road['name']], $road);
        }

        // 2. Seed Infrastructure Assets
        $assets = [
            [
                'type' => 'Znak',
                'gps_lat' => 44.79500000,
                'gps_lng' => 20.44000000,
                'status' => 'active',
                'installed_at' => '2021-05-15',
                'warranty_until' => '2031-05-15',
                'properties_json' => [
                    'sign_type' => 'Ograničenje brzine 80',
                    'material' => 'retroreflektujući lim',
                    'contractor' => 'Putevi SRB'
                ]
            ],
            [
                'type' => 'Semafor',
                'gps_lat' => 44.80800000,
                'gps_lng' => 20.46000000,
                'status' => 'damaged',
                'installed_at' => '2022-03-20',
                'warranty_until' => '2027-03-20',
                'properties_json' => [
                    'bulb_type' => 'LED',
                    'phases' => 3,
                    'contractor' => 'Siemens Traffic'
                ]
            ],
            [
                'type' => 'Most',
                'gps_lat' => 44.79890000,
                'gps_lng' => 20.44280000,
                'status' => 'active',
                'installed_at' => '2012-11-12',
                'warranty_until' => '2032-11-12',
                'properties_json' => [
                    'structural_type' => 'čelični gredni',
                    'length_m' => 470,
                    'contractor' => 'Mostogradnja'
                ]
            ],
            [
                'type' => 'Horizontalna signalizacija',
                'gps_lat' => 44.80400000,
                'gps_lng' => 20.46200000,
                'status' => 'repairing',
                'installed_at' => '2023-09-01',
                'warranty_until' => '2024-09-01',
                'properties_json' => [
                    'paint_type' => 'hladna plastika',
                    'color' => 'bela pešačka zona',
                    'contractor' => 'Signal doo'
                ]
            ]
        ];

        foreach ($assets as $asset) {
            InfrastructureAsset::create($asset);
        }

        // 3. Seed Traffic Accidents — više tačaka za vidljivu heatmapu
        $accidents = [
            // Klaster 1: Most Gazela (crna tačka)
            ['description' => 'Lančani sudar tri vozila na mostu Gazela', 'gps_lat' => 44.79850, 'gps_lng' => 20.44300, 'severity' => 'teska', 'reported_at' => '2026-05-20 08:30:00'],
            ['description' => 'Nalet na pešaka - Gazela', 'gps_lat' => 44.79870, 'gps_lng' => 20.44350, 'severity' => 'fatalna', 'reported_at' => '2026-04-12 22:10:00'],
            ['description' => 'Sudar dva kamiona na Gazeli', 'gps_lat' => 44.79830, 'gps_lng' => 20.44280, 'severity' => 'teska', 'reported_at' => '2026-03-05 06:45:00'],
            ['description' => 'Sletanje sa puta - prilaz Gazeli', 'gps_lat' => 44.79900, 'gps_lng' => 20.44400, 'severity' => 'laka', 'reported_at' => '2026-02-18 14:20:00'],
            ['description' => 'Sudar u koloni - Gazela', 'gps_lat' => 44.79810, 'gps_lng' => 20.44250, 'severity' => 'teska', 'reported_at' => '2026-01-30 07:55:00'],

            // Klaster 2: Kneza Miloša
            ['description' => 'Težak sudar na pešačkom prelazu - Kneza Miloša', 'gps_lat' => 44.80400, 'gps_lng' => 20.46210, 'severity' => 'fatalna', 'reported_at' => '2026-05-18 23:45:00'],
            ['description' => 'Motociklista oboren na Kneza Miloša', 'gps_lat' => 44.80420, 'gps_lng' => 20.46250, 'severity' => 'teska', 'reported_at' => '2026-04-22 17:30:00'],
            ['description' => 'Sudar na raskrsnici Kneza Miloša', 'gps_lat' => 44.80380, 'gps_lng' => 20.46180, 'severity' => 'laka', 'reported_at' => '2026-03-11 09:15:00'],

            // Klaster 3: Bulevar kralja Aleksandra
            ['description' => 'Sletanje motocikla na Bulevaru', 'gps_lat' => 44.80000, 'gps_lng' => 20.49000, 'severity' => 'laka', 'reported_at' => '2026-05-25 19:15:00'],
            ['description' => 'Sudar na tramvajskoj pruzi - BKA', 'gps_lat' => 44.80050, 'gps_lng' => 20.48950, 'severity' => 'teska', 'reported_at' => '2026-04-03 08:00:00'],
            ['description' => 'Nalet na biciklistu - BKA', 'gps_lat' => 44.79980, 'gps_lng' => 20.49100, 'severity' => 'teska', 'reported_at' => '2026-02-14 16:40:00'],

            // Klaster 4: Autoput E-75
            ['description' => 'Prevrnuo se kamion na E-75', 'gps_lat' => 44.78500, 'gps_lng' => 20.43500, 'severity' => 'teska', 'reported_at' => '2026-05-01 04:20:00'],
            ['description' => 'Lančani sudar na E-75 (magla)', 'gps_lat' => 44.78550, 'gps_lng' => 20.43600, 'severity' => 'fatalna', 'reported_at' => '2026-01-15 06:00:00'],
            ['description' => 'Sletanje sa E-75 kod petlje', 'gps_lat' => 44.78600, 'gps_lng' => 20.43700, 'severity' => 'laka', 'reported_at' => '2026-03-20 21:30:00'],
        ];

        foreach ($accidents as $accident) {
            TrafficAccident::updateOrCreate(
                ['description' => $accident['description']],
                $accident
            );
        }
    }
}
