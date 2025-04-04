<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .logo {
            max-width: 120px;
        }

        .title {
            flex-grow: 1;
            text-align: center;
            margin: 0;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color:rgb(13, 13, 14);
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total {
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('storage/logo/logolAO.jpg') }}" alt="Logo de la Empresa" class="logo">
        <h2 class="title">Reporte de Ventas</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Resumen de Compra</th>
                <th>Fecha</th>
                <th style="text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $venta)
            <tr>
                <td>{{ $venta->cliente->nombre }}</td>
                <td>{{ $venta->productos_info}} </td>
                <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y')}}</td>
                <td style="text-align: center;">${{ number_format($venta->total, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>