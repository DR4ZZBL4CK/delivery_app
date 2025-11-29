<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\CamionController;
use App\Http\Controllers\Api\CamioneroController;
use App\Http\Controllers\Api\PaqueteController;

class FrontendController extends Controller
{
    private string $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.url') . '/api';
    }

    public function login(Request $request): JsonResponse
    {
        $response = Http::post($this->apiBaseUrl . '/auth/login', [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        if ($response->successful()) {
            $data = $response->json();
            session(['api_token' => $data['token']]);
            return response()->json($data);
        }

        return response()->json($response->json(), $response->status());
    }

    public function logout(): JsonResponse
    {
        $token = session('api_token');
        
        if ($token) {
            Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->post($this->apiBaseUrl . '/auth/logout');
        }

        session()->forget('api_token');
        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function getPaquetes(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        return app(PaqueteController::class)->index($request);
    }

    public function getCamioneros(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        return app(CamioneroController::class)->index($request);
    }

    public function getCamiones(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        return app(CamionController::class)->index($request);
    }

    public function createCamion(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        // Crear una nueva instancia de la solicitud con los datos del request actual
        $storeRequest = \App\Http\Requests\StoreCamionRequest::createFrom($request);
        $storeRequest->setContainer(app());
        return app(CamionController::class)->store($storeRequest);
    }

    public function deleteCamion($id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        $camion = \App\Models\Camion::findOrFail($id);
        return app(CamionController::class)->destroy($camion);
    }

    public function createCamionero(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        // Crear una nueva instancia de la solicitud con los datos del request actual
        $storeRequest = \App\Http\Requests\StoreCamioneroRequest::createFrom($request);
        $storeRequest->setContainer(app());
        return app(CamioneroController::class)->store($storeRequest);
    }

    public function deleteCamionero($id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        $camionero = \App\Models\Camionero::findOrFail($id);
        return app(CamioneroController::class)->destroy($camionero);
    }

    public function createPaquete(Request $request): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        // Crear una nueva instancia de la solicitud con los datos del request actual
        $storeRequest = \App\Http\Requests\StorePaqueteRequest::createFrom($request);
        $storeRequest->setContainer(app());
        return app(PaqueteController::class)->store($storeRequest);
    }

    public function deletePaquete($id): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }
        $paquete = \App\Models\Paquete::findOrFail($id);
        return app(PaqueteController::class)->destroy($paquete);
    }
}
