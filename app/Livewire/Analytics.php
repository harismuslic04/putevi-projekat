<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WorkOrder;
use App\Models\IssueReport;
use App\Models\TrafficAccident;
use Illuminate\Support\Facades\DB;

class Analytics extends Component
{
    public $totalBudget = 0;
    public $avgResponseTime = 0;
    public $solvedCount = 0;
    public $activeCount = 0;

    // Novi KPI-jevi
    public $totalDepreciation = 0;
    public $avgUrgentResponseTime = 0;
    
    // Budžetiranje
    public $budgetRegularLimit = 300000;
    public $spentRegular = 0;
    
    public $budgetExtraordinaryLimit = 150000;
    public $spentExtraordinary = 0;

    // Tabela troškova po kilometru
    public $roadCosts = [];

    // Charts data
    public $issuesByTypeKeys = [];
    public $issuesByTypeValues = [];
    public $costByTypeKeys = [];
    public $costByTypeValues = [];

    // Heatmap data
    public $damagePoints = [];
    public $accidentPoints = [];

    // Evidencija materijala i mašina
    public $materialUsage = [];
    public $machineUsage = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // 1. KPI Solved & Active counts
        $this->solvedCount = WorkOrder::where('status', 'completed')->count();
        $this->activeCount = IssueReport::whereIn('status', ['prijavljeno', 'verifikovano', 'nalog_izdat'])->count();

        // 2. KPI Total Budget Spent
        $this->totalBudget = DB::table('resource_work_order')->sum('cost');

        // 3. KPI Prosečno vreme rešavanja (svi nalozi)
        $completedOrders = WorkOrder::with('issueReport')
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->get();
            
        $totalHours = 0;
        $count = 0;
        foreach ($completedOrders as $order) {
            $reported = $order->issueReport ? $order->issueReport->reported_at : $order->created_at;
            $completed = $order->completed_at;
            if ($reported && $completed) {
                $diffInHours = abs($completed->diffInHours($reported));
                $totalHours += $diffInHours;
                $count++;
            }
        }
        $this->avgResponseTime = $count > 0 ? round($totalHours / $count, 1) : 0;

        // 4. KPI Amortizacija mašina (količina * depreciation_per_unit)
        $this->totalDepreciation = DB::table('resource_work_order')
            ->join('resources', 'resource_work_order.resource_id', '=', 'resources.id')
            ->join('work_orders', 'resource_work_order.work_order_id', '=', 'work_orders.id')
            ->where('work_orders.status', 'completed')
            ->where('resources.type', 'machine')
            ->select(DB::raw('SUM(resource_work_order.quantity * resources.depreciation_per_unit) as total_dep'))
            ->first()
            ->total_dep ?? 0;

        // 5. KPI Prosečno vreme reakcije na HITNE prijave (sneg, semafor, ili kritičan prioritet)
        $completedUrgentOrders = WorkOrder::with('issueReport')
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->where(function($q) {
                $q->where('priority', 'critical')
                  ->orWhereHas('issueReport', function($qi) {
                      $qi->whereIn('type', ['Poledica/Sneg', 'Neispravan semafor']);
                  });
            })
            ->get();

        $totalUrgentHours = 0;
        $urgentCount = 0;
        foreach ($completedUrgentOrders as $order) {
            $reported = $order->issueReport ? $order->issueReport->reported_at : $order->created_at;
            $completed = $order->completed_at;
            if ($reported && $completed) {
                $diffInHours = abs($completed->diffInHours($reported));
                $totalUrgentHours += $diffInHours;
                $urgentCount++;
            }
        }
        $this->avgUrgentResponseTime = $urgentCount > 0 ? round($totalUrgentHours / $urgentCount, 1) : 0;

        // 6. Budžetiranje: Redovno vs. Vanredno
        $completedRegular = WorkOrder::where('status', 'completed')
            ->where('maintenance_type', 'redovno')
            ->pluck('id')
            ->toArray();
        $this->spentRegular = DB::table('resource_work_order')
            ->whereIn('work_order_id', $completedRegular)
            ->sum('cost');

        $completedExtraordinary = WorkOrder::where('status', 'completed')
            ->where('maintenance_type', 'vanredno')
            ->pluck('id')
            ->toArray();
        $this->spentExtraordinary = DB::table('resource_work_order')
            ->whereIn('work_order_id', $completedExtraordinary)
            ->sum('cost');

        // 7. Obračun troškova po kilometru deonice puta
        $this->roadCosts = [];
        $roads = \App\Models\RoadSegment::all();
        foreach ($roads as $road) {
            // Skupi sve završene naloge na ovom putu (direktne i indirektne preko prijava)
            $directOrderIds = WorkOrder::where('status', 'completed')
                ->where('road_segment_id', $road->id)
                ->pluck('id')
                ->toArray();

            $indirectOrderIds = WorkOrder::where('status', 'completed')
                ->whereHas('issueReport', function($q) use ($road) {
                    $q->where('road_segment_id', $road->id);
                })
                ->pluck('id')
                ->toArray();

            $allOrderIds = array_unique(array_merge($directOrderIds, $indirectOrderIds));
            
            $roadCost = DB::table('resource_work_order')
                ->whereIn('work_order_id', $allOrderIds)
                ->sum('cost');

            $costPerKm = $road->length_km > 0 ? $roadCost / $road->length_km : 0;

            $this->roadCosts[] = [
                'name' => $road->name,
                'category' => $road->category,
                'length_km' => $road->length_km,
                'total_cost' => $roadCost,
                'cost_per_km' => $costPerKm
            ];
        }

        // 8. Grafikoni: Broj prijava po tipu
        $issuesByType = IssueReport::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();
            
        $this->issuesByTypeKeys = array_keys($issuesByType);
        $this->issuesByTypeValues = array_values($issuesByType);

        // 9. Grafikoni: Struktura troškova
        $resourceCosts = DB::table('resource_work_order')
            ->join('resources', 'resource_work_order.resource_id', '=', 'resources.id')
            ->select('resources.type', DB::raw('SUM(resource_work_order.cost) as total_cost'))
            ->groupBy('resources.type')
            ->get()
            ->pluck('total_cost', 'type')
            ->toArray();
            
        $translatedTypes = [];
        foreach ($resourceCosts as $type => $cost) {
            $translatedTypes[$type === 'material' ? 'Materijali' : 'Mehanizacija i rad'] = floatval($cost);
        }
        
        $this->costByTypeKeys = array_keys($translatedTypes);
        $this->costByTypeValues = array_values($translatedTypes);

        // 10. Heatmap tačke oštećenja
        $this->damagePoints = IssueReport::select('gps_lat', 'gps_lng')
            ->get()
            ->map(function($issue) {
                return [floatval($issue->gps_lat), floatval($issue->gps_lng), 0.6];
            })
            ->toArray();

        // Heatmap tačke nesreća ("crne tačke")
        $this->accidentPoints = TrafficAccident::select('gps_lat', 'gps_lng')
            ->get()
            ->map(function($acc) {
                return [floatval($acc->gps_lat), floatval($acc->gps_lng), 1.0];
            })
            ->toArray();

        // 11. Evidencija utrošenih materijala
        $this->materialUsage = DB::table('resource_work_order')
            ->join('resources', 'resource_work_order.resource_id', '=', 'resources.id')
            ->where('resources.type', 'material')
            ->select(
                'resources.name',
                'resources.unit',
                DB::raw('SUM(resource_work_order.quantity) as total_qty'),
                DB::raw('SUM(resource_work_order.cost) as total_cost')
            )
            ->groupBy('resources.name', 'resources.unit')
            ->get()
            ->toArray();

        // 12. Praćenje mašina i amortizacije
        $this->machineUsage = DB::table('resource_work_order')
            ->join('resources', 'resource_work_order.resource_id', '=', 'resources.id')
            ->where('resources.type', 'machine')
            ->select(
                'resources.name',
                'resources.unit',
                'resources.depreciation_per_unit',
                DB::raw('SUM(resource_work_order.quantity) as total_qty'),
                DB::raw('SUM(resource_work_order.cost) as total_cost'),
                DB::raw('SUM(resource_work_order.quantity * resources.depreciation_per_unit) as total_depreciation')
            )
            ->groupBy('resources.name', 'resources.unit', 'resources.depreciation_per_unit')
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.analytics');
    }
}
