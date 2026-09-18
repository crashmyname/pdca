<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Bpjs\Framework\Helpers\Auth;
use Bpjs\Framework\Helpers\BaseController;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\Response;
use Bpjs\Framework\Helpers\View;
use Middlewares\SessionMiddleware;

class AuthController extends BaseController
{
    // Controller logic here
    public function login(Request $request, AuthService $service)
    {
        $login = $service->login($request->all());
        return Response::json([
            'status' => $login['status'],
            'message' => $login['message'] ?? 'success',
            'data' => $login['data'] ?? null
        ],$login['status']);
    }

    public function me(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->json(['error' => 'Unauthenticated'], 401);
        }

        return $this->json([
            'user' => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'role'     => $user->role,
                'email'    => $user->email ?? '',
            ],
        ], 200);
    }

    public function register(Request $request, AuthService $service)
    {
        $result = $service->register($request->all());

        return Response::json([
            'status'  => $result['status'],
            'message' => $result['message'] ?? 'success',
            'data'    => $result['data'] ?? null,
        ], $result['status']);
    }

    public function update(Request $req)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->json(['message' => 'Unauthorized'], 401);
        }

        $me = User::find($user->id);
        if (!$me) {
            return $this->json(['message' => 'User tidak ditemukan'], 404);
        }

        $req->validate([
            'name'     => 'required|min:2',
            'username' => 'required|min:3',
        ]);

        $name     = trim($req->input('name'));
        $username = trim($req->input('username'));
        $oldPw    = $req->input('old_password');
        $newPw    = $req->input('new_password');

        $dup = User::query()
            ->where('username', '=', $username)
            ->where('id', '!=', $me->id)
            ->first();

        if ($dup) {
            return $this->json(['message' => 'Username sudah dipakai user lain'], 422);
        }

        if ($oldPw || $newPw) {
            if (!$oldPw || !$newPw) {
                return $this->json(['message' => 'Password lama & baru wajib diisi'], 422);
            }
            if (!password_verify($oldPw, $me->password)) {
                return $this->json(['message' => 'Password lama salah'], 422);
            }
            if (strlen($newPw) < 6) {
                return $this->json(['message' => 'Password baru minimal 6 karakter'], 422);
            }
            $me->password = password_hash($newPw, PASSWORD_BCRYPT);
        }

        $me->name     = $name;
        $me->username = $username;
        $me->save();

        return $this->json([
            'message' => 'Profile berhasil diupdate',
            'user' => [
                'id'       => $me->id,
                'name'     => $me->name,
                'username' => $me->username,
                'role'     => strtolower($me->role ?? 'leader'),
            ]
        ], 200);
    }

    public function logout()
    {
        Auth::logout();
        SessionMiddleware::destroy();
        if(Request::isAjax()){
            return Response::json([
                'status' => 200,
                'message' => 'Berhasil logout'
            ]);
        }
        return redirect('');
    }
}
