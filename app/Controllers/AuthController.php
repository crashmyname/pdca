<?php

namespace App\Controllers;

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

    public function register(Request $request, AuthService $service)
    {
        $result = $service->register($request->all());

        return Response::json([
            'status'  => $result['status'],
            'message' => $result['message'] ?? 'success',
            'data'    => $result['data'] ?? null,
        ], $result['status']);
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
