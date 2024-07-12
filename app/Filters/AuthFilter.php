<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\Config\Services;
use App\Models\UserModel;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getHeader('Authorization');
        if (!$authHeader) {
            return Services::response()
                ->setJSON(['status' => 401, 'message' => 'Authorization header missing'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $token = str_replace('Bearer ', '', $authHeader->getValue());

        try {
            $decoded = JWT::decode($token, new Key(getenv('ACCESS_TOKEN_KEY'), getenv('JWT_ALG')));
            $userId = $decoded->data->id;

            $userModel = new UserModel();
            $user = $userModel->select('users.*, title')->join('roles', 'users.role_id = roles.id')->find($userId);

            if (!$user) {
                return Services::response()
                    ->setJSON(['status' => 401, 'message' => 'User not found'])
                    ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }

            // Check if user role is in allowed roles
            if (!empty($arguments)) {
                // $allowedRoles = explode('|', $arguments[0]);
                $allowedRoles = $arguments;
                if (!in_array(strtolower($user['title']), $allowedRoles)) {
                    return Services::response()
                        ->setJSON(['status' => 403, 'message' => 'Access forbidden for your role'])
                        ->setStatusCode(ResponseInterface::HTTP_FORBIDDEN);
                }
            }
        } catch (\Exception $e) {
            return Services::response()
                ->setJSON(['status' => 401, 'message' => 'Invalid token'])
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here if needed
    }
}
