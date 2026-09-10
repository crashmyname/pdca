<?php
namespace Middlewares;

use App\Models\User;
use Bpjs\Framework\Helpers\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Bpjs\Framework\Helpers\View;

class Middleware
{
    public function handle()
    {
        if (!$this->checkToken()) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(['error' => 'Token tidak valid atau tidak ditemukan']);
            exit();
        }
    }

    public function checkToken()
    {
        $headers = getallheaders();

        $cookieToken = cookie_get('token');
        if ($cookieToken) {
            return $this->validateJWT($cookieToken);
        }

        if (!isset($headers['Authorization'])) {
            header('Content-Type: application/json');
            header("HTTP/1.1 401 Unauthorized");
            Response::json([
                'status' => 400,
                'message' => 'Authorization token tidak ditemukan',
            ],400);
            return false;
        }

        $authHeader = $headers['Authorization'];
        $token = substr($authHeader, 7);

        if (strlen($token) > 128) {
            if (!$this->validateJWT($token)) {
                return false;
            }
        } else {
            if (!$this->validateBearer($token)) {
                return false;
            }
        }

        if (!isset($headers['api_key'])) {
            header('Content-Type: application/json');
            header("HTTP/1.1 401 Unauthorized");
            Response::json([
                'status' => 400,
                'message' => 'API Token tidak ditemukan',
            ],400);
            return false;
        }

        $apiToken = $headers['api_key'];
        if (!$this->validateApiToken($apiToken)) {
            return false;
        }

        return true;
    }

    private function validateBearer($token)
    {
        if (!isset($_SESSION['token']) || $_SESSION['token'] !== $token) {
            header('Content-Type: application/json');
            header("HTTP/1.1 401 Unauthorized");
            Response::json([
                'status' => 400,
                'message' => 'Bearer Token tidak valid',
            ],400);
            return false;
        }

        return true;
    }

    private function validateJWT($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $_SESSION['user'] = (array) $decoded;
            if (isset($decoded->sign, $decoded->sub)) {
                $expectedSign = hash_hmac('SHA256', $decoded->sub . env('JWT_SECRET'), env('JWT_SECRET'));

                if (!hash_equals($expectedSign, $decoded->sign)) {
                    Response::json([
                        'status' => 401,
                        'message' => 'JWT sign tidak valid'
                    ], 401);
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            Response::json([
                'status' => 401,
                'message' => 'JWT Token tidak valid: ' . $e->getMessage()
            ], 401);
            return false;
        }
    }

    private function validateApiToken($apiToken)
    {
        $user = User::query()->where('api_key','=',$apiToken)->first();
        if(!$user){
            return Response::json([
                'status' => 404,
                'message' => 'User not found'
            ],404);
        }
        if ($apiToken !== $user->api_key) { 
            header('Content-Type: application/json');
            header("HTTP/1.1 401 Unauthorized");
            Response::json([
                'status' => 400,
                'message' => 'API Token tidak valid',
            ],400);
            return false;
        }

        return true;
    }
}

