<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);

//Creamos la coleccion de rutas con el middleware de autenticacion
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('/categories', CategoryController::class);
    Route::apiResource('/post', PostController::class);
});

Route::post('/final-debug-upload', function (Request $request) {
    // 1. Verificar si el archivo llegó
    if (!$request->hasFile('image_test')) {
        return response()->json(['error' => 'Laravel no recibió ningún archivo con la clave image_test. El problema está en Postman.'], 400);
    }

    // 2. Obtener información de configuración del disco
    $diskConfig = config('filesystems.disks.public');

    // 3. Intentar guardar el archivo y capturar CUALQUIER error
    $path = null;
    $errorMessage = null;
    $fileExistsAfter = false;
    $absolutePath = null;

    try {
        $file = $request->file('image_test');
        // Intentamos guardar en un subdirectorio de prueba
        $path = $file->store('public/final-test');

        // Si store() devuelve una ruta, verificamos si el archivo existe FÍSICAMENTE
        if ($path) {
            $fileExistsAfter = Storage::exists($path);
            $absolutePath = Storage::path($path);
        }

    } catch (\Exception $e) {
        // Si cualquier cosa falla (permisos, etc.), capturamos el mensaje exacto del error
        $errorMessage = $e->getMessage();
    }

    // 4. Devolver un reporte completo
    return response()->json([
        'configuracion_del_disco' => $diskConfig,
        'resultado_del_metodo_store' => $path,
        'mensaje_de_error_capturado' => $errorMessage,
        'archivo_existe_fisicamente' => $fileExistsAfter,
        'ruta_absoluta_en_el_disco' => $absolutePath,
    ]);
});