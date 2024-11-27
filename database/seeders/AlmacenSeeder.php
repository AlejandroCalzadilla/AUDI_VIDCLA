<?php

namespace Database\Seeders;

use App\Models\Almacen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlmacenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('almacens')->insert([
            [
                'nombre' => 'Oficina Central',
                'ubicacion' => 'Avenida Trompillo frente al Deber',
                'capacidad' => 1000,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Virgen de Cotoca',
                'ubicacion' => 'Av Virgen de Cotoca 2do anillo',
                'capacidad' => 900,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Oficina Kilometro 8',
                'ubicacion' => 'Av Doble Vía La Guardia km 8',
                'capacidad' => 800,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
