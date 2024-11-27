<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Almacen;
use App\Models\NotaCompra;
use App\Models\Parabrisa;
use App\Models\Proveedor;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotaCompraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run($fechacompra): void
    {
        $almacenes = Almacen::all();
        $parabrisas = Parabrisa::all();
        $proveedores = Proveedor::all();

        $fechaPersonalizada = $fechacompra;

        $prioritizedDescriptions = [
            "TOYOTA COROLLA 4 PUERTAS VAGONETA",
            "TOYOTA COROLLA DX 4 PUERTAS VAGONETA Y AUTO",
            "TOYOTA IPSUM VAGONETA 4 PUERTAS, P/REDONDA/PEGADO",
            "TOYOTA STARLET-SOLEIL 4Y 2 PUERTAS AUTO/PEGADO",
            "TOYOTA RAV JEEP Y VAGONETA",
            "TOYOTA HILUX CAMIONETA 2 PUERTAS, P/REDONDA/GOMA",
            "TOYOTA LAND CRUSIER CAMIONETA TIPO Y.P.F.B.",
            "TOYOTA PROBOX VAGONETA 4PUERTAS P/REDONDA",
            
        ];

        foreach ($almacenes as $almacen) {
            $capacidadMaxima = $almacen->capacidad;
            $capacidadOcupada = $this->calcularCapacidadOcupada($almacen->id);
            $capacidadDisponible = $capacidadMaxima - $capacidadOcupada;
            $capacidadPrioritaria = $capacidadMaxima * 0.6; // 40% para parabrisas prioritarios
            $capacidadNoPrioritaria = $capacidadMaxima - $capacidadPrioritaria; // 60% para el resto
            $capacidadActual = $capacidadOcupada;
        
            // Filtrar parabrisas prioritarios con la posición igual a 1
            $prioritizedParabrisas = $parabrisas->filter(function ($parabrisa) use ($prioritizedDescriptions) {
                return in_array($parabrisa->descripcion, $prioritizedDescriptions) && $parabrisa->posicion_id == 1;
            });
        
            foreach ($prioritizedParabrisas as $para) {
                if ($capacidadActual < $capacidadPrioritaria && $capacidadDisponible > 0) {
                    $cantidad = min(rand(5, 20), $capacidadPrioritaria - $capacidadActual); // Ajusta la cantidad según la capacidad disponible
                    $precioUnitario = rand(50, 150);
                    $importeTotal = $precioUnitario * $cantidad;
                    $proveedor = $proveedores->random();
        
                    NotaCompra::create([
                        'cantidad' => $cantidad,
                        'fecha' => $fechaPersonalizada,
                        'precio_unitario' => $precioUnitario,
                        'importe_total' => $importeTotal,
                        'almacen_id' => $almacen->id,
                        'parabrisa_id' => $para->id,
                        'proveedor_id' => $proveedor->id
                    ]);
        
                    // Verificar y actualizar el stock
                    $this->updateStock($almacen->id, $para->id, $cantidad);
                    $capacidadActual += $cantidad;
                    $capacidadDisponible -= $cantidad;
                }
            }
        
            // Procesar parabrisas restantes para llenar el 60% restante de la capacidad
            $remainingParabrisas = $parabrisas->diff($prioritizedParabrisas);
            foreach ($remainingParabrisas as $parab) {
                if ($capacidadActual < $capacidadMaxima && $capacidadDisponible > 0) {
                    $cantidad = min(rand(1, 10), $capacidadMaxima - $capacidadActual); // Ajusta la cantidad según la capacidad disponible
                    $precioUnitario = rand(50, 150);
                    $importeTotal = $precioUnitario * $cantidad;
                    $proveedor = $proveedores->random();
        
                    NotaCompra::create([
                        'cantidad' => $cantidad,
                        'fecha' => $fechaPersonalizada,
                        'precio_unitario' => $precioUnitario,
                        'importe_total' => $importeTotal,
                        'almacen_id' => $almacen->id,
                        'parabrisa_id' => $parab->id,
                        'proveedor_id' => $proveedor->id
                    ]);
        
                    // Verificar y actualizar el stock
                    $this->updateStock($almacen->id, $parab->id, $cantidad);
                    $capacidadActual += $cantidad;
                    $capacidadDisponible -= $cantidad;
                }
            }
        }
    }

    private function calcularCapacidadOcupada($almacenId)
    {
        return DB::table('almacen_parabrisa')
            ->where('almacen_id', $almacenId)
            ->sum('stock');
    }
    
    private function updateStock($almacenId, $parabrisaId, $cantidad)
    {
        $almacenParabrisa = DB::table('almacen_parabrisa')
            ->where('almacen_id', $almacenId)
            ->where('parabrisa_id', $parabrisaId)
            ->first();
    
        if ($almacenParabrisa) {
            DB::table('almacen_parabrisa')
                ->where('almacen_id', $almacenId)
                ->where('parabrisa_id', $parabrisaId)
                ->update(['stock' => $almacenParabrisa->stock + $cantidad]);
        } else {
            DB::table('almacen_parabrisa')->insert([
                'almacen_id' => $almacenId,
                'parabrisa_id' => $parabrisaId,
                'stock' => $cantidad,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
