<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AutenticacionDesdeApiUsuarios
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorizationHeader = $request->header('Authorization');
        if (empty($authorizationHeader)) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $token = preg_replace('/^Bearer\s+/i', '', trim($authorizationHeader));
        if (empty($token)) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $apiAuthUrl = config('services.api_auth_url', env('API_AUTH_URL'));
        if (empty($apiAuthUrl)) {
            Log::error('AutenticacionDesdeApiUsuarios: API_AUTH_URL no está configurada');

            return response()->json(['error' => 'Authentication service not configured'], 500);
        }
        $parsed = parse_url($apiAuthUrl);
        if (!isset($parsed['scheme'])) {
            $apiAuthUrl = 'http://' . ltrim($apiAuthUrl, '/');
        }

        $validateUrl = rtrim($apiAuthUrl, '/') . '/api/validate';

        try {
            $validacion = Http::withHeaders([
                'Accept' => 'application/json',
            ])->withToken($token)->timeout(5)->get($validateUrl);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('AutenticacionDesdeApiUsuarios: fallo de conexión a API de usuarios', ['url' => $validateUrl, 'exception' => $e->getMessage()]);

            return response()->json(['error' => 'Unable to contact authentication server'], 503);
        } catch (\Throwable $e) {
            Log::error('AutenticacionDesdeApiUsuarios: excepción al validar token', ['url' => $validateUrl, 'exception' => $e->getMessage()]);

            return response()->json(['error' => 'Authentication validation error'], 500);
        }

        if (!$validacion->successful()) {
            $status = $validacion->status() ?: 401;
            $body = $validacion->json();
            $message = is_array($body) && isset($body['error']) ? $body['error'] : 'Invalid token';

            return response()->json(['error' => $message, 'status' => $status], $status == 200 ? 401 : $status);
        }

        $userData = $validacion->json();
        $request->attributes->set('user', $userData);

        if (is_array($userData) && isset($userData['cedula'])) {
            $request->attributes->set('cedula', $userData['cedula']);
        }

        return $next($request);
    }
}
