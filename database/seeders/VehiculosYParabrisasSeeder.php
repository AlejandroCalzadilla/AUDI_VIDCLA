<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Maatwebsite\Excel\Facades\Excel;
use App\Models\Vehiculo;
use App\Models\Parabrisas;
use App\Models\Marca;
use App\Models\Posicion;
use App\Models\Categoria;
use App\Models\Parabrisa;
use Maatwebsite\Excel\Concerns\ToArray;



class VehiculosYParabrisasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Ruta del archivo Excel
        $filePath = storage_path('app/excels/catalogo.csv');

        // Cargar el archivo Excel utilizando maatwebsite/excel
        $data = Excel::toArray(new VehiculosImport, $filePath);

        // El primer array dentro de $data es la primera hoja del archivo Excel
        $rows = $data[0];

        // Insertar marcas si no existen
        $marcaToyota = Marca::firstOrCreate(['nombre' => 'Toyota']);
        $marcaNissan = Marca::firstOrCreate(['nombre' => 'Nissan']);
        $marcaMitsubishi = Marca::firstOrCreate(['nombre' => 'Mitsubishi']);
        
        // Insertar posiciones si no existen
        $posiciones = [
            'parabrisas delantero' => Posicion::firstOrCreate(['nombre' => 'parabrisas delantero']),
            'parabrisas trasero' => Posicion::firstOrCreate(['nombre' => 'parabrisas trasero']),
            'derecho delantero' => Posicion::firstOrCreate(['nombre' => 'derecho delantero']),
            'derecho trasero' => Posicion::firstOrCreate(['nombre' => 'derecho trasero']),
            'izquierdo delantero' => Posicion::firstOrCreate(['nombre' => 'izquierdo delantero']),
            'izquierdo trasero' => Posicion::firstOrCreate(['nombre' => 'izquierdo trasero']),
        ];

        // Insertar categorías si no existen
        $categoriaLaminado = Categoria::firstOrCreate(['nombre' => 'laminado']);
        $categoriaTemplado = Categoria::firstOrCreate(['nombre' => 'templado']);

        // Procesar el archivo Excel y crear registros
        foreach ($rows as $index => $row) {
            // Saltar la fila de encabezado
            if ($index === 0) {
                continue;
            }

            // Validar y normalizar el formato de "AÑO"
            $anio = $this->procesarAnio($row[2]);

            // Crear el vehículo si no existe
            $vehiculo = Vehiculo::create([
                'año' => $anio, // Columna AÑO ya procesada
                'descripcion' => $row[1], // Columna DESCRIPCIÓN
                'marca_id' => $marcaToyota->id // Suponiendo que todos son Toyota en este caso
            ]);

            // Crear parabrisas para cada posición
            foreach ($posiciones as $posicion) {
                Parabrisa::create([
                    'abajo' => $row[5], // Columna ABAJO
                    'arriba' => $row[3], // Columna ARRIBA
                    'costado' => $row[6], // Columna COSTADO
                    'medio' => $row[4], // Columna MEDIO
                    'descripcion' => $row[1], // Nombre del parabrisas basado en descripción del vehículo
                    'observacion' => 'Lore ipsum dolor sit amet',
                    'posicion_id' => $posicion->id,
                    'categoria_id' => $categoriaLaminado->id, // Asumimos categoría laminado, puedes ajustarlo
                    'vehiculo_id' => $vehiculo->id,
                ]);
            }
        }
    }

    // Método para procesar el campo "AÑO"
    private function procesarAnio($anio)
    {
        // Eliminar espacios en blanco
        $anio = trim($anio);

        // Verificar si tiene un rango (año1-año2)
        if (strpos($anio, '-') !== false) {
            list($anioInicio, $anioFin) = explode('-', $anio);
            return trim($anioInicio) . '-' . trim($anioFin);
        }

        // Si solo hay un año, devolverlo como está
        return $anio;
    }
}

class VehiculosImport implements ToArray
{
    public function array(array $rows)
    {
        // Devolver el array de filas
        return $rows;
    }
}