<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCamioneroRequest;
use App\Http\Requests\UpdateCamioneroRequest;
use App\Http\Resources\CamioneroResource;
use App\Models\Camionero;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CamioneroController extends Controller
{
    public function index(Request $request): JsonResponse
    {
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

    public function store(StoreCamioneroRequest $request): JsonResponse
    {
        $camionero = Camionero::create($request->only([
            'documento', 'nombre', 'apellido', 'fecha_nacimiento', 'licencia', 'telefono'
        ]));

        if ($request->has('camiones')) {
            $camionero->camiones()->sync($request->input('camiones'));
        }

        $camionero->load('camiones');

        return response()->json(new CamioneroResource($camionero), 201);
    }

    public function show(Camionero $camionero): JsonResponse
    {
        $camionero->load('camiones');
        return response()->json(new CamioneroResource($camionero));
    }

    public function update(UpdateCamioneroRequest $request, Camionero $camionero): JsonResponse
    {
        $camionero->update($request->only([
            'documento', 'nombre', 'apellido', 'fecha_nacimiento', 'licencia', 'telefono'
        ]));

        if ($request->has('camiones')) {
            $camionero->camiones()->sync($request->input('camiones'));
        }

        $camionero->load('camiones');

        return response()->json(new CamioneroResource($camionero));
    }

    public function destroy(Camionero $camionero): JsonResponse
    {
        $camionero->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
