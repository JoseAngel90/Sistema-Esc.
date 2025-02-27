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

// Ruta para mostrar el número de alumnos
Route::get('/numero-alumnos', function () {
    return Alumno::count();  // Devuelve el número de alumnos en la base de datos
});

// Rutas para el CRUD de alumnos
Route::get('/registrar-alumno/{alumnoEdit?}', [AlumnoController::class, 'create'])->name('alumnos.create');
Route::post('/registrar-alumno', [AlumnoController::class, 'store'])->name('alumnos.store');
Route::put('/registrar-alumno/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
Route::delete('/registrar-alumno/{alumno}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');

// Rutas para el CRUD de datos generales
// Ruta para mostrar el formulario de datos generales o los datos ya registrados
// Ruta para mostrar y guardar los datos (GET y POST)
Route::match(['get', 'post'], '/home', [DatosGeneralesController::class, 'create'])->name('home');
Route::post('/home', [DatosGeneralesController::class, 'store'])->name('datosGenerales.store');
Route::get('/home', [DatosGeneralesController::class, 'create'])->name('home');
Route::post('/home', [DatosGeneralesController::class, 'store'])->name('datosGenerales.store');
Route::put('/home', [DatosGeneralesController::class, 'update'])->name('datosGenerales.update');


//ruta para el pase de lista
Route::get('/pase-de-lista', [PaseListaController::class, 'index'])->name('pase.lista');
Route::post('/pase-de-lista', [PaseListaController::class, 'store'])->name('pase.lista.store');


//ruta para las evaluaciones
Route::get('/Evaluaciones', [evaluacionesController::class, 'index'])->name('evaluacion');
Route::post('/guardar-calificaciones', [EvaluacionesController::class, 'guardarCalificaciones'])->name('guardar.calificaciones');








