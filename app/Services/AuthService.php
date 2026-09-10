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
}
