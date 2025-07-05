<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Task Created</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f8fa;
            color: #333;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        h1 {
            color: #2c3e50;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1> New Task Created</h1>

        <p><span class="label">Task Name:</span> {{ $task->name }}</p>
        <p><span class="label">Project:</span> {{ $task->project->name }}</p>
        <p><span class="label">Created By:</span> {{ $createdBy->name }}</p>
        <p><span class="label">Due Date:</span> {{ \Carbon\Carbon::parse($task->due_date)->format('F j, Y') }}</p>
        <p><span class="label">Description:</span> {{ $task->description ?? 'No description provided.' }}</p>
        <p><strong>Status:</strong>
            <span class="label">{{ ucfirst($task->status ?? 'Unknown') }}</span>
        </p>

        <div class="footer">
            This is an automated message from {{ config('app.name') }}.
        </div>
    </div>
</body>

</html>