<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Task Updated</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .header {
            border-bottom: 2px solid #007bff;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .header h2 {
            color: #007bff;
            margin: 0;
        }

        .content p {
            line-height: 1.6;
            margin: 10px 0;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            background-color: #17a2b8;
            color: #fff;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h2> Task Updated</h2>
        </div>

        <div class="content">
            <p><strong>Task Name:</strong> {{ $task->name }}</p>
            <p><strong>Project:</strong> {{ $task->project->name ?? '—' }}</p>
            <p><strong>Updated By:</strong> {{ $updatedBy->name }}</p>
            <p><strong>Due Date:</strong> {{ $task->due_date ?? 'N/A' }}</p>
            <p><strong>Description:</strong></p>
            <p>{{ $task->description ?? 'No description provided.' }}</p>

            <p><strong>Status:</strong>
                <span class="badge">{{ ucfirst($task->status ?? 'Unknown') }}</span>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated message from {{ config('app.name') }}.</p>
        </div>
    </div>
</body>

</html>