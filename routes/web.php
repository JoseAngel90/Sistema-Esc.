<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DatosGeneralesController;
use App\Http\Controllers\paseListaController;
use App\Http\Controllers\evaluacionesController;
use App\Models\Alumno;  // Asegúrate de importar la clase Alumno
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PanelController;


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

// Ruta para el inicio después de iniciar sesión
Route::get('/home', [HomeController::class, 'index'])->name('home');


// Rutas para el CRUD de alumnos
// Ruta para registrar un alumno con grado y grupo seleccionados

Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');
Route::get('/registrar-alumno/{alumnoEdit?}', [AlumnoController::class, 'create'])->name('alumnos.create');
Route::post('/registrar-alumno', [AlumnoController::class, 'store'])->name('alumnos.store');
Route::put('/registrar-alumno/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
Route::delete('/registrar-alumno/{alumno}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');


// Rutas para el CRUD de datos generales
// Ruta para mostrar el formulario de datos generales o los datos ya registrados
// Ruta para mostrar y guardar los datos (GET y POST)
Route::get('/home', [DatosGeneralesController::class, 'create'])->name('home'); // Solo la ruta GET para mostrar los datos
Route::post('/home', [DatosGeneralesController::class, 'store'])->name('datosGenerales.store'); // Ruta POST para guardar los datos
Route::put('/home', [DatosGeneralesController::class, 'update'])->name('datosGenerales.update'); // Ruta PUT para actualizar los datos



//ruta para el pase de lista
Route::get('/pase-de-lista', [PaseListaController::class, 'index'])->name('pase.lista');
Route::post('/pase-de-lista', [PaseListaController::class, 'store'])->name('pase.lista.store');

//ruta para las evaluaciones
Route::get('/Evaluaciones', [evaluacionesController::class, 'index'])->name('evaluacion');
Route::post('/guardar-calificaciones', [EvaluacionesController::class, 'guardarCalificaciones'])->name('guardar.calificaciones');


//Ruta para manejar grado y grupo
Route::post('/grupo', [GrupoController::class, 'store'])->name('grupo.store');
Route::delete('/grupos/{id}', [GrupoController::class, 'destroy'])->name('grupos.destroy');




//Panel
Route::get('/panel/{grado}/{grupo}', function ($grado, $grupo) { 
    return view('panel', compact('grado', 'grupo'));
})->name('panel');
Route::get('panel/{grado}/{grupo}', [PanelController::class, 'showPanel'])->name('panel');










