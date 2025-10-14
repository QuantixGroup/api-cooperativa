<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class AutenticacionDesdeApiUsuarios
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        if ($token == null)
            return response(["error" => "Not authenticated"], 401);

        $apiAuthUrl = env("API_AUTH_URL");
        Log::info("API_AUTH_URL: " . $apiAuthUrl);
        Log::info("Token: " . $token);

        $validacion = Http::withHeaders([
            'Authorization' => $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->get($apiAuthUrl . '/api/validate');

        Log::info("Response status: " . $validacion->status());
        Log::info("Response body: " . $validacion->body());

        if ($validacion->status() != 200) {
            Log::error("Token validation failed with status: " . $validacion->status());
            return response(["error" => "Invalid Token", "status" => $validacion->status()], 401);
        }

        Log::info("Token validated successfully, proceeding to controller");
        $userData = $validacion->json();
        $request->merge(['user' => $userData]);

        // Agregar cedula a attributes para que el controlador pueda accederla
        if (isset($userData['cedula'])) {
            $request->attributes->set('cedula', $userData['cedula']);
        }

        return $next($request);
    }
}