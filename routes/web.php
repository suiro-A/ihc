<?php

use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('recepcionista.dashboard');
})->name('recepcionista.dashboard');

// Formularios
Route::get('/registrar',[PacienteController::class,'index'])->name('registrarIndex');

Route::get('/paciente/create',[PacienteController::class, 'create'])->name('paciente.create');

// Se guarda en la BD
Route::post('/paciente',[PacienteController::class,'registrar'])->name('paciente.registrar');

// Buscar
Route::get('/paciente/search', function () {
    return view('recepcionista.pacientes.buscar');
})->name('paciente.search');

Route::get('/paciente/{id}',[PacienteController::class, 'buscar'])->name('paciente.buscar');
// Actualizar
// Form update
Route::get('/paciente/{id}/edit',[PacienteController::class, 'edit'])->name('paciente.edit');
Route::put('/paciente/{id}',[PacienteController::class, 'update'])->name('paciente.update');

// Borrar
Route::delete('/paciente/{id}', [PacienteController::class, 'destroy'])->name('paciente.destroy');



Route::get('/layout', function () {
    return view('layouts.app');
});


