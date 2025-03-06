<?php

namespace App\Http\Controllers;

use App\Helpers\Res;
use App\Models\User;
use Illuminate\Http\Request;
use DB;
use Exception;
use Hash;
use Log;
use Validator;

class AuthController
{
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($validation->fails()) {           
            return response()->json([
                'error' => 'Erro de validação',
                'errors' => $validation->errors()
            ], 400);
        }

        $user = User::firstWhere('email', $request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
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
        $validation = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Digite um email!',
            'email.email' => 'Digite um email válido!',
        ]);

        if ($validation->fails()) {
            return Msg::validationErrors($validation->errors());
        }

        $user = User::firstWhere('email', $request->email);
        if (!$user) {
            return Msg::message(401, 'Falha ao enviar solicitação!');
        }

        $existingToken = PasswordResetToken::where('email', $user->email);

        $token = new PasswordResetToken([
            'email' => $user->email,
            'token' => PasswordResetToken::generate(),
            'expiration' => now()->addHours(6)
        ]);

        try {
            DB::transaction(function () use ($existingToken, $token) {
                $existingToken->delete();
                $token->save();
            });
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return Msg::serverError($e->getMessage());
        }

        try {
            Mail::to($user)->send(
                new Forgot($user->name, $token->token)
            );
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return Msg::serverError($e->getMessage());
        }

        return Msg::message(200, 'Solicitação enviada!');
    }

    public function reset(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'password' => ['min:8', 'required', 'confirmed'],
        ], [
            'password.required' => 'Digite uma senha!',
            'password.confirmed' => 'Senhas diferentes!',
            'password.min' => 'A senha precisa ter ao menos 8 caracteres!',
        ]);

        if ($validation->fails()) {
            return Msg::validationErrors($validation->errors());
        }

        $token = PasswordResetToken::firstWhere('token', $request->token);

        if (!$token || $token->expiration < now()) {
            return Msg::message(401, 'Solicite outro token de senha!');
        }

        $user = User::firstWhere('email', $token->email);

        $user->password = $request->password;

        try {
            DB::transaction(function () use ($user, $token) {
                $user->save();
                $token->delete();
            });
        } catch (Exception $e) {
            Log::info($e->getMessage());
            return Msg::serverError($e->getMessage());
        }

        return Msg::message(200, 'Senha alterada com sucesso!');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return Msg::message(200, 'Logout feito com sucesso!');
    }
}
