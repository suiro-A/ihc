<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\RecepcionistaController;
use App\Http\Controllers\AdminController;

// Rutas de autenticación
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    
    // Rutas del Doctor
    Route::middleware(['role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
        Route::get('/agenda', [DoctorController::class, 'agenda'])->name('agenda');
        Route::get('/historial', [DoctorController::class, 'historial'])->name('historial.index');
        Route::get('/historial/{id}', [DoctorController::class, 'historialPaciente'])->name('historial.paciente');
        Route::get('/citas/{id}', [DoctorController::class, 'detalleCita'])->name('citas.detalle');
        Route::post('/citas/{id}/diagnostico', [DoctorController::class, 'guardarDiagnostico'])->name('citas.diagnostico');
    });

    // Rutas del Recepcionista
    Route::middleware(['role:recepcionista'])->prefix('recepcionista')->name('recepcionista.')->group(function () {
        Route::get('/dashboard', [RecepcionistaController::class, 'dashboard'])->name('dashboard');
        
        // Pacientes
        Route::get('/pacientes/registrar', [RecepcionistaController::class, 'registrarPaciente'])->name('pacientes.registrar');
        Route::post('/pacientes', [RecepcionistaController::class, 'guardarPaciente'])->name('pacientes.guardar');
        Route::get('/pacientes/buscar', [RecepcionistaController::class, 'buscarPacientes'])->name('pacientes.buscar');
        Route::get('/pacientes/{id}/editar', [RecepcionistaController::class, 'editarPaciente'])->name('pacientes.editar');
        Route::put('/pacientes/{id}', [RecepcionistaController::class, 'actualizarPaciente'])->name('pacientes.actualizar');
        
        // Citas
        Route::get('/citas', [RecepcionistaController::class, 'gestionarCitas'])->name('citas.index');
        Route::get('/citas/agendar', [RecepcionistaController::class, 'agendarCita'])->name('citas.agendar');
        Route::post('/citas', [RecepcionistaController::class, 'guardarCita'])->name('citas.guardar');
        Route::get('/citas/{id}/editar', [RecepcionistaController::class, 'editarCita'])->name('citas.editar');
        Route::put('/citas/{id}', [RecepcionistaController::class, 'actualizarCita'])->name('citas.actualizar');
        Route::patch('/citas/{id}/cancelar', [RecepcionistaController::class, 'cancelarCita'])->name('citas.cancelar');
        Route::get('/citas/paciente/{id}', [RecepcionistaController::class, 'historialCitasPaciente'])->name('citas.paciente');
    });

    // Rutas del Administrativo
    Route::middleware(['role:administrativo'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Usuarios
        Route::get('/usuarios', [AdminController::class, 'gestionUsuarios'])->name('usuarios.index');
        Route::get('/usuarios/crear', [AdminController::class, 'crearUsuario'])->name('usuarios.crear');
        Route::post('/usuarios', [AdminController::class, 'guardarUsuario'])->name('usuarios.guardar');
        Route::get('/usuarios/{id}/editar', [AdminController::class, 'editarUsuario'])->name('usuarios.editar');
        Route::put('/usuarios/{id}', [AdminController::class, 'actualizarUsuario'])->name('usuarios.actualizar');
        Route::patch('/usuarios/{id}/toggle', [AdminController::class, 'toggleUsuario'])->name('usuarios.toggle');
        
        // Disponibilidad
        Route::get('/disponibilidad', [AdminController::class, 'disponibilidad'])->name('disponibilidad.index');
        Route::post('/disponibilidad', [AdminController::class, 'guardarDisponibilidad'])->name('disponibilidad.guardar');
        
    });
});
