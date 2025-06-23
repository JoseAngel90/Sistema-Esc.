<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acta de Calificaciones</title>
    <style>
        @page {
    size: landscape;
    margin: 10mm; /* Reducir los márgenes de la página */
}

body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
    color: #1e1e1e;
}

.container {
    width: 100%;
    max-width: 900px;
    margin: 10px auto; /* Reducir el margen superior e inferior */
    background-color: #ffffff;
    padding: 20px; /* Reducir el padding interior */
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

h1, h4 {
    text-align: center;
    color: #2c3e50;
}

h1 {
    font-size: 22px; /* Reducir el tamaño de la fuente */
    margin-bottom: 5px;
}

h4 {
    font-size: 14px; /* Reducir el tamaño de la fuente */
    margin-top: 0;
    font-weight: normal;
    color: #34495e;
    line-height: 1.6;
}

.teacher-info, .grade-info {
    margin-bottom: 15px; /* Reducir los márgenes */
    font-size: 13px; /* Reducir el tamaño de la fuente */
}

.teacher-info p, .grade-info p {
    margin: 4px 0; /* Reducir los márgenes en los párrafos */
}

h3 {
    font-size: 15px; /* Reducir el tamaño de la fuente */
    color: #2c3e50;
    margin-top: 20px; /* Reducir el margen superior */
    border-bottom: 1px solid #ccc;
    padding-bottom: 4px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px; /* Reducir el margen superior */
    font-size: 12px; /* Reducir el tamaño de la fuente */
}

th, td {
    padding: 8px; /* Reducir el padding de las celdas */
    text-align: center;
    border: 1px solid #ccc;
}

th {
    background-color: #2c3e50;
    color: white;
    font-weight: bold;
}

td {
    background-color: #fdfdfd;
}

.footer {
    text-align: center;
    margin-top: 20px; /* Reducir el margen superior */
    font-size: 12px;
    color: #777;
}

    </style>

</head>
<body>
<div class="container">
    <h1>Acta de Calificaciones</h1>
    <h4>
        Grado: {{ $gradoFiltro ?? 'Todos' }} | Grupo: {{ $grupoFiltro ?? 'Todos' }} <br>
        <br>
    </h4>

        <table class="grade-info" style="width:100%; margin-bottom:15px; font-size:13px;">
        <tr>
            <td style="vertical-align:top; width:50%;">
                <p><strong>Lugar o Localidad:</strong> {{ $datosGenerales->localidad ?? '' }}</p>
                <p><strong>Ciclo Escolar:</strong> {{ $datosGenerales->ciclo_escolar ?? '' }}</p>
                <p><strong>Nombre y Clave de la Escuela:</strong> {{ $datosGenerales->nombre_y_clave ?? '' }}</p>
                <p><strong>Turno:</strong> {{ $datosGenerales->turno ?? '' }}</p>
            </td>
            <td style="vertical-align:top; width:50%;">
                <p><strong>Asignatura:</strong> {{ $datosGenerales->asignatura ?? '' }}</p>
                <p><strong>Profesor:</strong> {{ $datosGenerales->nombre_profesor ?? '' }}</p>
                <p><strong>Periodo:</strong> {{ $datosGenerales->periodo ?? '' }}</p>
                <p><strong>Clave CT:</strong> {{ $datosGenerales->clave_ct ?? '' }}</p>
            </td>
        </tr>
    </table>
    @foreach ($tipos as $index => $tipo)
       <h3>
        Criterio {{ $loop->index + 1 }}
        @if(!empty($etiquetasRubros[$tipo]))
            : {{ $etiquetasRubros[$tipo] }}
        @endif
        @if(array_key_exists($tipo, $porcentaje))
            {{ $porcentaje[$tipo] !== null ? $porcentaje[$tipo] : 0 }}%
        @endif
    </h3>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Alumno</th>
                    @for ($i = 1; $i <= ($tipo == "apoyo_p" ? 5 : 3); $i++)
                        <th>
                            {{ $aspectos[$i][$tipo] ?? 'Aspecto ' . $i }}
                        </th>
                    @endfor
                    <th>Calificación Final</th>
                    <th>Total (Entero)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumnos as $alumno)
                    @php
                        $calificaciones = $calificacionesPorPestania[$tipo][$alumno->id] ?? null;
                        $total = $calificaciones->Total ?? 0;
                        $totalEntero = (int)$total;
                    @endphp
                    <tr>
                        <td>{{ $alumno->nombre_alumno }}</td>
                        @for ($i = 1; $i <= ($tipo == "apoyo_p" ? 5 : 3); $i++)
                            @php
                                $campoEvaluacion = 'evaluacion_' . $i;
                            @endphp
                            <td>{{ $calificaciones->$campoEvaluacion ?? '-' }}</td>
                        @endfor
                        <td>{{ $calificaciones->Total ?? '-' }}</td>
                        <td>{{ $totalEntero }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <h3>Promedio de Calificaciones por Alumno</h3>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Alumno</th>
                <th>Promedio (Decimal)</th> <!-- Promedio decimal -->
                <th>Promedio (Entero)</th> <!-- Promedio entero -->
                <th>Rango</th> <!-- Nuevo: Texto de clasificación -->
            </tr>
        </thead>
        <tbody>
        @foreach ($promedios as $promedio)
    @php
        $rango = '';
        $imagen_base64 = ''; // Variable para la imagen en base64

        // Determinamos el rango y la imagen
        if ($promedio['promedio'] == 0) {
            $rango = 'No aplica';
            $imagen_path = null;
        } elseif ($promedio['promedio'] >= 0 && $promedio['promedio'] <= 59.9) {
            $rango = 'Aprendiz';
            $imagen_path = public_path('rangos/aprendiz.png');
        } elseif ($promedio['promedio'] >= 60 && $promedio['promedio'] <= 79.9) {
            $rango = 'Estudiante constante';
            $imagen_path = public_path('rangos/constante.png');
        } elseif ($promedio['promedio'] >= 80 && $promedio['promedio'] <= 95.9) {
            $rango = 'Alumno destacado';
            $imagen_path = public_path('rangos/destacado.png');
        } elseif ($promedio['promedio'] >= 96 && $promedio['promedio'] <= 100) {
            $rango = 'Honor académico';
            $imagen_path = public_path('rangos/honor.png');
        } else {
            $rango = 'Sin Clasificación';
            $imagen_path = null;
        }

        // Convertimos la imagen a base64 si existe
        if ($imagen_path && file_exists($imagen_path)) {
            $imagen_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($imagen_path));
        }
    @endphp

    <tr>
        <td>{{ $promedio['nombre'] }}</td>
        <td>{{ $promedio['promedio'] }}</td>
        <td>{{ $promedio['promedio_entero'] }}</td>
        <td>
            @if ($imagen_base64)
                <div>{{ $rango }}</div> <!-- Rango encima de la imagen -->
                <img src="{{ $imagen_base64 }}" alt="{{ $rango }}" width="50" height="50">
            @else
                <span>No Disponible</span>
            @endif
        </td>
    </tr>
@endforeach



        </tbody>
    </table>

    

    <div class="footer">
        <p>Sistema de Gestión Escolar</p>
    </div>
</div>
</body>


</body>


</html>
