<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Password;
use App\Http\Requests\ForgotRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Exception;
use Log;

class AuthController
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::firstWhere([
            'email' => $request->safe()->email
        ]);

        if (!$user || !\Hash::check($request->safe()->password, $user->password)) {
            return response()->json([
                'error' => 'Credenciais não encontradas',
            ], 401);
        }

        $expiration = now()->addHours(6);

        $token = $user->createToken(
            $request->ip(),
            expiresAt: $expiration
        );

        return response()->json([
            'token' => $token->plainTextToken,
            'expiration' => $expiration,
            'user' => $user->only('id', 'name', 'email')
        ], 200);
    }

    public function forgot(ForgotRequest $request): JsonResponse
    {
        $user = User::firstWhere([
            'email' => $request->safe()->email,
        ]);

        if (!$user) {
            return response()->json([
                'error' => 'Credenciais não encontradas',
            ], 401);
        }

        $status = Password::sendResetLink([
            'email' => $user->email
        ]);

        return $status === Password::ResetLinkSent
            ? response()->json([
                'message' => 'Um email foi enviado para sua conta',
            ], 200)
            : response()->json([
                'error' => 'Falha ao enviar email',
            ], 500);
    }

    public function reset(ResetRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->safe()->only([
                'email',
                'password',
                'password_confirmation',
                'token'
            ]),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => \Hash::make($password),
                ])->save();
                event(new PasswordReset($user));
            }
        );

        return match ($status) {
            Password::PasswordReset => response()->json([
                'message' => 'Senha atualizada com sucesso',
            ], 200),
            Password::InvalidToken => response()->json([
                'error' => 'Solicitação expirada',
            ], 401),
            default => response()->json([
                'error' => 'Falha ao atualizar senha',
            ], 500)
        };
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso',
        ], 200);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            DB::transaction(function () use ($request) {
                User::create($request->validated());
            });

            return response()->json([
                'message' => 'Usuário criado com sucesso',
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => 'Falha ao registrar usuário, tente novamente mais tarde'
            ], 500);
        }
    }

    public function validate(Request $request)
    {
        return response()->json([
            'message' => 'Acesso autorizado!'
        ]);
    }
}
