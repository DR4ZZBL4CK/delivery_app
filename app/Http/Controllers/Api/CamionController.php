<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCamionRequest;
use App\Http\Requests\UpdateCamionRequest;
use App\Http\Resources\CamionResource;
use App\Models\Camion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CamionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
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

    public function store(StoreCamionRequest $request): JsonResponse
    {
        $camion = Camion::create($request->only(['placa', 'modelo']));

        if ($request->has('camioneros')) {
            $camion->camioneros()->sync($request->input('camioneros'));
        }

        $camion->load('camioneros');

        return response()->json(new CamionResource($camion), 201);
    }

    public function show(Camion $camion): JsonResponse
    {
        $camion->load('camioneros');
        return response()->json(new CamionResource($camion));
    }

    public function update(UpdateCamionRequest $request, Camion $camion): JsonResponse
    {
        $camion->update($request->only(['placa', 'modelo']));

        if ($request->has('camioneros')) {
            $camion->camioneros()->sync($request->input('camioneros'));
        }

        $camion->load('camioneros');

        return response()->json(new CamionResource($camion));
    }

    public function destroy(Camion $camion): JsonResponse
    {
        $camion->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
