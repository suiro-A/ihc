<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Medicamento;

class MedicamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicamentos = [
            ['nombre' => 'Paracetamol', 'descripcion' => 'Analgésico y antipirético', 'presentacion' => '500mg tabletas'],
            ['nombre' => 'Ibuprofeno', 'descripcion' => 'Antiinflamatorio no esteroideo', 'presentacion' => '400mg tabletas'],
            ['nombre' => 'Amoxicilina', 'descripcion' => 'Antibiótico penicilina', 'presentacion' => '500mg cápsulas'],
            ['nombre' => 'Losartán', 'descripcion' => 'Antihipertensivo', 'presentacion' => '50mg tabletas'],
            ['nombre' => 'Metformina', 'descripcion' => 'Antidiabético', 'presentacion' => '850mg tabletas'],
            ['nombre' => 'Omeprazol', 'descripcion' => 'Inhibidor de bomba de protones', 'presentacion' => '20mg cápsulas'],
            ['nombre' => 'Atorvastatina', 'descripcion' => 'Estatina para colesterol', 'presentacion' => '20mg tabletas'],
            ['nombre' => 'Captopril', 'descripcion' => 'Inhibidor de ECA', 'presentacion' => '25mg tabletas'],
            ['nombre' => 'Diclofenaco', 'descripcion' => 'Antiinflamatorio no esteroideo', 'presentacion' => '50mg tabletas'],
            ['nombre' => 'Loratadina', 'descripcion' => 'Antihistamínico', 'presentacion' => '10mg tabletas'],
        ];

        foreach ($medicamentos as $medicamento) {
            Medicamento::create($medicamento);
        }
    }
}
