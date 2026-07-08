<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\IssueReport;
use App\Models\WorkOrder;
use App\Models\User;

class DispatcherDashboard extends Component
{
    public $issues = [];
    public $activeWorkOrders = [];
    public $completedWorkOrders = [];
    public $workers = [];
    
    // Filter
    public $filterTime = 'all';

    // Modal state
    public $isOpen = false;
    public $isRegularOpen = false;
    public $selectedIssueId = null;
    
    // Form fields (Vanredni nalozi)
    public $assigned_worker_id = '';
    public $priority = 'normal';
    public $description = '';

    // Form fields (Redovni nalozi)
    public $regular_road_id = '';
    public $regular_worker_id = '';
    public $regular_priority = 'normal';
    public $regular_task_name = 'Čišćenje snega';
    public $regular_description = '';

    public $roads = [];

    public function mount()
    {
        $this->loadData();
    }

    public function updatedFilterTime()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Učitaj sve prijave građana koje nisu duplikati i nemaju aktivan nalog
        $this->issues = IssueReport::whereDoesntHave('workOrders', function($q) {
                $q->whereIn('status', ['pending', 'in_progress']);
            })
            ->whereIn('status', ['prijavljeno', 'verifikovano'])
            ->whereNull('duplicate_of_id')
            ->with(['roadSegment', 'duplicates'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Učitaj aktivne radne naloge
        $this->activeWorkOrders = WorkOrder::with(['issueReport', 'assignedUser', 'resources', 'roadSegment'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Učitaj završene radne naloge
        $query = WorkOrder::with(['issueReport', 'assignedUser', 'resources', 'roadSegment'])
            ->where('status', 'completed');
            
        if ($this->filterTime === 'week') {
            $query->where('completed_at', '>=', now()->startOfWeek());
        } elseif ($this->filterTime === 'month') {
            $query->where('completed_at', '>=', now()->startOfMonth());
        } elseif ($this->filterTime === 'year') {
            $query->where('completed_at', '>=', now()->startOfYear());
        }

        $this->completedWorkOrders = $query->orderBy('completed_at', 'desc')->get();

        // Učitaj radnike i puteve
        $this->workers = User::role('terenski_radnik')->get();
        $this->roads = \App\Models\RoadSegment::all();
    }

    public function verifyIssue($issueId)
    {
        $issue = IssueReport::find($issueId);
        if ($issue && $issue->status === 'prijavljeno') {
            $issue->update(['status' => 'verifikovano']);
            $this->loadData();
            session()->flash('message', 'Prijava je uspešno verifikovana!');
        }
    }

    public function openAssignModal($issueId)
    {
        $this->selectedIssueId = $issueId;
        $issue = IssueReport::with('roadSegment')->find($issueId);
        
        // Prioritizacija: ako je prijava na autoputu, automatski preporuči critical/high prioritet
        if ($issue && $issue->roadSegment && $issue->roadSegment->category === 'autoput') {
            $this->priority = 'critical';
        } else {
            $this->priority = 'normal';
        }

        $this->isOpen = true;
    }

    public function openRegularModal()
    {
        $this->isRegularOpen = true;
        $this->reset(['regular_road_id', 'regular_worker_id', 'regular_priority', 'regular_description']);
        $this->regular_task_name = 'Čišćenje snega';
    }

    public function createRegularWorkOrder()
    {
        $this->validate([
            'regular_road_id' => 'required|exists:road_segments,id',
            'regular_worker_id' => 'required|exists:users,id',
            'regular_priority' => 'required|in:low,normal,high,critical',
            'regular_task_name' => 'required|string',
            'regular_description' => 'nullable|string|max:1000',
        ]);

        WorkOrder::create([
            'issue_report_id' => null,
            'road_segment_id' => $this->regular_road_id,
            'assigned_to_user_id' => $this->regular_worker_id,
            'priority' => $this->regular_priority,
            'description' => $this->regular_task_name . ' - ' . ($this->regular_description ?: 'Redovno održavanje'),
            'status' => 'pending',
            'maintenance_type' => 'redovno',
        ]);

        $this->isRegularOpen = false;
        $this->loadData();
        session()->flash('message', 'Nalog za redovno održavanje je uspešno kreiran!');
    }

    public function cancelWorkOrder($workOrderId)
    {
        $workOrder = WorkOrder::find($workOrderId);
        if ($workOrder) {
            $workOrder->update(['status' => 'cancelled']);
            
            // Vrati prijavu na 'prijavljeno'
            if ($workOrder->issueReport) {
                $workOrder->issueReport->update(['status' => 'prijavljeno']);
            }
            
            $this->loadData();
            session()->flash('message', 'Radni nalog je uspešno otkazan i prijava je vraćena na čekanje!');
        }
    }

    public function assignWorkOrder()
    {
        $this->validate([
            'assigned_worker_id' => 'required|exists:users,id',
            'priority' => 'required|in:low,normal,high,critical',
            'description' => 'nullable|string|max:1000',
        ]);

        $issue = IssueReport::find($this->selectedIssueId);

        WorkOrder::create([
            'issue_report_id' => $this->selectedIssueId,
            'road_segment_id' => $issue ? $issue->road_segment_id : null,
            'assigned_to_user_id' => $this->assigned_worker_id,
            'priority' => $this->priority,
            'description' => $this->description ?: ($issue ? $issue->description : ''),
            'status' => 'pending',
            'maintenance_type' => 'vanredno',
        ]);

        // Postavi status na "Nalog izdat"
        if ($issue) {
            $issue->update(['status' => 'nalog_izdat']);
        }

        $this->reset(['isOpen', 'selectedIssueId', 'assigned_worker_id', 'priority', 'description']);
        $this->loadData();
        
        session()->flash('message', 'Radni nalog je uspešno kreiran i dodeljen!');
    }

    public function render()
    {
        return view('livewire.dispatcher-dashboard');
    }
}
