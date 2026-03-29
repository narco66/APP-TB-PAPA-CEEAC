<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 11px; }
        .header { border-bottom: 2px solid #0a2157; margin-bottom: 16px; padding-bottom: 10px; }
        .title { color: #0a2157; font-size: 20px; font-weight: bold; }
        .subtitle { color: #4b5563; font-size: 10px; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #dbe2ea; padding: 6px; vertical-align: top; }
        th { background: #eef3ff; color: #0a2157; text-align: left; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $title }}</div>
        <div class="subtitle">{{ $subtitle }}</div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headings) }}">Aucune donnee disponible pour ce rapport.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>