<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCamioneroRequest;
use App\Http\Requests\UpdateCamioneroRequest;
use App\Http\Resources\CamioneroResource;
use App\Models\Camionero;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Camioneros",
 *     description="Endpoints para gestión de camioneros"
 * )
 */
class CamioneroController extends Controller
{
    /**
     * @OA\Get(
     *     path="/camioneros",
     *     summary="Listar camioneros",
     *     description="Obtiene una lista paginada de camioneros con búsqueda",
     *     tags={"Camioneros"},
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
     *         description="Buscar por nombre, apellido, documento o licencia",
     *         required=false,
     *         @OA\Schema(type="string", example="Juan")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de camioneros obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=25)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Camionero::class);
        
        $query = Camionero::with('camiones');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('documento', 'like', "%{$search}%")
                    ->orWhere('licencia', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        $camioneros = $query->paginate($perPage);

        return response()->json([
            'data' => CamioneroResource::collection($camioneros),
            'meta' => [
                'current_page' => $camioneros->currentPage(),
                'per_page' => $camioneros->perPage(),
                'total' => $camioneros->total(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/camioneros",
     *     summary="Crear camionero",
     *     description="Crea un nuevo camionero. Requiere rol admin. El camionero debe ser mayor de 18 años.",
     *     tags={"Camioneros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"documento", "nombre", "apellido", "fecha_nacimiento", "licencia", "telefono"},
     *             @OA\Property(property="documento", type="string", example="1234567890", maxLength=10, minLength=5),
     *             @OA\Property(property="nombre", type="string", example="Juan", maxLength=45, minLength=2),
     *             @OA\Property(property="apellido", type="string", example="Pérez", maxLength=45, minLength=2),
     *             @OA\Property(property="fecha_nacimiento", type="string", format="date", example="1990-01-15"),
     *             @OA\Property(property="licencia", type="string", example="ABC12345", maxLength=10, minLength=5),
     *             @OA\Property(property="telefono", type="string", example="+57 300 1234567", maxLength=15, minLength=7),
     *             @OA\Property(property="camiones", type="array", @OA\Items(type="integer"), example={1, 2})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Camionero creado exitosamente",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=422, description="Errores de validación"),
     *     @OA\Response(response=401, description="No autenticado"),
     *     @OA\Response(response=403, description="No autorizado - Se requiere rol admin")
     * )
     */
    public function store(StoreCamioneroRequest $request): JsonResponse
    {
        $this->authorize('create', Camionero::class);
        
        $camionero = Camionero::create($request->only([
            'documento', 'nombre', 'apellido', 'fecha_nacimiento', 'licencia', 'telefono'
        ]));

        if ($request->has('camiones')) {
            $camionero->camiones()->sync($request->input('camiones'));
        }

        $camionero->load('camiones');

        return response()->json(new CamioneroResource($camionero), 201);
    }

    /**
     * @OA\Get(
     *     path="/camioneros/{id}",
     *     summary="Obtener camionero",
     *     description="Obtiene los detalles de un camionero específico",
     *     tags={"Camioneros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del camionero",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Camionero obtenido exitosamente",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=404, description="Camionero no encontrado"),
     *     @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function show(Camionero $camionero): JsonResponse
    {
        $this->authorize('view', $camionero);
        
        $camionero->load('camiones');
        return response()->json(new CamioneroResource($camionero));
    }

    /**
     * @OA\Put(
     *     path="/camioneros/{id}",
     *     summary="Actualizar camionero",
     *     description="Actualiza un camionero existente. Requiere rol admin.",
     *     tags={"Camioneros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del camionero",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="documento", type="string", example="1234567890"),
     *             @OA\Property(property="nombre", type="string", example="Juan"),
     *             @OA\Property(property="apellido", type="string", example="Pérez"),
     *             @OA\Property(property="fecha_nacimiento", type="string", format="date", example="1990-01-15"),
     *             @OA\Property(property="licencia", type="string", example="ABC12345"),
     *             @OA\Property(property="telefono", type="string", example="+57 300 1234567"),
     *             @OA\Property(property="camiones", type="array", @OA\Items(type="integer"), example={1, 2})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Camionero actualizado exitosamente",
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=404, description="Camionero no encontrado"),
     *     @OA\Response(response=422, description="Errores de validación"),
     *     @OA\Response(response=401, description="No autenticado"),
     *     @OA\Response(response=403, description="No autorizado - Se requiere rol admin")
     * )
     */
    public function update(UpdateCamioneroRequest $request, Camionero $camionero): JsonResponse
    {
        $this->authorize('update', $camionero);
        
        $camionero->update($request->only([
            'documento', 'nombre', 'apellido', 'fecha_nacimiento', 'licencia', 'telefono'
        ]));

        if ($request->has('camiones')) {
            $camionero->camiones()->sync($request->input('camiones'));
        }

        $camionero->load('camiones');

        return response()->json(new CamioneroResource($camionero));
    }

    /**
     * @OA\Delete(
     *     path="/camioneros/{id}",
     *     summary="Eliminar camionero",
     *     description="Elimina un camionero. Requiere rol admin.",
     *     tags={"Camioneros"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del camionero",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Camionero eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Eliminado")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Camionero no encontrado"),
     *     @OA\Response(response=401, description="No autenticado"),
     *     @OA\Response(response=403, description="No autorizado - Se requiere rol admin")
     * )
     */
    public function destroy(Camionero $camionero): JsonResponse
    {
        $this->authorize('delete', $camionero);
        
        $camionero->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
