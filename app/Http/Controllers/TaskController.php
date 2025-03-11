<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tasks = Task::select(['id', 'name', 'status', 'created_at']);
            return DataTables::of($tasks)
                ->addIndexColumn()
                ->editColumn('created_at', function ($task) {
                    return Carbon::parse($task->created_at)->format('d M Y');
                })
                ->addColumn('action', function ($task) {
                    return '<button class="btn btn-sm btn-primary edit" data-id="' . $task->id . '">Edit</button>
                            <button class="btn btn-sm btn-danger delete" data-id="' . $task->id . '">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('tasks.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        Task::create($request->all());
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        return response()->json(Task::find($id));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        Task::find($id)->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Task::find($id)->delete();
        return response()->json(['success' => true]);
    }
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate([
            'status' => 'required|in:Pending,In Progress,Completed'
        ]);

        $task->update(['status' => $request->status]);

        return response()->json(['message' => 'Task status updated successfully']);
    }

}
