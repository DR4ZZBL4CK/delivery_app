<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="Delivery API",
 *     version="1.0.0",
 *     description="API REST para sistema de gestión de pedidos y entregas",
 *     @OA\Contact(
 *         email="admin@delivery.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Servidor de desarrollo"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class SwaggerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/health-check",
     *     summary="Health Check",
     *     description="Verifica el estado del API",
     *     @OA\Response(
     *         response=200,
     *         description="API funcionando correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="OK"),
     *             @OA\Property(property="version", type="string", example="1.0"),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function healthCheck(): JsonResponse
    {
        return response()->json([
            'status' => 'OK',
            'version' => config('app.version', '1.0'),
            'timestamp' => now(),
        ]);
    }
}
