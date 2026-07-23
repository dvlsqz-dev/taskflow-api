<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .description { color: #555; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background-color: #f3f4f6; }
        .status-done { color: #16a34a; font-weight: bold; }
        .status-in_progress { color: #ca8a04; font-weight: bold; }
        .status-todo { color: #6b7280; }
    </style>
</head>
<body>
    <h1>{{ $project->name }}</h1>
    <p class="description">{{ $project->description }}</p>

    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Fecha límite</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->description }}</td>
                    <td class="status-{{ $task->status }}">{{ $task->status }}</td>
                    <td>{{ $task->date_limit }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Este proyecto no tiene tareas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>