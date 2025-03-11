@extends('layouts.layout')

@section('title', 'Task List')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Task List</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTaskModal">Add Task</button>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="taskTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTaskForm">
                    @csrf
                    <div class="mb-3">
                        <label for="taskName" class="form-label">Task Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="taskName" name="name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="taskStatus" class="form-label">Status</label>
                        <select class="form-control" id="taskStatus" name="status">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTaskForm">
                    @csrf
                    <input type="hidden" id="editTaskId">
                    <div class="mb-3">
                        <label for="editTaskName" class="form-label">Task Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="editTaskName" name="name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="editTaskStatus" class="form-label">Status</label>
                        <select class="form-control" id="editTaskStatus" name="status">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Update Task</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')

<script>
$(document).ready(function() {
    let taskTable = $('#taskTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('/tasks') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name' },
            {
                data: 'status',
                render: function(data, type, row) {
                    return `
                        <select class="form-control status-dropdown" data-id="${row.id}">
                            <option value="Pending" ${data === 'Pending' ? 'selected' : ''}>Pending</option>
                            <option value="In Progress" ${data === 'In Progress' ? 'selected' : ''}>In Progress</option>
                            <option value="Completed" ${data === 'Completed' ? 'selected' : ''}>Completed</option>
                        </select>
                    `;
                }
            },
            { data: 'created_at', render: function(data) {
                return moment(data).format('DD-MM-YYYY'); 
            }},
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    //update status
    $(document).on('change', '.status-dropdown', function() {
        let taskId = $(this).data('id');
        let newStatus = $(this).val();
        
        $.ajax({
            url: `/tasks/${taskId}/update-status`,
            type: "PUT",
            data: {
                status: newStatus,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                toastr.success("Task status updated successfully");
                taskTable.ajax.reload(null, false);
            },
            error: function() {
                toastr.error("Failed to update status");
            }
            });
        });
    
    // Add Task
    $('#addTaskForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('tasks.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                $('#addTaskModal').modal('hide');
                $('#addTaskForm')[0].reset();
                taskTable.ajax.reload();
            }
        });
    });

    // Open Edit Task Modal
    $(document).on('click', '.edit', function() {
        let taskId = $(this).data('id');
        $.get("/tasks/" + taskId + "/edit", function(task) {
            $('#editTaskId').val(task.id);
            $('#editTaskName').val(task.name);
            $('#editTaskStatus').val(task.status);
            $('#editTaskModal').modal('show');
        });
    });

    // Update Task
    $('#editTaskForm').on('submit', function(e) {
        e.preventDefault();
        let taskId = $('#editTaskId').val();
        $.ajax({
            url: "/tasks/" + taskId,
            type: "PUT",
            data: $(this).serialize(),
            success: function(response) {
                $('#editTaskModal').modal('hide');
                taskTable.ajax.reload();
            }
        });
    });

    // Delete Task
    $(document).on('click', '.delete', function() {
        if (confirm("Are you sure you want to delete this task?")) {
            let taskId = $(this).data('id');
            $.ajax({
                url: "/tasks/" + taskId,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    taskTable.ajax.reload();
                }
            });
        }
    });
});
</script>
@endpush
@endsection
