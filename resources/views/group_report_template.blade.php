<!DOCTYPE html>
<html>
<head>
    <title>Group Report</title>
    <style>
        body { font-family: 'Arial', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Report for {{ $data['name'] ?? 'N/A' }}</h1>
    <p>Group Admin: {{ $data['group_admin'] ?? 'N/A' }}</p>
    <p>Created At: {{ $data['created_at'] ?? 'N/A' }}</p>

    <h2>Group Events</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>event</th>
            </tr>
        </thead>
        <tbody>
            @if (!empty($data['logs']))
                @foreach ($data['logs'] as $log)
                    <tr>
                        <td>{{ $log['date'] ?? 'N/A' }}</td>
                        <td>{{ $log['message'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3">No logs available</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
