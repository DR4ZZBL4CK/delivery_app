<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
        $token = session('api_token');
        
        if (!$token) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get($this->apiBaseUrl . '/paquetes', $request->all());

        return response()->json($response->json(), $response->status());
    }

    public function getCamioneros(Request $request): JsonResponse
    {
        $token = session('api_token');
        
        if (!$token) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get($this->apiBaseUrl . '/camioneros', $request->all());

        return response()->json($response->json(), $response->status());
    }

    public function getCamiones(Request $request): JsonResponse
    {
        $token = session('api_token');
        
        if (!$token) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get($this->apiBaseUrl . '/camiones', $request->all());

        return response()->json($response->json(), $response->status());
    }
}
