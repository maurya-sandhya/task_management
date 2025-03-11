@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Admin Dashboard</h2>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-primary text-white text-center p-3">
                <h4>Total Tasks</h4>
                <h3 id="totalTasks">{{ $totalTasks }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white text-center p-3">
                <h4>Total Hours Logged</h4>
                <h3 id="totalHours">{{ $totalHours }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark text-center p-3">
                <h4>Pending Tasks</h4>
                <h3 id="pendingTasks">{{ $pendingTasks }}</h3>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mt-4">
        <div class="col-md-6">
            <h5>Task Progress</h5>
            <canvas id="taskProgressChart"></canvas>
        </div>
        <div class="col-md-6">
            <h5>Hours Distribution per Task</h5>
            <canvas id="hoursDistributionChart"></canvas>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <h5>Daily Activity (Hours Logged Over Time)</h5>
            <canvas id="dailyActivityChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Task Progress Chart (Pie Chart)
    let taskProgressCtx = document.getElementById('taskProgressChart').getContext('2d');
    new Chart(taskProgressCtx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'In Progress', 'Completed'],
            datasets: [{
                data: {!! json_encode($taskStatusData) !!},
                backgroundColor: ['#FFCE56', '#36A2EB', '#4CAF50']
            }]
        }
    });

    // Hours Distribution Chart (Bar Chart)
    let hoursDistributionCtx = document.getElementById('hoursDistributionChart').getContext('2d');
    new Chart(hoursDistributionCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($taskNames) !!},
            datasets: [{
                label: 'Hours Worked',
                data: {!! json_encode($hoursWorkedData) !!},
                backgroundColor: '#36A2EB'
            }]
        }
    });

});
</script>
@endpush
@endsection
