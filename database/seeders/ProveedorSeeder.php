<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proveedores = [
            [
                'nombre' => 'Vidcla',
                'email' => 'vidcla@example.com',
                'telefono' => '123456789',
                'ciudad' => 'Santa Cruz',
                'pais' => 'Bolivia',
            ],
            [
                'nombre' => 'Sinyi',
                'email' => 'sinyi@example.com',
                'telefono' => '987654321',
                'ciudad' => 'Beijing',
                'pais' => 'China',
            ],
            [
                'nombre' => 'Fujao',
                'email' => 'fujao@example.com',
                'telefono' => '456789123',
                'ciudad' => 'Shanghai',
                'pais' => 'China',
            ],
            [
                'nombre' => 'Benson',
                'email' => 'benson@example.com',
                'telefono' => '789123456',
                'ciudad' => 'Guangzhou',
                'pais' => 'China',
            ],
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::create($proveedor);
        }
    }
}
