<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ranking de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background-color: #fff;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007BFF;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .empty {
            text-align: center;
            color: #666;
            padding: 1rem;
        }
    </style>
</head>
<body>
    <h1>Ranking de Usuarios</h1>


@if($ranking->isEmpty())
    <p class="empty">No hay usuarios en la base de datos.</p>
@else
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Racha Máxima</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ranking as $ranking)
                <tr>
                    <td>{{ $ranking->nombre }}</td>
                    <td>{{ $ranking->email }}</td>
                    <td>{{ $ranking->racha_maxima }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<a href="http://lingo.local/lingo">
    <button>Volver</button>
</a>



</body>
</html>
