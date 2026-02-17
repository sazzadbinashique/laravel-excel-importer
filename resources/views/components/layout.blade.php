<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Excel Importer' }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7f9; margin: 0; padding: 0; color: #111827; }
        .excel-importer-wrap { max-width: 1100px; margin: 32px auto; padding: 24px; }
        .ei-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06); padding: 24px; }
        .ei-title { font-size: 24px; font-weight: 700; margin: 0 0 6px; }
        .ei-subtitle { color: #6b7280; margin: 0 0 18px; }
        .ei-row { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        .ei-button { background: #2563eb; color: #fff; border: 0; padding: 10px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; }
        .ei-button.secondary { background: #f3f4f6; color: #111827; border: 1px solid #e5e7eb; }
        .ei-button.danger { background: #dc2626; }
        .ei-input, .ei-select { padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; width: 100%; }
        .ei-label { font-size: 13px; font-weight: 600; color: #374151; display: block; margin-bottom: 6px; }
        .ei-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
        .ei-badge { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #eef2ff; color: #4338ca; font-size: 12px; font-weight: 600; }
        .ei-table { width: 100%; border-collapse: collapse; }
        .ei-table th, .ei-table td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; font-size: 14px; }
        .ei-table th { background: #f9fafb; font-weight: 600; color: #374151; }
        .ei-progress { background: #e5e7eb; border-radius: 999px; height: 10px; overflow: hidden; }
        .ei-progress > div { background: #2563eb; height: 10px; }
        .ei-muted { color: #6b7280; font-size: 13px; }
        .ei-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 10px 12px; border-radius: 8px; }
        .ei-success { background: #ecfdf3; border: 1px solid #bbf7d0; color: #166534; padding: 10px 12px; border-radius: 8px; }
        .ei-section { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="excel-importer-wrap">
        {{ $slot }}
    </div>
</body>
</html>
