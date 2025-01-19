<!DOCTYPE html>
<html>
<head>
    <title>File Report</title>
    <style>
        body { font-family: 'Arial', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Report for {{ $data['name'] ?? 'N/A' }}</h1>
    <p>Status: {{ $data['status'] ?? 'N/A' }}</p>
    <p>Uploaded by: {{ $data['userName'] ?? 'N/A' }}</p>
    <p>Group: {{ $data['groupName'] ?? 'N/A' }}</p>

    <h2>File Operations</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Operation</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            @if (!empty($data['fileLogs']))
                @foreach ($data['fileLogs'] as $log)
                    <tr>
                        <td>{{ $log['date'] ?? 'N/A' }}</td>
                        <td>{{ $log['operation'] ?? 'N/A' }}</td>
                        <td>{{ $log['user']['name'] ?? 'N/A' }}</td>
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
