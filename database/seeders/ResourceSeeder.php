<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Resource;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = [
            [
                'name' => 'Asfaltna masa',
                'unit' => 'kg',
                'type' => 'material',
                'cost_per_unit' => 0.15,
                'depreciation_per_unit' => 0.00,
            ],
            [
                'name' => 'Industrijska so',
                'unit' => 'kg',
                'type' => 'material',
                'cost_per_unit' => 0.08,
                'depreciation_per_unit' => 0.00,
            ],
            [
                'name' => 'Saobraćajni znak',
                'unit' => 'komad',
                'type' => 'material',
                'cost_per_unit' => 85.00,
                'depreciation_per_unit' => 0.00,
            ],
            [
                'name' => 'Kamion kiper',
                'unit' => 'sati',
                'type' => 'machine',
                'cost_per_unit' => 45.00,
                'depreciation_per_unit' => 15.00,
            ],
            [
                'name' => 'Bager / Rovokopač',
                'unit' => 'sati',
                'type' => 'machine',
                'cost_per_unit' => 60.00,
                'depreciation_per_unit' => 20.00,
            ],
            [
                'name' => 'Putar (Terenski radnik)',
                'unit' => 'sati',
                'type' => 'machine',
                'cost_per_unit' => 12.00,
                'depreciation_per_unit' => 0.00,
            ],
            [
                'name' => 'Farba za signalizaciju',
                'unit' => 'litar',
                'type' => 'material',
                'cost_per_unit' => 3.50,
                'depreciation_per_unit' => 0.00,
            ],
            [
                'name' => 'Valjak',
                'unit' => 'sati',
                'type' => 'machine',
                'cost_per_unit' => 55.00,
                'depreciation_per_unit' => 18.00,
            ],
        ];

        foreach ($resources as $resource) {
            Resource::updateOrCreate(
                ['name' => $resource['name']],
                $resource
            );
        }
    }
}
