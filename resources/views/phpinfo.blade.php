<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Info</title>
</head>
<body>
    <h1>Informasi PHP</h1>

    <h2>Waktu Timeout PHP:</h2>
    <p>Max Execution Time: {{ $maxExecutionTime }} detik</p>

    <h2>Detail Info PHP:</h2>
    <div>
        {!! $phpInfo !!}
    </div>
</body>
</html>
