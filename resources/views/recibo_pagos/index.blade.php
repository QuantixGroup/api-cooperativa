<!DOCTYPE html>
<html>
<head>
    <title>Recibos de Pago</title>
</head>
<body>
    <h1>Recibos de Pago</h1>
   <table style="border: 1px solid black; border-collapse: collapse;">
        <tr>
            <th>ID</th>
            <th>Usuario ID</th>
            <th>Monto</th>
            <th>Fecha de Pago</th>
            <th>Estado</th>
        </tr>
        @foreach($recibos as $recibo)
        <tr>
            <td>{{ $recibo->id }}</td>
            <td>{{ $recibo->usuario_id }}</td>
            <td>{{ $recibo->monto }}</td>
            <td>{{ $recibo->fecha_pago }}</td>
            <td>{{ $recibo->estado }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>
