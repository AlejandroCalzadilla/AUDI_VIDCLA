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

    // Identificar los parabrisas prioritarios (50% de los más vendidos)
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

        // Comprar solo si la ocupación está por debajo del 80% de la capacidad
        if ($capacidadOcupada >= $capacidadMaxima * 0.8) {
            continue;
        }

        $capacidadPrioritaria = $capacidadMaxima * 0.5; // 50% para parabrisas prioritarios
        $prioritizedParabrisas = $parabrisas->filter(function ($parabrisa) use ($prioritizedDescriptions) {
            return in_array($parabrisa->descripcion, $prioritizedDescriptions);
        });

        // Comprar parabrisas prioritarios
        $capacidadActual = $capacidadOcupada;
        foreach ($prioritizedParabrisas as $para) {
            if ($capacidadActual < $capacidadPrioritaria) {
                $cantidad = min(rand(10, 30), $capacidadPrioritaria - $capacidadActual);
                $this->crearNotaCompra($almacen, $para, $proveedores->random(), $fechaPersonalizada, $cantidad);
                $capacidadActual += $cantidad;
            }
        }

        // Comprar parabrisas no prioritarios
        $remainingParabrisas = $parabrisas->diff($prioritizedParabrisas);
        foreach ($remainingParabrisas as $parab) {
            if ($capacidadActual < $capacidadMaxima) {
                $cantidad = min(rand(3, 15), $capacidadMaxima - $capacidadActual);
                $this->crearNotaCompra($almacen, $parab, $proveedores->random(), $fechaPersonalizada, $cantidad);
                $capacidadActual += $cantidad;
            }
        }
    }
}

private function calcularCapacidadOcupada($almacenId)
{
    // Calcula el stock total ocupado en el almacén
    return DB::table('almacen_parabrisa')
        ->where('almacen_id', $almacenId)
        ->sum('stock');
}

private function updateStock($almacenId, $parabrisaId, $cantidad)
{
    // Actualiza el stock en la tabla almacen_parabrisa
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

private function crearNotaCompra($almacen, $parabrisa, $proveedor, $fecha, $cantidad)
{
    $precioUnitario = rand(50, 150);
    $importeTotal = $precioUnitario * $cantidad;

    // Crear la nota de compra
    NotaCompra::create([
        'cantidad' => $cantidad,
        'fecha' => $fecha,
        'precio_unitario' => $precioUnitario,
        'importe_total' => $importeTotal,
        'almacen_id' => $almacen->id,
        'parabrisa_id' => $parabrisa->id,
        'proveedor_id' => $proveedor->id,
    ]);

    // Actualizar el stock del almacén
    $this->updateStock($almacen->id, $parabrisa->id, $cantidad);
}

     
}
