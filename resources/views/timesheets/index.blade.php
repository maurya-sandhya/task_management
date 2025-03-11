@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>Timesheet Management</h2>
    
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTimesheetModal">Add Timesheet</button>
    <hr>
    <table class="table table-bordered mt-3" id="timesheetTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Task</th>
                <th>Date</th>
                <th>Hours Worked</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
        </thead>
    </table>
</div>

<!-- Add Timesheet Modal -->
<div class="modal fade" id="addTimesheetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Timesheet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addTimesheetForm">
                    @csrf
                    <div class="mb-3">
                        <label>Task</label>
                        @php
                        $tasks = DB::table('tasks')->get();
                        @endphp
                        <select class="form-control" name="task_id">
                            @foreach($tasks as $task)
                                <option value="{{ $task->id }}">{{ $task->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date">
                    </div>
                    <div class="mb-3">
                        <label>Hours Worked</label>
                        <input type="number" class="form-control" name="hours_worked" max="24">
                    </div>
                    <div class="mb-3">
                        <label>Comments</label>
                        <textarea class="form-control" name="comments"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Timesheet Modal -->
<div class="modal fade" id="editTimesheetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Timesheet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editTimesheetForm">
                    @csrf
                    <input type="hidden" name="id" id="editTimesheetId">
                    <div class="mb-3">
                        <label>Task</label>
                        <select class="form-control" name="task_id" id="editTaskId">
                            @foreach(DB::table('tasks')->get() as $task)
                                <option value="{{ $task->id }}">{{ $task->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date" id="editDate">
                    </div>
                    <div class="mb-3">
                        <label>Hours Worked</label>
                        <input type="number" class="form-control" name="hours_worked" id="editHoursWorked" max="24">
                    </div>
                    <div class="mb-3">
                        <label>Comments</label>
                        <textarea class="form-control" name="comments" id="editComments"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>


@push('scripts')

<script>
$(document).ready(function() {
    let table = $('#timesheetTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('/timesheets') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            // { data: 'id' },
            { data: 'task_name' },
            { data: 'date' },
            { data: 'hours_worked' },
            { data: 'comments' },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    // ✅ Add Timesheet
    $('#addTimesheetForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('timesheets.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function() {
                $('#addTimesheetModal').modal('hide');
                table.ajax.reload();
            }
        });
    });

    // ✅ Edit Timesheet (Open Modal & Load Data)
    $(document).on('click', '.edit', function() {
        let id = $(this).data('id');
        $.get("/timesheets/" + id + "/edit", function(data) {
            $('#editTimesheetId').val(data.id);
            $('#editTaskId').val(data.task_id);
            $('#editDate').val(data.date);
            $('#editHoursWorked').val(data.hours_worked);
            $('#editComments').val(data.comments);
            $('#editTimesheetModal').modal('show'); // Open Edit Modal
        });
    });

    // ✅ Update Timesheet
    $('#editTimesheetForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#editTimesheetId').val(); // Get ID from hidden input
        $.ajax({
            url: "/timesheets/" + id,
            type: "POST", // Use POST since Laravel needs _method for PUT
            data: $(this).serialize() + "&_method=PUT",
            success: function() {
                $('#editTimesheetModal').modal('hide');
                table.ajax.reload();
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    // ✅ Delete Timesheet
    $(document).on('click', '.delete', function() {
        if (confirm("Are you sure?")) {
            $.ajax({
                url: "/timesheets/" + $(this).data('id'),
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function() {
                    table.ajax.reload();
                }
            });
        }
    });
});

</script>
@endpush
@endsection
