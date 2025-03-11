<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timesheet;
use DataTables;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $timesheets = Timesheet::with('task:id,name')->select(['id', 'task_id', 'date', 'hours_worked', 'comments']);
            return DataTables::of($timesheets)
                ->addIndexColumn()
                ->addColumn('task_name', fn($t) => $t->task->name)
                ->addColumn('action', fn($t) => '<button class="btn btn-sm btn-warning edit" data-id="'.$t->id.'">Edit</button>
                                                 <button class="btn btn-sm btn-danger delete" data-id="'.$t->id.'">Delete</button>')
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('timesheets.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date',
            'hours_worked' => 'required|numeric|min:0.5|max:24',
            'comments' => 'nullable|string',
        ]);

        Timesheet::create($request->all());
        return response()->json(['message' => 'Timesheet entry added successfully']);
    }

    public function edit($id)
    {
        return response()->json(Timesheet::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date',
            'hours_worked' => 'required|numeric|min:0.5|max:24',
            'comments' => 'nullable|string',
        ]);

        Timesheet::findOrFail($id)->update($request->all());
        return response()->json(['message' => 'Timesheet entry updated successfully']);
    }

    public function destroy($id)
    {
        Timesheet::destroy($id);
        return response()->json(['message' => 'Timesheet entry deleted successfully']);
    }
}
