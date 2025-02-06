<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th{
            padding: 8px;
            text-align: left;
            font-size: 11px
        }
        td {
            padding: 6px;
            text-align: left;
            font-size: 9px
        }
    </style>
</head>
<body>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ITEM DESIGN</th>
                <th>TIPE</th>
                <th>URAIAN DESIGN</th>
                <th>SATUAN</th>
                <th>MATERIAL</th>
                <th>JASA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <td>{{ $item['No'] }}</td>
                <td>{{ $item['Nama'] }}</td>
                <td>{{ $item['Tipe'] }}</td>
                <td>{{ $item['Uraian'] }}</td>
                <td>{{ $item['Unit'] }}</td>
                <td>{{ $item['Harga Material'] }}</td>
                <td>{{ $item['Harga Jasa'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
