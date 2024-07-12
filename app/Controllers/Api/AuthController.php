<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\I18n\Time;
use App\Models\UserModel;

class AuthController extends ResourceController
{
    private $jwtAlg;
    private $accessTokenKey;
    private $refreshTokenKey;
    private $accessTokenExpired;
    private $refreshTokenExpired;
    private $userModel;

    public function __construct()
    {
        $this->jwtAlg = ($algo = getenv('JWT_ALG')) ? $algo : 'HS256';
        $this->accessTokenKey = getenv('ACCESS_TOKEN_KEY');
        $this->refreshTokenKey = getenv('REFRESH_TOKEN_KEY');
        $this->accessTokenExpired = getenv('ACCESS_TOKEN_EXPIRY');
        $this->refreshTokenExpired = getenv('REFRESH_TOKEN_EXPIRY');
        $this->userModel = new UserModel();
    }

    public function login()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        $user = $this->userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            $issuedAt = Time::now()->getTimestamp();
            $expirationTime = $issuedAt +  $this->accessTokenExpired;
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'data' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                ]
            ];

            $accessToken = JWT::encode($payload, $this->accessTokenKey, $this->jwtAlg);

            $expirationTime = $issuedAt + $this->refreshTokenExpired;
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'data' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                ]
            ];

            $refreshToken = JWT::encode($payload, $this->refreshTokenKey, $this->jwtAlg);

            return $this->respond([
                'status' => 200,
                'message' => 'Login successful',
                'data' => [
                    'accessToken' => $accessToken,
                    'refreshToken' => $refreshToken,
                ]
            ]);
        } else {
            return $this->respond([
                'status' => 401,
                'message' => 'Invalid credentials'
            ], 401);
        }
    }

    public function refreshToken()
    {

        $refreshToken = $this->request->getVar('refreshToken');

        if (!$refreshToken) {
            return $this->respond([
                'status' => 403,
                'message' => 'Please send refreshToken'
            ], 403);
        }

        try {
            $decoded = JWT::decode($refreshToken, new Key($this->refreshTokenKey, $this->jwtAlg));
            $issuedAt = Time::now()->getTimestamp();

            $expirationTime = $issuedAt +  $this->accessTokenExpired;
            $newPayload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'data' => $decoded->data
            ];

            $newToken = JWT::encode($newPayload, $this->accessTokenKey, $this->jwtAlg);

            return $this->respond([
                'status' => 200,
                'message' => 'Token refreshed',
                'data' => [
                    'accessToken' => $newToken
                ]
            ]);
        } catch (\Exception $e) {
            return $this->respond([
                'status' => 401,
                'message' => 'Invalid token'
            ], 401);
        }
    }
}
