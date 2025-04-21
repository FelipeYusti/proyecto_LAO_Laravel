<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        body {
            font-family: 'Helvetica Neue', sans-serif;
            color: #333;
            margin: 20px;
        }

        .header {
            color: black;
            padding: 20px;
            text-align: center;
        }

        .header img {
            max-width: 150px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 12px;
        }

        th {
            background-color: #34495e;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        h1,
        h2 {
            margin-bottom: 30px;
        }

        h2 {
            text-align: center;
            margin-top: 70px;
        }

        footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('storage/logo/logolAO-sinFondo.png') }}" alt="Logo de la Empresa" class="logo">
        <h1 class="title">Informe de Ventas - {{ strtoupper($informe['mes'])  }}</h1>
    </div>
    <table>
        <thead>
            <tr>
                <th>Mes</th>
                <th>C.Ventas</th>
                <th>C.Productos</th>
                <th style="text-align: center;">Total Recibido</th>
                <th style="text-align: center;">Total Esperado</th>
                <th style="text-align: center;">Diferencia</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $informe['mes'] }}</td>
                <td>{{ $informe['cantidadVenta']}} </td>
                <td>{{ $informe ['cantidadProductos']}} </td>
                <td style="text-align: center;">${{ number_format($informe['total'], 0) }}</td>
                <td style="text-align: center;">${{ number_format($informe['totalRecibido'], 0) }}</td>
                <td style="text-align: center;">${{ number_format($informe['diferencia'], 0) }}</td>
            </tr>
        </tbody>
    </table>
    <H2>Productos Vendidos</H2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th style="text-align: center;">P. Untario</th>
                <th style="text-align: center;">Total Sin Descuentos</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $producto)
            <tr>
                <td>{{ $producto['nombreProducto']}} </td>
                <td>{{ $producto ['cantidad']}} </td>
                <td style="text-align: center;">${{ number_format($producto['precioUnitario'], 0) }}</td>
                <td style="text-align: center;">${{ number_format($producto['total'], 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2 class="title">Gráfico de Productos</h2>
    <img src="{{ $grafica }}" alt="Gráfico de productos" style="width: 100%; max-width: 700px; margin-top: 20px;">

    <footer>
        Reporte generado automáticamente el {{ \Carbon\Carbon::now()->format('d/m/Y') }}.
    </footer>
</body>

</html>