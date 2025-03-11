<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Task-wise Report</h2>
    <table>
        <thead>
            <tr>
                <th>Task</th>
                <th>Date</th>
                <th>Total Hours</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
            <tr>
                <td>{{ $row->task->name ?? 'N/A' }}</td>
                <td>{{ $row->work_date }}</td>
                <td>{{ $row->total_hours }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
