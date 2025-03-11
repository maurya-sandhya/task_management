<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Timesheet;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Task statistics
        $totalTasks = Task::count();
        $pendingTasks = Task::where('status', 'Pending')->count();
        $totalHours = Timesheet::sum('hours_worked');

        // Task Progress Data (Pie Chart)
        $taskStatusData = [
            Task::where('status', 'Pending')->count(),
            Task::where('status', 'In Progress')->count(),
            Task::where('status', 'Completed')->count()
        ];

        // Hours Distribution per Task (Bar Chart)
        $taskHours = Timesheet::select('task_id', DB::raw('SUM(hours_worked) as total_hours'))
            ->groupBy('task_id')
            ->with('task')
            ->get();

        $taskNames = $taskHours->map(fn($item) => $item->task->name);
        $hoursWorkedData = $taskHours->map(fn($item) => $item->total_hours);

        // Daily Activity (Line Chart)
        $dailyHours = Timesheet::select(DB::raw('DATE(date) as work_date'), DB::raw('SUM(hours_worked) as total_hours'))
            ->groupBy('work_date')
            ->orderBy('work_date', 'ASC')
            ->get();

        $dates = $dailyHours->pluck('work_date');
        $dailyHoursData = $dailyHours->pluck('total_hours');

        return view('dashboard.dashboard', compact(
            'totalTasks',
            'pendingTasks',
            'totalHours',
            'taskStatusData',
            'taskNames',
            'hoursWorkedData',
            'dates',
            'dailyHoursData'
        ));
    }
}

