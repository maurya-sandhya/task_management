@extends('layouts.layout')

@section('content')
<div class="container">
    <h2>Task-wise Reports</h2>

    <!-- Filters -->
    <div class="row mb-3">
        <div class="col-md-4">
            <label>Task</label>
            <select id="filterTask" class="form-control">
                <option value="">All Tasks</option>
                @foreach($tasks as $task)
                    <option value="{{ $task->id }}">{{ $task->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label>Start Date</label>
            <input type="date" id="startDate" class="form-control">
        </div>
        <div class="col-md-3">
            <label>End Date</label>
            <input type="date" id="endDate" class="form-control">
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <button id="filterBtn" class="btn btn-primary w-100">Filter</button>
        </div>
        <hr>
        <div class="col-md-2">
            <label>&nbsp;</label>
            <a href="{{ route('reports.exportPDF') }}" class="btn btn-danger">Export PDF</a>
        </div>
    </div>

    <!-- Report Table -->
    <table class="table table-bordered" id="reportTable">
        <thead>
            <tr>
                <th>Task</th>
                <th>Date</th>
                <th>Total Hours</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    function fetchReports() {
        $.ajax({
            url: "{{ route('reports.data') }}",
            type: "GET",
            data: {
                task_id: $('#filterTask').val(),
                start_date: $('#startDate').val(),
                end_date: $('#endDate').val()
            },
            success: function(data) {
                let tbody = '';
                data.forEach(row => {
                    tbody += `<tr>
                        <td>${row.task.name}</td>
                        <td>${row.work_date}</td>
                        <td>${row.total_hours}</td>
                    </tr>`;
                });
                $('#reportTable tbody').html(tbody);
            }
        });
    }

    // Load reports initially
    fetchReports();

    // Apply filters
    $('#filterBtn').click(fetchReports);
});
</script>
@endpush
@endsection
