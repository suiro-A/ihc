<?php

namespace App\Http\Controllers;

use App\Models\Medicamentos;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PacienteController extends Controller
{   
    public function index(){
        return view('registrarPaciente');
    }

    public function create()  {

        $medicamentos = Medicamentos::all(); //Usando modelo
        $alergias = DB::table('alergia')->get(); // Sin usar un modelo
        $enfermedades = DB::table('enfermedad_cronica')->get();

        return view('recepcionista.pacientes.registrar', compact('medicamentos', 'alergias', 'enfermedades'));

        
    }

    public function registrar(Request $datos){
        //   dd($datos->all());
        // return $datos;
        $datos->validate([
        'nombres' => 'required|string|max:100',
        'apellidos' => 'required|string|max:100',
        'dni' => 'required|digits:8|unique:paciente,dni',
        'fecha_nac' => 'required|date',
        'sexo' => 'required|in:0,1',
        'telefono' => 'required|digits_between:7,9',
        'correo' => 'required|email|unique:paciente,correo',
        'cronicas' => 'nullable|array',
        'cronicas.*' => 'integer|exists:enfermedad_cronica,id_enfermedad',

        'medicacion' => 'nullable|array',
        'medicacion.*' => 'integer|exists:medicamento,id_medicamento',

        'alergias' => 'nullable|array',
        'alergias.*' => 'integer|exists:alergia,id_alergia',

        ]);
        session()->flash('pacienteCreate',[

            'title' => "¡Bien hecho!",
            'text' => "Paciente creado correctamente",
            'icon' => "success"
        ]);
        // return $datos;

        // Si 'medicacion' viene en la petición, úsalo; si no, usa array vacío
        $medicamentos = $datos->input('medicacion', []);
        $enfermedades = $datos->input('cronicas', []);  
        $alergias = $datos->input('alergias', []);  

        // Paciente::create($datos->all());

        $paciente = new Paciente();

        $paciente->nombres = $datos->nombres;
        $paciente->apellidos = $datos->apellidos;   
        $paciente->dni = $datos->dni;       
        $paciente->fecha_nac = $datos->fecha_nac;  
        $paciente->sexo = $datos->sexo;  
        $paciente->telefono = $datos->telefono;  
        $paciente->correo = $datos->correo;

        $paciente->save();

        // ! aca ira la creación en  historial
        $historial =$paciente->historial()->create([]);

        // No es necesario obtener solo el ID
        // $historialId = $historial->id_historial;


        // Llenado de las tablas intermedias de medicamentos, alergias y enfermedades
        if (!empty($medicamentos)) {
            // hay medicamentos seleccionados
            $historial->medicamentos()->attach($medicamentos);
        }
        if (!empty($alergias)) {
            // hay alergias seleccionadas
            $historial->alergias()->attach($alergias);
        }
        if (!empty($enfermedades)) {
            // hay enfermedades seleccionadas
            $historial->enfermedades()->attach($enfermedades);
        }
        


        return redirect()->route('paciente.search')->with('success', 'Usuario creado correctamente.');

        
    }


    public function buscar($id){
        // $paciente = Paciente::find($id);
        $id = "%".$id."%";
        $paciente = Paciente::where('nombres', 'like', $id)->get();

        if ($paciente->isEmpty()) {
            return response()->json(['er' => 'Paciente no encontrado'], 404);
         }

        return response()->json($paciente);
        // return $paciente;

    }

    public function edit($id)  {
        $paciente = Paciente::find($id);

        return view('recepcionista.pacientes.editar', compact('paciente'));
                
    }

    public function update(Request $request, $id){

        $paciente = Paciente::find($id);

        //TODO Falta validar

        $paciente->update([
            'nombres'     => $request->input('nombre'),
            'apellidos'   => $request->input('apellidos'),
            'dni'         => $request->input('dni'),
            'fecha_nac'   => $request->input('fecha_nacimiento'),
            'sexo'        => $request->input('genero'),
            'telefono'    => $request->input('telefono'),
            'correo'      => $request->input('email'),

        ]);

        session()->flash('swal',[

         
            'title' => "¡Bien hecho!",
            'text' => "Paciente actualizado correctamente",
            'icon' => "success"


        ]);
        // return request();
        return redirect()->route('paciente.search')->with('success', 'Paciente actualizado correctamente');

    }

    public function destroy($id)  {
        $paciente = Paciente::find($id);
        $paciente->delete();
        // return redirect()->route('prueba')->with('success', 'Paciente Eliminado correctamente');
    

         



   
        return response()->json([
            'success' => true,
            'message' => 'Paciente eliminado correctamente'
        ]);
        
    }
}
