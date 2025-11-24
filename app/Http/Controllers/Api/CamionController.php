<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCamionRequest;
use App\Http\Requests\UpdateCamionRequest;
use App\Http\Resources\CamionResource;
use App\Models\Camion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Camiones",
 *     description="Endpoints para gestión de camiones"
 * )
 */
class CamionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/camiones",
     *     summary="Listar camiones",
     *     description="Obtiene una lista paginada de camiones con búsqueda",
     *     tags={"Camiones"},
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
     *         name="search",
     *         in="query",
     *         description="Buscar por placa o modelo",
     *         required=false,
     *         @OA\Schema(type="string", example="ABC123")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de camiones obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=15)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Camion::class);
        
        $query = Camion::with('camioneros');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('placa', 'like', "%{$search}%")
                    ->orWhere('modelo', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $camiones = $query->paginate($perPage);

        return response()->json([
            'data' => CamionResource::collection($camiones),
            'meta' => [
                'current_page' => $camiones->currentPage(),
                'per_page' => $camiones->perPage(),
                'total' => $camiones->total(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/camiones",
     *     summary="Crear camión",
     *     description="Crea un nuevo camión. Requiere rol admin.",
     *     tags={"Camiones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"placa", "modelo"},
     *             @OA\Property(property="placa", type="string", example="ABC123", maxLength=10, minLength=5),
     *             @OA\Property(property="modelo", type="string", example="2020", maxLength=10, minLength=2),
     *             @OA\Property(property="camioneros", type="array", @OA\Items(type="integer"), example={1, 2})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Camión creado exitosamente",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=422, description="Errores de validación"),
     *     @OA\Response(response=401, description="No autenticado"),
     *     @OA\Response(response=403, description="No autorizado - Se requiere rol admin")
     * )
     */
    public function store(StoreCamionRequest $request): JsonResponse
    {
        $this->authorize('create', Camion::class);
        
        $camion = Camion::create($request->only(['placa', 'modelo']));

        if ($request->has('camioneros')) {
            $camion->camioneros()->sync($request->input('camioneros'));
        }

        $camion->load('camioneros');

        return response()->json(new CamionResource($camion), 201);
    }

    /**
     * @OA\Get(
     *     path="/camiones/{id}",
     *     summary="Obtener camión",
     *     description="Obtiene los detalles de un camión específico",
     *     tags={"Camiones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del camión",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Camión obtenido exitosamente",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=404, description="Camión no encontrado"),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function show(Camion $camion): JsonResponse
    {
        $this->authorize('view', $camion);
        
        $camion->load('camioneros');
        return response()->json(new CamionResource($camion));
    }

    /**
     * @OA\Put(
     *     path="/camiones/{id}",
     *     summary="Actualizar camión",
     *     description="Actualiza un camión existente. Requiere rol admin.",
     *     tags={"Camiones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del camión",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="placa", type="string", example="XYZ789"),
     *             @OA\Property(property="modelo", type="string", example="2021"),
     *             @OA\Property(property="camioneros", type="array", @OA\Items(type="integer"), example={1, 2})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Camión actualizado exitosamente",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=404, description="Camión no encontrado"),
     *     @OA\Response(response=422, description="Errores de validación"),
     *     @OA\Response(response=401, description="No autenticado"),
     *     @OA\Response(response=403, description="No autorizado - Se requiere rol admin")
     * )
     */
    public function update(UpdateCamionRequest $request, Camion $camion): JsonResponse
    {
        $this->authorize('update', $camion);
        
        $camion->update($request->only(['placa', 'modelo']));

        if ($request->has('camioneros')) {
            $camion->camioneros()->sync($request->input('camioneros'));
        }

        $camion->load('camioneros');

        return response()->json(new CamionResource($camion));
    }

    /**
     * @OA\Delete(
     *     path="/camiones/{id}",
     *     summary="Eliminar camión",
     *     description="Elimina un camión. Requiere rol admin.",
     *     tags={"Camiones"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del camión",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Camión eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Eliminado")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Camión no encontrado"),
     *     @OA\Response(response=401, description="No autenticado"),
     *     @OA\Response(response=403, description="No autorizado - Se requiere rol admin")
     * )
     */
    public function destroy(Camion $camion): JsonResponse
    {
        $this->authorize('delete', $camion);
        
        $camion->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
