<?php

namespace App\Http\Controllers;

use App\Models\User;
use DB;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Log;

class AuthController
{
    public function login(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], $this->validationMessages());

        if ($validation->fails()) {
            return response()->json([
                'error' => 'Erro de validação',
                'errors' => $validation->errors()
            ], 400);
        }

        $user = User::firstWhere(['email' => $request->email]);

        if (!$user || !\Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Credenciais não encontradas',
            ], 401);
        }

        $remainingTime = now()->addHours(6);

        $token = $user->createToken($request->ip(), expiresAt: $remainingTime);

        return response()->json([
            'token' => $token->plainTextToken,
            'remaining' => $remainingTime,
            'user' => $user->only('id', 'name', 'email')
        ], 200);
    }

    public function forgot(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ], $this->validationMessages());

        if ($validation->fails()) {
            return response()->json([
                'error' => 'Erro de validação',
                'errors' => $validation->errors()
            ], 400);
        }

        $user = User::firstWhere(['email' => $request->email]);

        if (!$user) {
            return response()->json([
                'error' => 'Credenciais não encontradas',
            ], 401);
        }

        $status = PasswordFacade::sendResetLink([
            'email' => $user->email
        ]);

        return $status === PasswordFacade::ResetLinkSent
            ? response()->json([
                'message' => 'Um email foi enviado para sua conta',
            ], 200)
            : response()->json([
                'error' => 'Falha ao enviar email',
            ], 500);
    }

    public function reset(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'email' => ['email', 'required'],
            'token' => ['required'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], $this->validationMessages());

        if ($validation->fails()) {
            return response()->json([
                'error' => 'Erro de validação',
                'errors' => $validation->errors()
            ], 400);
        }

        $status = PasswordFacade::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => \Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return match ($status) {
            PasswordFacade::PasswordReset => response()->json([
                'message' => 'Senha atualizada com sucesso',
            ], 200),
            PasswordFacade::InvalidToken => response()->json([
                'error' => 'Solicitação expirada',
            ], 401),
            default => response()->json([
                'error' => 'Falha ao atualizar senha',
            ], 500)
        };
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso',
        ], 200);
    }

    public function register(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['email', 'required'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], $this->validationMessages());

        if ($validation->fails()) {
            return response()->json([
                'error' => 'Erro de validação',
                'errors' => $validation->errors()
            ]);
        }

        try {
            DB::transaction(function () use ($validation) {
                User::create($validation->validated());
            });

            $status = PasswordFacade::sendResetLink([
                'email' => $request->email
            ]);

            return $status === PasswordFacade::ResetLinkSent
                ? response()->json([
                    'message' => 'Um email foi enviado para sua conta',
                ], 200)
                : response()->json([
                    'error' => 'Falha ao enviar email',
                ], 500);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'error' => 'Falha ao registrar usuário, tente novamente mais tarde'
            ], 500);
        }
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    private function validationMessages(): array
    {
        return [
            'email.required' => 'Preencha seu email',
            'email.email' => 'O email está inválido',
            'password.required' => 'Preencha a senha',
            'password.min' => 'A senha precisa ter ao menos 8 caracteres',
            'password.letters' => 'A senha precisa ter ao menos uma letra',
            'password.numbers' => 'A senha precisa ter ao menos um número',
            'password.confirmed' => 'Senha de confirmação diferente',
        ];
    }
}
