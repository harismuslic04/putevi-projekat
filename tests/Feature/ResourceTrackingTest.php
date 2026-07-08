<?php

use App\Models\User;
use App\Models\WorkOrder;
use App\Models\IssueReport;
use App\Models\Resource;
use Livewire\Livewire;
use App\Livewire\WorkerDashboard;
use App\Livewire\Analytics;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    $this->seed(\Database\Seeders\ResourceSeeder::class);
});

test('default resources are correctly seeded', function () {
    $this->assertDatabaseHas('resources', [
        'name' => 'Asfaltna masa',
        'unit' => 'kg',
        'type' => 'material'
    ]);
    $this->assertDatabaseHas('resources', [
        'name' => 'Kamion kiper',
        'unit' => 'sati',
        'type' => 'machine'
    ]);
});

test('terenski radnik can log resources and complete work order', function () {
    $worker = User::role('terenski_radnik')->first();

    $issue = IssueReport::create([
        'user_id' => $worker->id,
        'type' => 'Udarna rupa',
        'description' => 'Velika rupa na glavnoj ulici',
        'gps_lat' => 44.7866,
        'gps_lng' => 20.4489,
        'status' => 'prijavljeno'
    ]);

    $workOrder = WorkOrder::create([
        'issue_report_id' => $issue->id,
        'assigned_to_user_id' => $worker->id,
        'priority' => 'high',
        'status' => 'in_progress',
        'started_at' => now()
    ]);

    $resource = Resource::where('name', 'Asfaltna masa')->first();

    Livewire::actingAs($worker)
        ->test(WorkerDashboard::class)
        ->call('openCompletionModal', $workOrder->id)
        ->set('selectedResourceId', $resource->id)
        ->set('quantity', 150.50)
        ->call('addResource')
        ->assertCount('loggedResources', 1)
        ->call('completeWorkWithResources')
        ->assertHasNoErrors();

    // Provera da li je nalog završen i status prijave 'sanirano'
    $workOrder->refresh();
    $issue->refresh();

    expect($workOrder->status)->toBe('completed');
    expect($issue->status)->toBe('sanirano');

    // Provera unosa u pivot tabelu
    $this->assertDatabaseHas('resource_work_order', [
        'work_order_id' => $workOrder->id,
        'resource_id' => $resource->id,
        'quantity' => 150.50,
        'cost' => 150.50 * $resource->cost_per_unit
    ]);
});

test('manager can access analytics dashboard', function () {
    $manager = User::role('menadzer')->first();

    Livewire::actingAs($manager)
        ->test(Analytics::class)
        ->assertOk()
        ->assertSee('Utrošen Budžet')
        ->assertSee('Vreme Sanacije');
});
