<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Presença</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>

    <h2>Relatório de Presença</h2>
    <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    <p><strong>Gerado por:</strong> {{ $user->name }}</p>

    <table>
        <thead>
            <tr>
                <th>Matrícula</th>
                <th>Nome</th>
                <th>Data</th>
                <th>Entrada Manhã</th>
                <th>Saída Manhã</th>
                <th>Entrada Tarde</th>
                <th>Saída Tarde</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendanceRecords as $record)
                <tr>
                <td>{{ $record->user_id }}</td>
                <td>{{ $record->name }}</td>
                <td>{{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }}</td>

                <td>
                    @if(is_null($record->morning_clock_in))
                        Pendente
                    @else
                        {{ $record->morning_clock_in }}
                    @endif
                </td>

                <td>
                    @if(is_null($record->morning_clock_out))
                        Pendente
                    @else
                        {{ $record->morning_clock_out }}
                    @endif
                </td>

                <td>
                    @if(is_null($record->afternoon_clock_in))
                        Pendente
                    @else
                        {{ $record->afternoon_clock_in }}
                    @endif
                </td>

                <td>
                    @if(is_null($record->afternoon_clock_out))
                        Pendente
                    @else
                        {{ $record->afternoon_clock_out }}
                    @endif
                </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
