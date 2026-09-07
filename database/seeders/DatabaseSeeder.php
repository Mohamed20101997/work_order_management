<?php

namespace Database\Seeders;

use App\Enums\AssetStatus;
use App\Enums\InspectionResult;
use App\Enums\Priority;
use App\Enums\WorkOrderStatus;
use App\Models\Activity;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Company;
use App\Models\Inspection;
use App\Models\Part;
use App\Models\PartUsage;
use App\Models\Report;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin User', 'email' => 'admin@example.com']);
        $manager = User::factory()->manager()->create(['name' => 'Factory Manager', 'email' => 'manager@example.com']);
        $tech1 = User::factory()->technician()->create(['name' => 'Technician One', 'email' => 'tech1@example.com']);
        $tech2 = User::factory()->technician()->create(['name' => 'Technician Two', 'email' => 'tech2@example.com']);
        User::factory()->viewer()->create(['name' => 'Viewer User', 'email' => 'viewer@example.com']);

        $types = collect([
            ['name' => 'Motor', 'code' => 'MTR'],
            ['name' => 'Pump', 'code' => 'PMP'],
            ['name' => 'Generator', 'code' => 'GEN'],
            ['name' => 'Compressor', 'code' => 'CMP'],
            ['name' => 'Other', 'code' => 'OTH'],
        ])->map(fn (array $type) => AssetType::create($type));

        $companies = Company::factory()->count(5)->create();
        $parts = Part::factory()->count(12)->create();

        $statuses = [
            AssetStatus::Received, AssetStatus::Received,
            AssetStatus::AwaitingInspection, AssetStatus::AwaitingInspection,
            AssetStatus::UnderRepair, AssetStatus::UnderRepair, AssetStatus::UnderRepair,
            AssetStatus::AwaitingTesting, AssetStatus::AwaitingTesting,
            AssetStatus::Completed, AssetStatus::Completed,
            AssetStatus::Released,
        ];

        $assets = collect();

        for ($i = 0; $i < 24; $i++) {
            $status = $statuses[$i % count($statuses)];
            $type = $types->random();

            $asset = Asset::factory()->create([
                'asset_type_id' => $type->id,
                'company_id' => $companies->random()->id,
                'status' => $status,
                'priority' => Arr::random(Priority::cases()),
                'released_date' => $status === AssetStatus::Released ? now()->subDays(rand(1, 10)) : null,
                'created_by' => $manager->id,
            ]);
            $asset->assignNumber();

            Activity::record($asset, 'received', "Asset {$asset->asset_number} received", [], $manager->id);
            if ($status !== AssetStatus::Received) {
                Activity::record($asset, 'status_changed', "Status changed to {$status->label()}", ['to' => $status->value], $manager->id);
            }

            $assets->push($asset);
        }

        $woStatuses = [
            WorkOrderStatus::Open, WorkOrderStatus::InProgress, WorkOrderStatus::InProgress,
            WorkOrderStatus::AwaitingTesting, WorkOrderStatus::Completed,
        ];

        foreach ($assets->take(15) as $index => $asset) {
            $status = $woStatuses[$index % count($woStatuses)];

            $workOrder = WorkOrder::factory()->create([
                'asset_id' => $asset->id,
                'status' => $status,
                'priority' => $asset->priority,
                'assigned_to' => $status === WorkOrderStatus::Open ? null : Arr::random([$tech1->id, $tech2->id]),
                'due_date' => $index % 4 === 0 ? now()->subDays(3) : now()->addDays(rand(2, 14)),
                'started_at' => $status === WorkOrderStatus::Open ? null : now()->subDays(rand(1, 5)),
                'completed_at' => $status === WorkOrderStatus::Completed ? now()->subDay() : null,
                'diagnosis' => $status === WorkOrderStatus::Open ? null : 'Worn bearings and damaged winding insulation detected.',
                'work_performed' => $status === WorkOrderStatus::Completed ? 'Replaced bearings, rewound stator, tested under load.' : null,
                'created_by' => $manager->id,
            ]);
            $workOrder->assignNumber();

            Activity::record($workOrder, 'created', "Work order {$workOrder->number} created", [], $manager->id);

            if ($index % 3 === 0) {
                $part = $parts->random();
                $quantity = rand(1, 3);
                $part->decrement('quantity', min($quantity, $part->quantity));
                PartUsage::create([
                    'part_id' => $part->id,
                    'work_order_id' => $workOrder->id,
                    'user_id' => $tech1->id,
                    'quantity' => $quantity,
                    'used_at' => now()->subDays(rand(0, 4)),
                ]);
            }

            if ($index % 4 === 0) {
                $inspection = Inspection::create([
                    'asset_id' => $asset->id,
                    'work_order_id' => $workOrder->id,
                    'inspector_id' => $tech2->id,
                    'inspection_date' => now()->subDays(rand(0, 6)),
                    'result' => Arr::random(InspectionResult::cases()),
                    'status' => 'completed',
                    'notes' => 'Visual and electrical inspection performed.',
                ]);
                $inspection->assignNumber();
            }
        }

        $reportTypes = [
            'Monthly Asset Summary',
            'Work Order Performance Report',
            'Parts Inventory Report',
            'Inspection Results Summary',
            'Overdue Work Orders Report',
            'Asset Status Distribution',
            'Technician Productivity Report',
            'Company Asset Breakdown',
        ];

        foreach ($reportTypes as $i => $title) {
            Report::create([
                'title' => $title,
                'type' => Arr::random(['summary', 'detailed', 'analytics']),
                'data' => ['generated' => true, 'items' => rand(5, 50)],
                'generated_by' => Arr::random([$admin->id, $manager->id]),
                'generated_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
