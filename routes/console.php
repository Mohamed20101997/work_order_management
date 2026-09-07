<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:prune-failed')->weekly();
