<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaqueteRequest;
use App\Http\Requests\UpdatePaqueteRequest;
use App\Http\Resources\PaqueteResource;
use App\Models\DetallePaquete;
use App\Models\Paquete;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Paquetes",
 *     description="Endpoints para gestión de paquetes"
 * )
 */
class PaqueteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/paquetes",
     *     summary="Listar paquetes",
     *     description="Obtiene una lista paginada de paquetes con opciones de filtrado",
     *     tags={"Paquetes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de página",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Elementos por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="camioneros_id",
     *         in="query",
     *         description="Filtrar por ID de camionero",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="estados_paquetes_id",
     *         in="query",
     *         description="Filtrar por ID de estado",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de paquetes obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Paquete::class);
        
        $query = Paquete::with(['camionero', 'estado', 'detalles.tipoMercancia']);

        if ($request->filled('camioneros_id')) {
            $query->where('camioneros_id', $request->integer('camioneros_id'));
        }

        if ($request->filled('estados_paquetes_id')) {
            $query->where('estados_paquetes_id', $request->integer('estados_paquetes_id'));
        }

        $perPage = (int) $request->input('per_page', 10);
        $paquetes = $query->paginate($perPage);

        return response()->json([
            'data' => PaqueteResource::collection($paquetes),
            'meta' => [
                'current_page' => $paquetes->currentPage(),
                'per_page' => $paquetes->perPage(),
                'total' => $paquetes->total(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/paquetes",
     *     summary="Crear paquete",
     *     description="Crea un nuevo paquete con sus detalles. Requiere rol admin.",
     *     tags={"Paquetes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"camioneros_id", "estados_paquetes_id", "direccion"},
     *             @OA\Property(property="camioneros_id", type="integer", example=1),
     *             @OA\Property(property="estados_paquetes_id", type="integer", example=1),
     *             @OA\Property(property="direccion", type="string", example="Calle 123 #45-67", maxLength=25),
     *             @OA\Property(property="detalles", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="tipo_mercancia_id", type="integer", example=1),
     *                     @OA\Property(property="dimencion", type="string", example="50x30x40 cm", maxLength=45),
     *                     @OA\Property(property="peso", type="string", example="15 kg", maxLength=45),
     *                     @OA\Property(property="fecha_entrega", type="string", format="date", example="2025-12-01")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Paquete creado exitosamente",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=422, description="Errores de validación"),
     *     @OA\Response(response=401, description="No autenticado"),
     *     @OA\Response(response=403, description="No autorizado - Se requiere rol admin")
     * )
     */
    public function store(StorePaqueteRequest $request): JsonResponse
    {
        $this->authorize('create', Paquete::class);
        
        $paquete = Paquete::create($request->only(['camioneros_id', 'estados_paquetes_id', 'direccion']));

        $detalles = $request->input('detalles', []);
        foreach ($detalles as $detalle) {
            DetallePaquete::create([
                'paquetes_id' => $paquete->id,
                'tipo_mercancia_id' => $detalle['tipo_mercancia_id'],
                'dimencion' => $detalle['dimencion'],
                'peso' => $detalle['peso'],
                'fecha_entrega' => $detalle['fecha_entrega'],
            ]);
        }

        $paquete->load(['camionero', 'estado', 'detalles.tipoMercancia']);

        return response()->json(new PaqueteResource($paquete), 201);
    }

    /**
     * @OA\Get(
     *     path="/paquetes/{id}",
     *     summary="Obtener paquete",
     *     description="Obtiene los detalles de un paquete específico",
     *     tags={"Paquetes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del paquete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paquete obtenido exitosamente",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=404, description="Paquete no encontrado"),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function show(Paquete $paquete): JsonResponse
    {
        $this->authorize('view', $paquete);
        
        $paquete->load(['camionero', 'estado', 'detalles.tipoMercancia']);
        return response()->json(new PaqueteResource($paquete));
    }

    /**
     * @OA\Put(
     *     path="/paquetes/{id}",
     *     summary="Actualizar paquete",
     *     description="Actualiza un paquete existente. Requiere rol admin.",
     *     tags={"Paquetes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del paquete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="camioneros_id", type="integer", example=1),
     *             @OA\Property(property="estados_paquetes_id", type="integer", example=2),
     *             @OA\Property(property="direccion", type="string", example="Nueva Calle 456", maxLength=25),
     *             @OA\Property(property="detalles", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="tipo_mercancia_id", type="integer", example=1),
     *                     @OA\Property(property="dimencion", type="string", example="60x40x50 cm"),
     *                     @OA\Property(property="peso", type="string", example="20 kg"),
     *                     @OA\Property(property="fecha_entrega", type="string", format="date", example="2025-12-05")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paquete actualizado exitosamente",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=404, description="Paquete no encontrado"),
     *     @OA\Response(response=422, description="Errores de validación"),
     *     @OA\Response(response=401, description="No autenticado"),
     *     @OA\Response(response=403, description="No autorizado - Se requiere rol admin")
     * )
     */
    public function update(UpdatePaqueteRequest $request, Paquete $paquete): JsonResponse
    {
        $this->authorize('update', $paquete);
        
        $paquete->update($request->only(['camioneros_id', 'estados_paquetes_id', 'direccion']));

        if ($request->has('detalles')) {
            foreach ($request->input('detalles', []) as $detalle) {
                if (isset($detalle['id'])) {
                    $detalleModel = DetallePaquete::where('id', $detalle['id'])
                        ->where('paquetes_id', $paquete->id)
                        ->first();
                    if ($detalleModel) {
                        $detalleModel->update([
                            'tipo_mercancia_id' => $detalle['tipo_mercancia_id'],
                            'dimencion' => $detalle['dimencion'],
                            'peso' => $detalle['peso'],
                            'fecha_entrega' => $detalle['fecha_entrega'],
                        ]);
                    }
                } else {
                    DetallePaquete::create([
                        'paquetes_id' => $paquete->id,
                        'tipo_mercancia_id' => $detalle['tipo_mercancia_id'],
                        'dimencion' => $detalle['dimencion'],
                        'peso' => $detalle['peso'],
                        'fecha_entrega' => $detalle['fecha_entrega'],
                    ]);
                }
            }
        }

        $paquete->load(['camionero', 'estado', 'detalles.tipoMercancia']);
        return response()->json(new PaqueteResource($paquete));
    }

    /**
     * @OA\Delete(
     *     path="/paquetes/{id}",
     *     summary="Eliminar paquete",
     *     description="Elimina un paquete. Requiere rol admin.",
     *     tags={"Paquetes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del paquete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paquete eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Eliminado")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Paquete no encontrado"),
     *     @OA\Response(response=401, description="No autenticado"),
     *     @OA\Response(response=403, description="No autorizado - Se requiere rol admin")
     * )
     */
    public function destroy(Paquete $paquete): JsonResponse
    {
        $this->authorize('delete', $paquete);
        
        $paquete->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}


