<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DatosGeneralesController;
use App\Http\Controllers\PaseListaController;
use App\Http\Controllers\EvaluacionesController;
use App\Models\Alumno;  // Asegúrate de importar la clase Alumno
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\CalificarCotejoController;
use App\Http\Controllers\ActasController;
use App\Http\Controllers\AdministradorController;


/*
|-----------------------------------------------------------------------
| Web Routes
|-----------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación
Auth::routes();

// Agrupar todas las rutas que requieren autenticación
Route::middleware(['auth'])->group(function () {
    // Ruta para el inicio después de iniciar sesión
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Rutas para el CRUD de alumnos
    Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
    Route::get('/registrar-alumno/{alumnoEdit?}', [AlumnoController::class, 'create'])->name('alumnos.create');
    Route::post('/registrar-alumno', [AlumnoController::class, 'store'])->name('alumnos.store');
    Route::put('/registrar-alumno/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
    Route::delete('/registrar-alumno/{alumno}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');

    // Rutas para el CRUD de datos generales
    Route::get('/home', [DatosGeneralesController::class, 'create'])->name('home'); // Solo la ruta GET para mostrar los datos
    Route::post('/home', [DatosGeneralesController::class, 'store'])->name('datosGenerales.store'); // Ruta POST para guardar los datos
    Route::put('/home', [DatosGeneralesController::class, 'update'])->name('datosGenerales.update'); // Ruta PUT para actualizar los datos

    // Ruta para el pase de lista
    Route::get('/pase-de-lista', [PaseListaController::class, 'index'])->name('pase.lista');
    Route::post('/pase-de-lista', [PaseListaController::class, 'store'])->name('pase.lista.store');

    // Ruta para las evaluaciones
    
    Route::get('/Evaluaciones', [EvaluacionesController::class, 'index'])->name('evaluacion');
    Route::post('/guardar-calificaciones', [EvaluacionesController::class, 'guardarCalificaciones'])->name('guardar.calificaciones');
    Route::post('/guardar-periodos', [EvaluacionesController::class, 'guardarPeriodos'])->name('guardar.periodos');

    // Ruta para manejar grado y grupo
    Route::post('/grupo', [GrupoController::class, 'store'])->name('grupo.store');
    Route::delete('/grupos/{id}', [GrupoController::class, 'destroy'])->name('grupos.destroy');

    // Panel
    Route::get('/panel/{grado}/{grupo}', [PanelController::class, 'showPanel'])->name('panel');
    

    // Ruta para el examen diagnóstico
    Route::get('/diagnostico/{grado}/{grupo}', [DiagnosticoController::class, 'index'])->name('diagnostico');
    Route::get('/diagnostico', [DiagnosticoController::class, 'mostrarDiagnostico'])->name('diagnostico.mostrar');
    Route::post('/guardar-reactivos', [DiagnosticoController::class, 'guardarReactivos'])->name('guardar.reactivos');


    //Ruta para las evaluaciones
    Route::get('/calificar-cotejo', [CalificarCotejoController::class, 'index'])->name('CalificarCotejo');
    Route::post('/guardar-calificaciones', [CalificacionController::class, 'guardarCalificaciones'])->name('guardarCalificaciones');
    Route::get('/get-grupos', [EvaluacionesController::class, 'getGruposByGrado'])->name('getGrupos');

    //Ruta para guardar calificaicones

    //Route::post('/ruta-de-guardar-ponderaciones', [CalificarCotejoController::class, 'guardarPonderaciones']);
    Route::post('/guardar-ponderaciones', [CalificarCotejoController::class, 'guardarRubro'])->name('guardar.rubro');



    // routes/web.php
    Route::post('/calificaciones/guardar', [CalificarCotejoController::class, 'guardarCalificacion'])->name('calificaciones.guardar');

    Route::get('/calificaciones', [CalificarCotejoController::class, 'index'])->name('calificaciones.index');
    Route::post('/guardar-calificacion', [CalificacionesController::class, 'guardar'])->name('guardar.calificacion');

    // Ruta para generar el PDF
    Route::get('/descargar-acta', [ActasController::class, 'descargarActa'])->name('descargar.acta');
    Route::post('/cerrar-acta', [ActasController::class, 'cerrarActa'])->name('cerrar.acta');

    // Ruta para guardar valores maximos
    Route::post('/guardar-elementos-correctos', [CalificarCotejoController::class, 'guardarElementosCorrectos'])->name('guardar.elementos.correctos');

    //Ruta para el administrador
    Route::get('/administrador', [AdministradorController::class, 'index'])->name('administrador');
    Route::delete('/administrador/{id}', [AdministradorController::class, 'destroy'])->name('administrador.destroy');
    Route::delete('/users/{id}', [AdministradorController::class, 'destroy'])->name('users.destroy');
    Route::post('/administrador/approve/{id}', [AdministradorController::class, 'approve'])->name('users.approve');

    // Ruta para recuperar contraseña
    Route::post('/users/reset-password/{id}', [AdministradorController::class, 'resetPassword'])->name('users.resetPassword');
});

    Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/administrador', [AdministradorController::class, 'index'])->name('administrador');
    Route::post('/administradores', [AdministradorController::class, 'store'])->name('administradores.store');
    Route::delete('/users/reject/{id}', [AdministradorController::class, 'reject'])->name('users.reject');
    

});