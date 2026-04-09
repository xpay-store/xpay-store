<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>Profit Log</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: right; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h2>سجل الأرباح</h2>
    <div>من: {{ $from }} | إلى: {{ $to }} | التجميع: {{ $groupBy }}</div>

    <table>
        <thead>
            <tr>
                <th>الفترة</th>
                <th>عدد الطلبات</th>
                <th>USD</th>
                <th>SYP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['period'] }}</td>
                    <td>{{ $row['orders'] }}</td>
                    <td>{{ number_format($row['usd'], 2) }}</td>
                    <td>{{ number_format($row['syp'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

