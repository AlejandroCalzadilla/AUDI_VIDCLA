<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NotaVenta;
use App\Models\NotaVentaParabrisa;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Almacen;
use App\Models\Parabrisa;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class NotaVentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run($startDate, $endDate)
    {
        // Obtener clientes y usuarios
        $clientes = Cliente::pluck('id')->toArray();
        $ejecutivosVentas = User::whereHas('roles', function ($query) {
            $query->where('name', 'Ejecutivo de ventas');
        })->pluck('id');

        // Asegúrate de tener usuarios con este rol
        if ($ejecutivosVentas->isEmpty()) {
            throw new Exception("No hay usuarios con el rol de 'Ejecutivo de ventas'.");
        }

        // Obtener parabrisas y stock disponible
        $almacenes = Almacen::with('parabrisas')->get();
        $parabrisas = Parabrisa::all();
        $daysBetween = $endDate->diffInDays($startDate);
        $ventaCounter = 0;



        foreach ($almacenes as $almacen) {
            // Obtener clientes y usuarios
            $clientes = Cliente::pluck('id')->toArray();
            $ejecutivosVentas = User::whereHas('roles', function ($query) {
                $query->where('name', 'Ejecutivo de ventas');
            })->pluck('id');

            // Asegúrate de tener usuarios con este rol
            if ($ejecutivosVentas->isEmpty()) {
                throw new Exception("No hay usuarios con el rol de 'Ejecutivo de ventas'.");
            }

            // Obtener almacenes con parabrisas
            $almacenes = Almacen::with('parabrisas')->get();
            $daysBetween = $endDate->diffInDays($startDate);
            $ventaCounter = 0;

            foreach ($almacenes as $almacen) {
                foreach ($almacen->parabrisas as $parabrisa) {
                    $stock = $parabrisa->pivot->stock; // Obtener el stock disponible en el almacén

                    if ($stock > 0) {
                        // Generar una cantidad de venta aleatoria respetando el stock disponible
                        $cantidadVenta = rand(1, min($stock, 10)); // No puede vender más del stock disponible
                        $precioVenta = rand(50, 150); // Precio de venta aleatorio
                        $importeTotal = $cantidadVenta * $precioVenta;
                        $clienteId = $clientes[array_rand($clientes)];
                        $userId = $ejecutivosVentas->random();
                        $fechaVenta = $startDate->copy()->addDays(rand(0, $daysBetween)); // Fecha aleatoria dentro del rango

                        // Crear la nota de venta
                        $notaVenta = NotaVenta::create([
                            'fecha' => $fechaVenta,
                            'monto_total' => $importeTotal,
                            'user_id' => $userId,
                            'cliente_id' => $clienteId,
                            'almacen_id' => $almacen->id
                        ]);

                        // Insertar el detalle de la venta en la tabla 'nota_venta_parabrisa'
                        DB::table('nota_venta_parabrisa')->insert([
                            'cantidad' => $cantidadVenta,
                            'precio_venta' => $precioVenta,
                            'importe' => $importeTotal,
                            'nota_venta_id' => $notaVenta->id,
                            'parabrisa_id' => $parabrisa->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        // Actualizar el stock del parabrisas en el almacén
                        DB::table('almacen_parabrisa')
                            ->where('almacen_id', $almacen->id)
                            ->where('parabrisa_id', $parabrisa->id)
                            ->update(['stock' => $stock - $cantidadVenta]);

                        $ventaCounter++;
                    }
                }
            }

            echo "Se han generado $ventaCounter ventas.";
        }
        echo "Se han generado $ventaCounter ventas y se ha consumido todo el stock de parabrisas.";
    }
}
