<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\IssueReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class DriverDashboard extends Component
{
    public $myReports = [];

    public function mount()
    {
        $this->loadReports();
    }

    #[On('issue-created')]
    public function loadReports()
    {
        $this->myReports = IssueReport::with('workOrders')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.driver-dashboard');
    }
}
