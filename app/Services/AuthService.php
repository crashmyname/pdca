<?php

namespace App\Services;

use App\Models\User;
use Bpjs\Framework\Core\Request;
use Bpjs\Framework\Helpers\Auth;

class AuthService
{
    // Service logic here
    public function login(array $data)
    {
        $credentials = [
            'identifier' => $data['username'],
            'password' => $data['password']
        ];
        $user = User::query()->where('username','=',$data['username'])->first();
        if(!$user){
            return [
                'status' => 404,
                'message' => 'Username not found'
            ];
        }
        if(Auth::attempt($credentials)){
            $userData = [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'role'     => strtolower($user->role),
            ];
            if(Request::isAjax()){       
                return ['status'=>200,'message'=>'Berhasil login','data'=>$userData];
            }
            return ['status'=>200,'message'=>'Berhasil login','data'=>$userData];
        } else {
            return [
                'status' => 400,
                'message' => 'Username atau password salah'
            ];
        }
    }

    public function register(array $data)
    {
        // ---- Validasi ----
        $name     = trim($data['name']     ?? '');
        $username = trim($data['username'] ?? '');
        $password = (string)($data['password'] ?? '');
        $role     = strtolower($data['role'] ?? 'operator');

        if ($name === '' || $username === '' || $password === '') {
            return ['status' => 422, 'message' => 'Nama, username, dan password wajib diisi'];
        }
        if (strlen($username) < 3) {
            return ['status' => 422, 'message' => 'Username minimal 3 karakter'];
        }
        if (strlen($password) < 6) {
            return ['status' => 422, 'message' => 'Password minimal 6 karakter'];
        }

        // ---- Cek username unik ----
        $exists = User::query()->where('username', '=', $username)->first();
        if ($exists) {
            return ['status' => 409, 'message' => 'Username sudah dipakai'];
        }

        // ---- Buat user ----
        try {
            $user = User::create([
                'name'     => $name,
                'username' => $username,
                'unique_user' => $username . uniqid(),
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role'     => $role,
            ]);

            // Ambil ID user baru (object / array)
            $userId = is_object($user) ? ($user->id ?? null)
                    : (is_array($user)  ? ($user['id'] ?? null) : null);

            return [
                'status'  => 201,
                'message' => 'Registrasi berhasil',
                'data'    => [
                    'id'       => $userId,
                    'name'     => $name,
                    'username' => $username,
                    'role'     => $role,
                ],
            ];

        } catch (\Throwable $e) {
            error_log('[AuthService::register] ' . $e->getMessage()
                . ' @ ' . $e->getFile() . ':' . $e->getLine());

            return ['status' => 500, 'message' => 'Gagal membuat akun: ' . $e->getMessage()];
        }
    }
}
