<?php

namespace App\Http\Controllers;

use App\Enums\AssetStatus;
use App\Models\Activity;
use App\Models\Asset;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return redirect()->route('filament.admin.pages.dashboard');
    }
}
