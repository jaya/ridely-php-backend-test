<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Autenticação via Keycloak"
 * )
 */
class AuthController extends Controller
{
    protected string $keycloakBaseUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->keycloakBaseUrl = sprintf("%s/protocol/openid-connect", config('keycloak.realm_url'));
        $this->clientId = config('keycloak.client_id');
        $this->clientSecret = config('keycloak.client_secret');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="Realiza login via Keycloak",
     *     description="Autentica o usuário usando grant_type=password no Keycloak.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username","password"},
     *             @OA\Property(property="username", type="string", example="anderson.contreira"),
     *             @OA\Property(property="password", type="string", example="123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login bem-sucedido",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="refresh_token", type="string"),
     *             @OA\Property(property="expires_in", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas"
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        Log::debug("target: {$this->keycloakBaseUrl}/token");
        Log::debug("username: $username");
        try {
            $response = Http::asForm()->post("{$this->keycloakBaseUrl}/token", [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username' => $username,
                'password' => $request->input('password'),
            ]);

            return response()->json($response->json());
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("Keycloak login error: " . $e->getMessage() . " Response: " . $e->response->body());
            return response()->json([
                'error' => 'Authentication failed',
                'details' => $e->response->json() ?? $e->getMessage()
            ], $e->response->status());
        } catch (\Throwable $e) {
            Log::error("Unexpected error during Keycloak login: " . $e->getMessage());
            return response()->json([
                'error' => 'An unexpected error occurred.',
                'details' => $e->getMessage()
            ], 500);
        }

    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh-token",
     *     tags={"Auth"},
     *     summary="Renova o token de acesso",
     *     description="Utiliza um refresh_token válido para renovar o token de acesso.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *             @OA\Property(property="refresh_token", type="string", example="REFRESH_TOKEN_HERE")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token renovado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="refresh_token", type="string"),
     *             @OA\Property(property="expires_in", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Refresh token inválido ou expirado"
     *     )
     * )
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        try {
            $response = Http::asForm()->post("{$this->keycloakBaseUrl}/token", [
                'grant_type' => 'refresh_token',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $request->input('refresh_token'),
            ]);

            $response->throw();

            return response()->json($response->json());

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("Keycloak refresh token error: " . $e->getMessage() . " Response: " . $e->response->body());
            return response()->json([
                'error' => 'Token refresh failed',
                'details' => $e->response->json() ?? $e->getMessage()
            ], $e->response->status());
        } catch (\Throwable $e) {
            Log::error("Unexpected error during Keycloak token refresh: " . $e->getMessage());
            return response()->json([
                'error' => 'An unexpected error occurred.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Auth"},
     *     summary="Realiza logout no Keycloak",
     *     description="Finaliza a sessão do usuário no Keycloak usando o refresh_token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *             @OA\Property(property="refresh_token", type="string", example="REFRESH_TOKEN_HERE")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Logout bem-sucedido"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Refresh token inválido ou erro de autenticação"
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string', // Refresh token é necessário para o logout
        ]);

        try {
            $response = Http::asForm()->post("{$this->keycloakBaseUrl}/logout", [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $request->input('refresh_token'),
            ]);

            $response->throw();

            // Keycloak retorna 204 No Content para logout bem-sucedido
            return response()->json(null, 204);

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("Keycloak logout error: " . $e->getMessage() . " Response: " . $e->response->body());
            return response()->json([
                'error' => 'Logout failed',
                'details' => $e->response->json() ?? $e->getMessage()
            ], $e->response->status());
        } catch (\Throwable $e) {
            Log::error("Unexpected error during Keycloak logout: " . $e->getMessage());
            return response()->json([
                'error' => 'An unexpected error occurred.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
