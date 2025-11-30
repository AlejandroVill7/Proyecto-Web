<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Dashboard</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #4f46e5;
            font-size: 24px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #666;
        }

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .stats-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .stats-grid td {
            width: 25%;
            padding: 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .table th,
        .table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        .progress-bar {
            background-color: #e5e7eb;
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
            width: 100px;
            display: inline-block;
            vertical-align: middle;
        }

        .progress-fill {
            height: 100%;
            background-color: #4f46e5;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Reporte General de Actividad</h1>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }} | Usuario: {{ Auth::user()->name }}</p>
    </div>

    {{-- RESUMEN GENERAL --}}
    <div class="section">
        <div class="section-title">Resumen General</div>
        <table class="stats-grid">
            <tr>
                <td>
                    <div class="stat-value">{{ $total_jueces + $total_participantes }}</div>
                    <div class="stat-label">Usuarios Totales</div>
                </td>
                <td>
                    <div class="stat-value">{{ $total_equipos }}</div>
                    <div class="stat-label">Equipos</div>
                </td>
                <td>
                    <div class="stat-value">{{ $total_proyectos }}</div>
                    <div class="stat-label">Proyectos</div>
                </td>
                <td>
                    <div class="stat-value">{{ $eventos_activos->count() }}</div>
                    <div class="stat-label">Eventos Activos</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- DETALLES DE PROYECTOS --}}
    <div class="section">
        <div class="section-title">Estado de Proyectos</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>Cantidad</th>
                    <th>Porcentaje</th>
                    <th>Visualización</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Evaluados</td>
                    <td>{{ $proyectosEvaluados }}</td>
                    <td>{{ $total_proyectos > 0 ? round(($proyectosEvaluados / $total_proyectos) * 100, 1) : 0 }}%</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill"
                                style="width: {{ $total_proyectos > 0 ? ($proyectosEvaluados / $total_proyectos) * 100 : 0 }}%;">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Pendientes</td>
                    <td>{{ $proyectosPendientes }}</td>
                    <td>{{ $total_proyectos > 0 ? round(($proyectosPendientes / $total_proyectos) * 100, 1) : 0 }}%</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill"
                                style="width: {{ $total_proyectos > 0 ? ($proyectosPendientes / $total_proyectos) * 100 : 0 }}%; background-color: #9ca3af;">
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- PARTICIPACIÓN POR CARRERA --}}
    <div class="section">
        <div class="section-title">Participación por Carrera</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Carrera</th>
                    <th>Participantes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participantesPorCarrera as $carrera => $total)
                    <tr>
                        <td>{{ $carrera }}</td>
                        <td>{{ $total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- PRÓXIMOS EVENTOS --}}
    <div class="section">
        <div class="section-title">Próximos Eventos</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha Inicio</th>
                    <th>Evento</th>
                    <th>Descripción</th>
                    <th>Fecha Fin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eventos_activos as $evento)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d/m/Y') }}</td>
                        <td>{{ $evento->nombre }}</td>
                        <td>{{ Str::limit($evento->descripcion, 50) }}</td>
                        <td>{{ \Carbon\Carbon::parse($evento->fecha_fin)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No hay eventos próximos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>

</html>