<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\WorkOrder;
use App\Models\IssueReport;
use Illuminate\Support\Facades\Auth;

class WorkerDashboard extends Component
{
    public $workOrders = [];
    public $completedOrders = [];

    // State for resource logging modal
    public $isCompletionModalOpen = false;
    public $selectedWorkOrderId = null;
    public $availableResources = [];
    public $selectedResourceId = '';
    public $quantity = '';
    public $loggedResources = [];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->workOrders = WorkOrder::with('issueReport')
            ->where('assigned_to_user_id', Auth::id())
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $this->completedOrders = WorkOrder::with(['issueReport', 'resources'])
            ->where('assigned_to_user_id', Auth::id())
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(10) // Prikazujemo poslednjih 10
            ->get();
    }

    public function startWork($id)
    {
        $workOrder = WorkOrder::find($id);
        if ($workOrder && $workOrder->assigned_to_user_id == Auth::id()) {
            $workOrder->update(['status' => 'in_progress', 'started_at' => now()]);
            $this->loadData();
        }
    }

    public function openCompletionModal($id)
    {
        $this->selectedWorkOrderId = $id;
        $this->availableResources = \App\Models\Resource::all();
        $this->loggedResources = [];
        $this->reset(['selectedResourceId', 'quantity']);
        $this->isCompletionModalOpen = true;
    }

    public function addResource()
    {
        $this->validate([
            'selectedResourceId' => 'required|exists:resources,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $resource = \App\Models\Resource::find($this->selectedResourceId);
        
        // Provera da li je već dodat
        foreach ($this->loggedResources as $index => $item) {
            if ($item['resource_id'] == $resource->id) {
                $this->loggedResources[$index]['quantity'] += floatval($this->quantity);
                $this->reset(['selectedResourceId', 'quantity']);
                return;
            }
        }

        $this->loggedResources[] = [
            'resource_id' => $resource->id,
            'name' => $resource->name,
            'quantity' => floatval($this->quantity),
            'unit' => $resource->unit,
            'cost_per_unit' => floatval($resource->cost_per_unit),
        ];

        $this->reset(['selectedResourceId', 'quantity']);
    }

    public function removeResource($index)
    {
        unset($this->loggedResources[$index]);
        $this->loggedResources = array_values($this->loggedResources);
    }

    public function completeWorkWithResources()
    {
        $workOrder = WorkOrder::find($this->selectedWorkOrderId);
        if ($workOrder && $workOrder->assigned_to_user_id == Auth::id()) {
            
            // Upis resursa i obračunatih troškova
            foreach ($this->loggedResources as $logged) {
                $workOrder->resources()->attach($logged['resource_id'], [
                    'quantity' => $logged['quantity'],
                    'cost' => $logged['quantity'] * $logged['cost_per_unit']
                ]);
            }

            $workOrder->update([
                'status' => 'completed', 
                'completed_at' => now()
            ]);
            
            // Ažuriraj i prijavu na 'sanirano'
            if ($workOrder->issueReport) {
                $workOrder->issueReport->update(['status' => 'sanirano']);
            }
            
            $this->isCompletionModalOpen = false;
            $this->selectedWorkOrderId = null;
            $this->loggedResources = [];
            
            $this->loadData();
            session()->flash('worker_message', 'Posao uspešno završen i utrošeni resursi su evidentirani!');
        }
    }

    public function render()
    {
        return view('livewire.worker-dashboard');
    }
}
