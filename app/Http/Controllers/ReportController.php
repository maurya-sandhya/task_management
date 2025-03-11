<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Timesheet;
use App\Models\Task;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $tasks = Task::all(); // Get all tasks for filter dropdown
        return view('reports.index', compact('tasks'));
    }

    public function getData(Request $request)
    {
        // dd($request->all());
        // DB::enableQueryLog();
        $query = Timesheet::select(
            'task_id',
            DB::raw('SUM(hours_worked) as total_hours'),
            DB::raw('DATE(date) as work_date')
        )->groupBy('task_id', 'work_date');

        // Apply filters if selected
        if ($request->has('task_id') && $request->task_id != '') {
            $query->where('task_id', $request->task_id);
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        

        $data = $query->with('task')->get();
        return response()->json($data);
    }
    public function exportPDF(Request $request)
    {
        $query = Timesheet::select(
            'task_id',
            DB::raw('SUM(hours_worked) as total_hours'),
            DB::raw('DATE(date) as work_date')
        )->groupBy('task_id', 'work_date')->with('task');

        // Apply filters
        if ($request->has('task_id') && $request->task_id != '') {
            $query->where('task_id', $request->task_id);
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $data = $query->get();
        $pdf = Pdf::loadView('reports.pdf', compact('data'));
        return $pdf->download('task-report.pdf');
    }
}
