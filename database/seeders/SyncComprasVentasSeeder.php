<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\NotaCompra;
use App\Models\Parabrisa;
use App\Models\Proveedor;
use App\Models\NotaVenta;
use App\Models\NotaVentaParabrisa;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\Rules\Role;

class SyncComprasVentasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $almacenes = Almacen::all();
        $parabrisas = Parabrisa::all();
        $proveedores = Proveedor::all();
        $clientes = Cliente::pluck('id')->toArray();
        $ejecutivosVentas = User::whereHas('roles', function ($query) {
            $query->where('name', 'Ejecutivo de ventas');
        })->pluck('id');

        if ($ejecutivosVentas->isEmpty()) {
            throw new Exception("No hay usuarios con el rol de 'Ejecutivo de ventas'.");
        }

        // Cargar para dos años (2024 y 2025)
        $periodos = [];
        $startYear = 2021;
        $endYear = 2024;

        for ($year = $startYear; $year <= $endYear; $year++) {
            for ($month = 1; $month <= 12; $month += 3) {
                $compra = sprintf('%04d-%02d-01', $year, $month);
                $ventaStart = sprintf('%04d-%02d-01', $year, $month);
                $ventaEnd = sprintf('%04d-%02d-%02d', $year, $month + 2, cal_days_in_month(CAL_GREGORIAN, $month + 2, $year));
                $periodos[] = ['compra' => $compra, 'venta_start' => $ventaStart, 'venta_end' => $ventaEnd];
            }
        }

        $prioritizedDescriptions = [
            "TOYOTA COROLLA 4 PUERTAS VAGONETA",
            "TOYOTA COROLLA DX 4 PUERTAS VAGONETA Y AUTO",
            "TOYOTA IPSUM VAGONETA 4 PUERTAS, P/REDONDA/PEGADO",
            "TOYOTA STARLET-SOLEIL 4Y 2 PUERTAS AUTO/PEGADO",
            "TOYOTA RAV JEEP Y VAGONETA",
            "TOYOTA HILUX CAMIONETA 2 PUERTAS, P/REDONDA/GOMA",
            "TOYOTA LAND CRUSIER CAMIONETA TIPO Y.P.F.B.",
            "TOYOTA PROBOX VAGONETA 4PUERTAS P/REDONDA"
        ];
        foreach ($periodos as $periodo) {
            // Fase de Compras (llenar almacenes)

            foreach ($almacenes as $almacen) {
                $capacidadMaxima = $almacen->capacidad;
                $capacidadActual = 0;

                // Filtrar parabrisas prioritarios
                $prioritizedParabrisas = $almacen->parabrisas->filter(function ($parabrisa) use ($prioritizedDescriptions) {
                    return in_array($parabrisa->descripcion, $prioritizedDescriptions);
                });

                // Procesar parabrisas prioritarios
                foreach ($prioritizedParabrisas as $parabrisa) {
                    while ($capacidadActual < $capacidadMaxima * 0.8) {
                        $cantidad = rand(1, 20);
                        $precioUnitario = rand(50, 150);
                        $importeTotal = $precioUnitario * $cantidad;
                        $proveedor = $proveedores->random();

                        NotaCompra::create([
                            'cantidad' => $cantidad,
                            'fecha' => $periodo['compra'],
                            'precio_unitario' => $precioUnitario,
                            'importe_total' => $importeTotal,
                            'almacen_id' => $almacen->id,
                            'parabrisa_id' => $parabrisa->id,
                            'proveedor_id' => $proveedor->id
                        ]);

                        $almacenParabrisa = DB::table('almacen_parabrisa')
                            ->where('almacen_id', $almacen->id)
                            ->where('parabrisa_id', $parabrisa->id)
                            ->first();

                        if ($almacenParabrisa) {
                            DB::table('almacen_parabrisa')
                                ->where('almacen_id', $almacen->id)
                                ->where('parabrisa_id', $parabrisa->id)
                                ->update(['stock' => $almacenParabrisa->stock + $cantidad]);
                        } else {
                            DB::table('almacen_parabrisa')->insert([
                                'almacen_id' => $almacen->id,
                                'parabrisa_id' => $parabrisa->id,
                                'stock' => $cantidad,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        $capacidadActual += $cantidad;
                    }
                }

                // Procesar parabrisas restantes
                $remainingParabrisas = $almacen->parabrisas->diff($prioritizedParabrisas);

                foreach ($remainingParabrisas as $parabrisa) {
                    while ($capacidadActual < $capacidadMaxima * 0.8) {
                        $cantidad = rand(1, 20);
                        $precioUnitario = rand(50, 150);
                        $importeTotal = $precioUnitario * $cantidad;
                        $proveedor = $proveedores->random();

                        NotaCompra::create([
                            'cantidad' => $cantidad,
                            'fecha' => $periodo['compra'],
                            'precio_unitario' => $precioUnitario,
                            'importe_total' => $importeTotal,
                            'almacen_id' => $almacen->id,
                            'parabrisa_id' => $parabrisa->id,
                            'proveedor_id' => $proveedor->id
                        ]);

                        $almacenParabrisa = DB::table('almacen_parabrisa')
                            ->where('almacen_id', $almacen->id)
                            ->where('parabrisa_id', $parabrisa->id)
                            ->first();

                        if ($almacenParabrisa) {
                            DB::table('almacen_parabrisa')
                                ->where('almacen_id', $almacen->id)
                                ->where('parabrisa_id', $parabrisa->id)
                                ->update(['stock' => $almacenParabrisa->stock + $cantidad]);
                        } else {
                            DB::table('almacen_parabrisa')->insert([
                                'almacen_id' => $almacen->id,
                                'parabrisa_id' => $parabrisa->id,
                                'stock' => $cantidad,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }

                        $capacidadActual += $cantidad;
                    }
                }
            }

            // Fase de Ventas (consumir stock)
            $startDate = Carbon::createFromFormat('Y-m-d', $periodo['venta_start']);
            $endDate = Carbon::createFromFormat('Y-m-d', $periodo['venta_end']);
            $daysBetween = $endDate->diffInDays($startDate);

            $prioritizedDescriptions = [
                "TOYOTA COROLLA 4 PUERTAS VAGONETA",
                "TOYOTA COROLLA DX 4 PUERTAS VAGONETA Y AUTO",
                "TOYOTA IPSUM VAGONETA 4 PUERTAS, P/REDONDA/PEGADO",
                "TOYOTA STARLET-SOLEIL 4Y 2 PUERTAS AUTO/PEGADO",
                "TOYOTA RAV JEEP Y VAGONETA",
                "TOYOTA HILUX CAMIONETA 2 PUERTAS, P/REDONDA/GOMA",
                "TOYOTA LAND CRUSIER CAMIONETA TIPO Y.P.F.B.",
                "TOYOTA PROBOX VAGONETA 4PUERTAS P/REDONDA"
            ];

            foreach ($almacenes as $almacen) {
                // Filtrar parabrisas prioritarios
                $prioritizedParabrisas = $almacen->parabrisas->filter(function ($parabrisa) use ($prioritizedDescriptions) {
                    return in_array($parabrisa->descripcion, $prioritizedDescriptions);
                });

                // Procesar parabrisas prioritarios
                foreach ($prioritizedParabrisas as $parabrisa) {
                    $stock = DB::table('almacen_parabrisa')
                        ->where('almacen_id', $almacen->id)
                        ->where('parabrisa_id', $parabrisa->id)
                        ->value('stock');

                    while ($stock > 0) {
                        $cantidadVenta = rand(1, min($stock, 10));
                        $precioVenta = rand(50, 150);
                        $importeTotal = $cantidadVenta * $precioVenta;

                        $clienteId = $clientes[array_rand($clientes)];
                        $userId = $ejecutivosVentas->random();
                        $fechaVenta = $startDate->copy()->addDays(rand(0, $daysBetween));

                        $notaVenta = NotaVenta::create([
                            'fecha' => $fechaVenta,
                            'monto_total' => $importeTotal,
                            'user_id' => $userId,
                            'cliente_id' => $clienteId,
                            'almacen_id' => $almacen->id
                        ]);

                        DB::table('nota_venta_parabrisa')->insert([
                            'cantidad' => $cantidadVenta,
                            'precio_venta' => $precioVenta,
                            'importe' => $importeTotal,
                            'nota_venta_id' => $notaVenta->id,
                            'parabrisa_id' => $parabrisa->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        // Reducir el stock disponible
                        $stock -= $cantidadVenta;

                        // Actualizar el stock en la base de datos
                        DB::table('almacen_parabrisa')
                            ->where('almacen_id', $almacen->id)
                            ->where('parabrisa_id', $parabrisa->id)
                            ->update(['stock' => $stock]);
                    }
                }

                // Procesar parabrisas restantes
                $remainingParabrisas = $almacen->parabrisas->diff($prioritizedParabrisas);

                foreach ($remainingParabrisas as $parabrisa) {
                    $stock = DB::table('almacen_parabrisa')
                        ->where('almacen_id', $almacen->id)
                        ->where('parabrisa_id', $parabrisa->id)
                        ->value('stock');

                    while ($stock > 0) {
                        $cantidadVenta = rand(1, min($stock, 10));
                        $precioVenta = rand(50, 150);
                        $importeTotal = $cantidadVenta * $precioVenta;

                        $clienteId = $clientes[array_rand($clientes)];
                        $userId = $ejecutivosVentas->random();
                        $fechaVenta = $startDate->copy()->addDays(rand(0, $daysBetween));

                        $notaVenta = NotaVenta::create([
                            'fecha' => $fechaVenta,
                            'monto_total' => $importeTotal,
                            'user_id' => $userId,
                            'cliente_id' => $clienteId,
                            'almacen_id' => $almacen->id
                        ]);

                        DB::table('nota_venta_parabrisa')->insert([
                            'cantidad' => $cantidadVenta,
                            'precio_venta' => $precioVenta,
                            'importe' => $importeTotal,
                            'nota_venta_id' => $notaVenta->id,
                            'parabrisa_id' => $parabrisa->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        // Reducir el stock disponible
                        $stock -= $cantidadVenta;

                        // Actualizar el stock en la base de datos
                        DB::table('almacen_parabrisa')
                            ->where('almacen_id', $almacen->id)
                            ->where('parabrisa_id', $parabrisa->id)
                            ->update(['stock' => $stock]);
                    }
                }
            }
        }
    }
}
