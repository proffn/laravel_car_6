<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Регистрация пользователя через API
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Создание токена 
            $tokenResult = $user->createToken('API Token');
            $token = $tokenResult->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
                'token_expires_in' => '15 days',
                'oauth_info' => [
                    'personal_access_client_id' => 1,
                    'password_grant_client_id' => 2,
                    'note' => 'Используйте этот токен в заголовке: Authorization: Bearer ' . $token,
                    'api_usage_example' => 'Все запросы API требуют заголовок Authorization'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Авторизация через API
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Создание токена 
            $tokenResult = $user->createToken('API Token');
            $token = $tokenResult->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => $user->is_admin,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 60 * 60 * 24 * 15, // 15 дней
                'oauth_guide' => [
                    'header_format' => 'Authorization: Bearer ' . $token,
                    'test_endpoint' => 'GET /api/user',
                    'note' => 'Сохраните этот токен для всех последующих запросов API'
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password'
        ], 401);
    }

    // Выход
    public function logout(Request $request)
    {
        try {
            $token = $request->user()->token();
            
            if ($token) {
                $token->revoke();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
                'note' => 'Токен отозван, для новых запросов потребуется новый вход'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Получить все токены пользователя 
    public function getTokens(Request $request)
    {
        try {
            $user = $request->user();
            $tokens = $user->tokens()->get();
            
            $formattedTokens = $tokens->map(function ($token) {
                $createdAt = $token->created_at;
                $expiresAt = $token->expires_at;
                
                // Безопасное форматирование дат
                $formattedCreatedAt = null;
                $formattedExpiresAt = null;
                
                if ($createdAt) {
                    if ($createdAt instanceof \DateTime || $createdAt instanceof \Carbon\Carbon) {
                        $formattedCreatedAt = $createdAt->format('Y-m-d H:i:s');
                    } elseif (is_string($createdAt)) {
                        $formattedCreatedAt = $createdAt;
                    }
                }
                
                if ($expiresAt) {
                    if ($expiresAt instanceof \DateTime || $expiresAt instanceof \Carbon\Carbon) {
                        $formattedExpiresAt = $expiresAt->format('Y-m-d H:i:s');
                    } elseif (is_string($expiresAt)) {
                        $formattedExpiresAt = $expiresAt;
                    }
                }
                
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'scopes' => $token->scopes,
                    'revoked' => (bool) $token->revoked,
                    'created_at' => $formattedCreatedAt,
                    'expires_at' => $formattedExpiresAt,
                    'is_current' => $request->bearerToken() && $token->id === optional($user->token())->id,
                ];
            });
            
            $currentToken = $user->token();
            
            return response()->json([
                'success' => true,
                'tokens' => $formattedTokens,
                'tokens_count' => $tokens->count(),
                'current_token' => $currentToken ? [
                    'id' => $currentToken->id,
                    'name' => $currentToken->name,
                    'created' => 'Активен',
                    'preview' => substr($request->bearerToken(), 0, 20) . '...'
                ] : null,
                'oauth_clients' => [
                    'personal_access_client_id' => 1,
                    'password_grant_client_id' => 2,
                    'note' => 'Эти ID используются для OAuth2 аутентификации'
                ],
                'api_guide' => [
                    'header_example' => 'Authorization: Bearer {ваш_токен}',
                    'your_token' => $request->bearerToken() ? substr($request->bearerToken(), 0, 30) . '...' : 'Не указан',
                    'protected_endpoints' => ['/api/cars', '/api/comments', '/api/user']
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tokens',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Создать новый токен 
    public function createToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Создание токена 
            $tokenResult = $request->user()->createToken($request->name);
            $token = $tokenResult->accessToken;
            
            $tokenPreview = strlen($token) > 30 ? substr($token, 0, 30) . '...' : $token;

            return response()->json([
                'success' => true,
                'message' => 'Token created successfully',
                'token_name' => $request->name,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => '15 days',
                'usage_instructions' => [
                    'header_format' => 'Authorization: Bearer ' . $tokenPreview,
                    'example_request' => 'GET /api/cars',
                    'note' => 'Сохраните этот токен в безопасном месте'
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Удалить токен
    public function deleteToken(Request $request, $tokenId)
    {
        try {
            $token = $request->user()->tokens()->find($tokenId);
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not found'
                ], 404);
            }
            
            // Нельзя удалить текущий токен через этот метод
            if ($token->id === optional($request->user()->token())->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot revoke current active token. Use /api/logout instead.'
                ], 400);
            }

            $token->revoke();
            
            return response()->json([
                'success' => true,
                'message' => 'Token revoked successfully',
                'revoked_token' => [
                    'id' => $token->id,
                    'name' => $token->name,
                    'was_current' => false
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to revoke token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}