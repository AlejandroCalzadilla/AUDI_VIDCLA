<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\NotaCompra;
use App\Models\NotaVenta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class DatabaseSeeder extends Seeder
{
    //para ejecutar los seeeder sin hacer el migrate:fresh --seed
    //php artisan db:seed 
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(VehiculosYParabrisasSeeder::class);
        $this->call(AlmacenSeeder::class);
        //$this->call(CategoriaSeeder::class);
        //$this->call(MarcaSeeder::class);
        //$this->call(VehiculoSeeder::class);
        //$this->call(PosicionSeeder::class);
        $this->call(ProveedorSeeder::class);
       // $this->call(NotaCompraSeeder::class);
          
        $this->call(PersonalSeeder::class);
        $this->call(ClienteSeeder::class);
        //$this->call(SyncComprasVentasSeeder::class);
        
        
        // Compras y Ventas desde 2021 hasta 2024

        /*
// Primer semestre de 2021
$compra1_2021 = '2021-01-01';
// Pasar fechas como parámetros al seeder de NotaCompra
$this->callWith(NotaCompraSeeder::class, [
    'fechacompra' => $compra1_2021,
]);

$startDate2021_1 = Carbon::create(2021, 1, 1);
$endDate2021_1 = Carbon::create(2021, 6, 30);
// Pasar fechas como parámetros al seeder de NotaVenta
$this->callWith(NotaVentaSeeder::class, [
    'startDate' => $startDate2021_1,
    'endDate' => $endDate2021_1,
]);




// Primer semestre de 2021
$compra1_2021 = '2021-07-01';
// Pasar fechas como parámetros al seeder de NotaCompra
$this->callWith(NotaCompraSeeder::class, [
    'fechacompra' => $compra1_2021,
]);

$startDate2021_1 = Carbon::create(2021, 7, 1);
$endDate2021_1 = Carbon::create(2021, 12, 30);
// Pasar fechas como parámetros al seeder de NotaVenta
$this->callWith(NotaVentaSeeder::class, [
    'startDate' => $startDate2021_1,
    'endDate' => $endDate2021_1,
]);

 
     
     */ 

           $startYear = 2021;
$endYear = 2024;

for ($year = $startYear; $year <= $endYear; $year++) {
    for ($month = 1; $month <= 12; $month += 3) {
        $compraDate = Carbon::create($year, $month, 1)->toDateString();
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfQuarter();

        // Pasar fechas como parámetros al seeder de NotaCompra
        $this->callWith(NotaCompraSeeder::class, [
            'fechacompra' => $compraDate,
        ]);

        // Pasar fechas como parámetros al seeder de NotaVenta
        $this->callWith(NotaVentaSeeder::class, [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
      
        


        //$this->call(NotaVentaSeeder::class);
        // No necesita llamar a TelefonoSeeder, porque los teléfonos son creados en ClienteSeeder.

    }
}
