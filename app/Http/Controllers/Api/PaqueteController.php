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

class PaqueteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
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

    public function store(StorePaqueteRequest $request): JsonResponse
    {
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

    public function show(Paquete $paquete): JsonResponse
    {
        $paquete->load(['camionero', 'estado', 'detalles.tipoMercancia']);
        return response()->json(new PaqueteResource($paquete));
    }

    public function update(UpdatePaqueteRequest $request, Paquete $paquete): JsonResponse
    {
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

    public function destroy(Paquete $paquete): JsonResponse
    {
        $paquete->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}


