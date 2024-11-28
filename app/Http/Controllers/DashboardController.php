<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\NotaCompra;
use App\Models\NotaVenta;
use App\Models\Parabrisa;
use App\Models\Proveedor;
use ConsoleTVs\Charts\Charts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('admin'); // Aplica el middleware de administrador a todos los métodos
    }




    public function todasLasVentas()
    {
        $ventas = NotaVenta::all();
        return response()->json($ventas);
    }

    public function todasLasCompras()
    {
        $compras = NotaCompra::all();
        return response()->json($compras);
    }

    public function todosLosAlmacenes()
    {
        $almacenes = Almacen::all();
        return response()->json($almacenes);
    }

    public function todosLosParabrisas()
    {
        $parabrisas = Parabrisa::all();
        return response()->json($parabrisas);
    }

    public function todosLosClientes()
    {
        $clientes = Cliente::all();
        return response()->json($clientes);
    }

    public function todosLosProveedores()
    {
        $proveedores = Proveedor::all();
        return response()->json($proveedores);
    }

    public function almacenparabrisa(){

        $almacenparabrisa = DB::table('almacen_parabrisa')->get();
        return response()->json($almacenparabrisa);
    }

    
    public function detallenotaventa(){

        $almacenparabrisa = DB::table('nota_venta_parabrisa')->get();
        return response()->json($almacenparabrisa);
    }


    public function categoria(){

        $categoria = DB::table('categorias')->get();
        return response()->json($categoria);

    }



    
    public function index()
    {


         return view('dashboard');

        /*

        $totalVentasMensual = NotaVenta::whereMonth('fecha', now()->month)->sum('monto_total');
        $totalComprasMensual = NotaCompra::whereMonth('fecha', now()->month)->sum('importe_total');
        $almacenesOcupados = Almacen::withCount('parabrisas')->get()->avg('capacity'); // Ejemplo

        // KPIs de Ventas


        $ventasPorMes = NotaVenta::selectRaw('EXTRACT(MONTH FROM fecha) as mes, EXTRACT(YEAR FROM fecha) as año, SUM(monto_total) as total_por_mes')
            ->join('nota_venta_parabrisa', 'nota_ventas.id', '=', 'nota_venta_parabrisa.nota_venta_id')
            ->join('parabrisas', 'parabrisas.id', '=', 'nota_venta_parabrisa.parabrisa_id')
            ->groupByRaw('EXTRACT(YEAR FROM fecha), EXTRACT(MONTH FROM fecha)')
            ->orderByRaw('EXTRACT(YEAR FROM fecha), EXTRACT(MONTH FROM fecha)')
            ->get();

        $cVentasPorMes = DB::table('nota_ventas as nv')
            ->select(
                DB::raw('EXTRACT(MONTH FROM fecha) as mes'),
                DB::raw('EXTRACT(YEAR FROM fecha) as año'),
                DB::raw('SUM(nvp.cantidad) as total_por_mes')
            )
            ->join('nota_venta_parabrisa as nvp', 'nv.id', '=', 'nvp.nota_venta_id')
            ->groupBy(DB::raw('EXTRACT(YEAR FROM fecha)'), DB::raw('EXTRACT(MONTH FROM fecha)'))
            ->orderBy(DB::raw('EXTRACT(YEAR FROM fecha)'), 'asc')
            ->orderBy(DB::raw('EXTRACT(MONTH FROM fecha)'), 'asc')
            ->get();


        //$catidadVentas = NotaVenta::sum('monto_total'); // Total de ventas realizadas                  
        $CantidadParabrisasVentas = NotaVenta::join('nota_venta_parabrisa', 'nota_ventas.id', '=', 'nota_venta_parabrisa.nota_venta_id')
            ->sum('nota_venta_parabrisa.cantidad'); //dd($CantidadParabrisasVentas);

        $totalVentas = NotaVenta::sum('monto_total'); // Total de ventas realizadas
        //dd($ventasPorMes); 
       
        $ventasPorProducto = NotaVenta::selectRaw('parabrisas.descripcion, SUM(nota_venta_parabrisa.cantidad) as total')
            ->join('nota_venta_parabrisa', 'nota_ventas.id', '=', 'nota_venta_parabrisa.nota_venta_id')
            ->join('parabrisas', 'parabrisas.id', '=', 'nota_venta_parabrisa.parabrisa_id')
            ->groupBy('parabrisas.descripcion')
            ->get();

        $ventasPorAlmacen = NotaVenta::selectRaw('almacens.nombre, SUM(nota_ventas.monto_total) as total')
            ->join('almacens', 'almacens.id', '=', 'nota_ventas.almacen_id')
            ->groupBy('almacens.nombre')
            ->get();

        $ingresosTotales = $totalVentas; // Ingresos totales por ventas


        $ventasPorEjecutivo = NotaVenta::selectRaw('users.name, SUM(nota_ventas.monto_total) as total')
            ->join('users', 'users.id', '=', 'nota_ventas.user_id')
            ->groupBy('users.name')
            ->get();

        $top5ParabrisasVendidos = NotaVenta::selectRaw('posicions.nombre, parabrisas.descripcion, SUM(nota_venta_parabrisa.cantidad) as total')
            ->join('nota_venta_parabrisa', 'nota_ventas.id', '=', 'nota_venta_parabrisa.nota_venta_id')
            ->join('parabrisas', 'parabrisas.id', '=', 'nota_venta_parabrisa.parabrisa_id')
            ->join('posicions', 'posicions.id', '=', 'parabrisas.posicion_id')
            ->groupBy('posicions.nombre', 'parabrisas.descripcion') // Corregido: agrupar por ambas columnas
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get();


        $parabrisasMayorMovimiento = Parabrisa::selectRaw('posicions.nombre,parabrisas.descripcion, (SUM(nota_venta_parabrisa.cantidad) + SUM(nota_compras.cantidad)) as movimiento_total')
            ->leftJoin('nota_venta_parabrisa', 'parabrisas.id', '=', 'nota_venta_parabrisa.parabrisa_id')
            ->leftJoin('nota_compras', 'parabrisas.id', '=', 'nota_compras.parabrisa_id')
            ->join('posicions', 'posicions.id', '=', 'parabrisas.posicion_id')
            ->groupBy('posicions.nombre', 'parabrisas.descripcion')
            ->orderBy('movimiento_total', 'DESC')
            ->limit(5)
            ->get();



        $parabrisasMenorMovimiento = Parabrisa::selectRaw('posicions.nombre, parabrisas.descripcion, (SUM(nota_venta_parabrisa.cantidad) + SUM(nota_compras.cantidad)) as movimiento_total')
            ->leftJoin('nota_venta_parabrisa', 'parabrisas.id', '=', 'nota_venta_parabrisa.parabrisa_id')
            ->leftJoin('nota_compras', 'parabrisas.id', '=', 'nota_compras.parabrisa_id')
            ->join('posicions', 'posicions.id', '=', 'parabrisas.posicion_id')
            ->groupBy('posicions.nombre', 'parabrisas.descripcion')
            ->orderBy('movimiento_total', 'ASC')
            ->limit(5)
            ->get();
        //dd($top5ParabrisasVendidos); 

        $tasaCrecimientoVentas = NotaVenta::selectRaw('EXTRACT(YEAR FROM fecha) as year, SUM(monto_total) as total')
            ->groupByRaw('EXTRACT(YEAR FROM fecha)')
            ->orderBy('year')
            ->get();

        // KPIs de Compras
        $totalCompras = NotaCompra::sum('importe_total'); // Total de compras realizadas
        $comprasPorProducto = NotaCompra::selectRaw('parabrisas.descripcion, SUM(nota_compras.cantidad) as total')
            ->join('parabrisas', 'parabrisas.id', '=', 'nota_compras.parabrisa_id')
            ->groupBy('parabrisas.descripcion')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get();

        $comprasPorProveedor = NotaCompra::selectRaw('proveedors.nombre, SUM(nota_compras.importe_total) as total')
            ->join('proveedors', 'proveedors.id', '=', 'nota_compras.proveedor_id')
            ->groupBy('proveedors.nombre')
            ->get();

        $comprasPorAlmacen = NotaCompra::selectRaw('almacens.nombre, SUM(nota_compras.importe_total) as total')
            ->join('almacens', 'almacens.id', '=', 'nota_compras.almacen_id')
            ->groupBy('almacens.nombre')
            ->get();

        // KPIs de Productos
        $stockPorAlmacen = Almacen::select('almacens.nombre')
            ->join('almacen_parabrisa', 'almacens.id', '=', 'almacen_parabrisa.almacen_id')
            ->join('parabrisas', 'parabrisas.id', '=', 'almacen_parabrisa.parabrisa_id')
            ->selectRaw('parabrisas.descripcion, SUM(almacen_parabrisa.stock) as stock_total')
            ->groupBy('almacens.nombre', 'parabrisas.descripcion')
            ->orderBy('stock_total', 'DESC')
            ->limit(5)
            ->get();


                 $parabrisasMayorMovimiento = Parabrisa::selectRaw('parabrisas.descripcion, (SUM(nota_venta_parabrisa.cantidad) + SUM(nota_compras.cantidad)) as movimiento_total')
            ->leftJoin('nota_venta_parabrisa', 'parabrisas.id', '=', 'nota_venta_parabrisa.parabrisa_id')
            ->leftJoin('nota_compras', 'parabrisas.id', '=', 'nota_compras.parabrisa_id')
            ->groupBy('parabrisas.descripcion')
            ->orderBy('movimiento_total', 'DESC')
            ->get(); 



        // KPIs de Almacén
        $ocupacionAlmacen = Almacen::selectRaw('nombre, capacidad, SUM(almacen_parabrisa.stock) as ocupado')
            ->leftJoin('almacen_parabrisa', 'almacens.id', '=', 'almacen_parabrisa.almacen_id')
            ->groupBy('nombre', 'capacidad')
            ->get();

        // Puedes añadir más KPIs aquí según sea necesario.

        return view('dashboard', compact(
            'cVentasPorMes',
            'CantidadParabrisasVentas',
            'ventasPorMes',
            'totalVentasMensual',
            'totalComprasMensual',
            'almacenesOcupados',
            'totalVentas',
            'ventasPorProducto',
            'ventasPorAlmacen',
            'ingresosTotales',
            'ventasPorEjecutivo',
            'top5ParabrisasVendidos',
            'tasaCrecimientoVentas',
            'totalCompras',
            'comprasPorProducto',
            'comprasPorProveedor',
            'comprasPorAlmacen',
            'stockPorAlmacen',
            'parabrisasMayorMovimiento',
            'parabrisasMenorMovimiento',
            'ocupacionAlmacen'
        ));

        */
    }

    
}
