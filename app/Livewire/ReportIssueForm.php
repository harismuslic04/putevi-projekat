<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\IssueReport;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class ReportIssueForm extends Component
{
    public $isOpen = false;
    public $lat;
    public $lng;
    public $type = 'Udarna rupa';
    public $description = '';

    #[On('map-clicked')]
    public function openForm($lat, $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->isOpen = true;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // u metrima
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    private function findClosestRoadSegment($lat, $lng)
    {
        $roads = \App\Models\RoadSegment::all();
        $closestRoad = null;
        $minDist = 300; // maksimalno 300 metara udaljenosti da bismo ga povezali

        foreach ($roads as $road) {
            if (is_null($road->start_lat) || is_null($road->start_lng)) continue;
            
            // Provera rastojanja deljenjem segmenta na 10 delova
            for ($i = 0; $i <= 10; $i++) {
                $fraction = $i / 10;
                $sampleLat = $road->start_lat + ($road->end_lat - $road->start_lat) * $fraction;
                $sampleLng = $road->start_lng + ($road->end_lng - $road->start_lng) * $fraction;
                
                $dist = $this->calculateDistance($lat, $lng, $sampleLat, $sampleLng);
                if ($dist < $minDist) {
                    $minDist = $dist;
                    $closestRoad = $road;
                }
            }
        }

        return $closestRoad;
    }

    public function submit()
    {
        $this->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'type' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        $latitude = $this->lat;
        $longitude = $this->lng;

        // 1. Pronađi najbliži putni pravac
        $closestRoad = $this->findClosestRoadSegment($latitude, $longitude);
        $roadSegmentId = $closestRoad ? $closestRoad->id : null;

        // 2. Detekcija duplih prijava (isti tip u krugu od 50 metara)
        $duplicate = IssueReport::where('type', $this->type)
            ->whereIn('status', ['prijavljeno', 'verifikovano', 'nalog_izdat'])
            ->get()
            ->first(function ($report) use ($latitude, $longitude) {
                return $this->calculateDistance($latitude, $longitude, $report->gps_lat, $report->gps_lng) < 50;
            });

        $status = 'prijavljeno';
        $duplicateOfId = null;
        $message = 'Prijava uspešno poslata!';

        if ($duplicate) {
            $status = 'duplikat';
            $duplicateOfId = $duplicate->id;
            $message = 'Problem na ovoj lokaciji je već prijavljen. Vaša prijava je pridružena postojećem zahtevu!';
        }

        IssueReport::create([
            'user_id' => Auth::id() ?? 1,
            'type' => $this->type,
            'description' => $this->description,
            'gps_lat' => $this->lat,
            'gps_lng' => $this->lng,
            'status' => $status,
            'road_segment_id' => $roadSegmentId,
            'duplicate_of_id' => $duplicateOfId,
        ]);

        $this->reset(['isOpen', 'type', 'description', 'lat', 'lng']);
        $this->dispatch('issue-created');
        session()->flash('message', $message);
    }

    public function render()
    {
        return view('livewire.report-issue-form');
    }
}
