<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\IssueReport;
use App\Models\InfrastructureAsset;
use App\Models\RoadSegment;
use App\Models\TrafficAccident;
use Livewire\Attributes\On;

class IssueMap extends Component
{
    public $issues = [];
    public $assets = [];
    public $roads = [];
    public $accidents = [];

    public function mount()
    {
        $this->loadMapData();
    }

    #[On('issue-created')]
    public function loadMapData()
    {
        // 1. Učitaj aktivne prijave građana (preskoči duplikate)
        $this->issues = IssueReport::whereIn('status', ['prijavljeno', 'verifikovano', 'nalog_izdat'])
            ->select('id', 'type', 'description', 'gps_lat', 'gps_lng', 'status')
            ->get()
            ->toArray();

        // 2. Učitaj svu imovinu (putne objekte, znakove, semafore)
        $this->assets = InfrastructureAsset::all()
            ->map(function($asset) {
                return [
                    'id' => $asset->id,
                    'type' => $asset->type,
                    'status' => $asset->status,
                    'gps_lat' => floatval($asset->gps_lat),
                    'gps_lng' => floatval($asset->gps_lng),
                    'properties' => $asset->properties_json,
                    'warranty_until' => $asset->warranty_until ? $asset->warranty_until->format('d.m.Y') : 'Nema',
                    'installed_at' => $asset->installed_at ? $asset->installed_at->format('Y') : 'Nepoznato',
                ];
            })
            ->toArray();

        // 3. Učitaj sve deonice puteva
        $this->roads = RoadSegment::all()
            ->map(function($road) {
                return [
                    'id' => $road->id,
                    'name' => $road->name,
                    'category' => $road->category,
                    'length_km' => floatval($road->length_km),
                    'asphalt_type' => $road->asphalt_type,
                    'status' => $road->status,
                    'start_lat' => floatval($road->start_lat),
                    'start_lng' => floatval($road->start_lng),
                    'end_lat' => floatval($road->end_lat),
                    'end_lng' => floatval($road->end_lng),
                ];
            })
            ->toArray();

        // 4. Učitaj saobraćajne nezgode
        $this->accidents = TrafficAccident::all()
            ->map(function($accident) {
                return [
                    'id' => $accident->id,
                    'description' => $accident->description,
                    'severity' => $accident->severity,
                    'gps_lat' => floatval($accident->gps_lat),
                    'gps_lng' => floatval($accident->gps_lng),
                    'reported_at' => $accident->reported_at ? $accident->reported_at->format('d.m.Y H:i') : ''
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.issue-map');
    }
}
