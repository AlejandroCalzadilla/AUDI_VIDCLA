<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,
    ]);
});

Route::post('/logout', function (Request $request) {
    $request->user()->tokens()->delete();

    return response()->json(['message' => 'Logged out successfully']);
})->middleware('auth:sanctum');






Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/todas-las-ventas', [DashboardController::class, 'todasLasVentas']);
    Route::get('/todas-las-compras', [DashboardController::class, 'todasLasCompras']);
    Route::get('/todos-los-almacenes', [DashboardController::class, 'todosLosAlmacenes']);
    Route::get('/todos-los-parabrisas', [DashboardController::class, 'todosLosParabrisas']);
    Route::get('/todos-los-clientes', [DashboardController::class, 'todosLosClientes']);
    Route::get('/todos-los-proveedores', [DashboardController::class, 'todosLosProveedores']);
    Route::get('/almacenparabrisa', [DashboardController::class, 'almacenparabrisa']);
    Route::get('/detalle-nota-venta', [DashboardController::class, 'detallenotaventa']);

});