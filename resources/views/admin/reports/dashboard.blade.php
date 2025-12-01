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

    {{-- RESUMEN GENERAL (Siempre visible si stats están activos) --}}
    @if($visibleSections['stats'] ?? true)
        <div class="section">
            <div class="section-title">Resumen General</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $total_jueces }}</div>
                    <div class="stat-label">Jueces Registrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $total_participantes }}</div>
                    <div class="stat-label">Participantes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $total_equipos }}</div>
                    <div class="stat-label">Equipos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $total_proyectos }}</div>
                    <div class="stat-label">Proyectos</div>
                </div>
            </div>
        </div>
    @endif

    {{-- DETALLES DE PROYECTOS --}}
    @if($visibleSections['evaluacion'] ?? true)
        <div class="section">
            <div class="section-title">
                Estado de Proyectos
                @if(isset($eventoReporte))
                    <span style="font-weight: normal; font-size: 12px; color: #666;">(Evento: {{ $eventoReporte }})</span>
                @endif
            </div>
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
                        <td>{{ $statsReporte['evaluados'] }}</td>
                        <td>{{ $statsReporte['total'] > 0 ? round(($statsReporte['evaluados'] / $statsReporte['total']) * 100, 1) : 0 }}%
                        </td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill"
                                    style="width: {{ $statsReporte['total'] > 0 ? ($statsReporte['evaluados'] / $statsReporte['total']) * 100 : 0 }}%;">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Pendientes</td>
                        <td>{{ $statsReporte['pendientes'] }}</td>
                        <td>{{ $statsReporte['total'] > 0 ? round(($statsReporte['pendientes'] / $statsReporte['total']) * 100, 1) : 0 }}%
                        </td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill"
                                    style="width: {{ $statsReporte['total'] > 0 ? ($statsReporte['pendientes'] / $statsReporte['total']) * 100 : 0 }}%; background-color: #9ca3af;">
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    {{-- PARTICIPACIÓN POR CARRERA --}}
    @if($visibleSections['carreras'] ?? true)
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
    @endif

    {{-- PRÓXIMOS EVENTOS --}}
    @if($visibleSections['eventos'] ?? true)
        <div class="section">
            <div class="section-title">Próximos Eventos</div>
            @if($eventos_activos->isEmpty())
                <p style="color: #666; font-style: italic;">No hay eventos programados próximamente.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Fecha Inicio</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($eventos_activos as $evento)
                            <tr>
                                <td>{{ $evento->nombre }}</td>
                                <td>{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d/m/Y') }}</td>
                                <td>{{ Str::limit($evento->descripcion, 50) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif

</body>

</html>