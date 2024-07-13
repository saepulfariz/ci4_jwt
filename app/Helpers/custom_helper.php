<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function logged($key)
{
    $request = \Config\Services::request();
    $authHeader = $request->getHeader('Authorization');
    // jika kirim token
    if ($authHeader) {
        $token = str_replace('Bearer ', '', $authHeader->getValue());
        $decoded = JWT::decode($token, new Key(getenv('ACCESS_TOKEN_KEY'), getenv('JWT_ALG')));
        $userData = $decoded->data;
        $userId = $userData->id;
        if (property_exists($userData, $key)) {
            // Kembalikan nilai dari properti tersebut
            return $userData->$key;
        } else {
            // Kembalikan null jika properti tidak ditemukan
            return null;
        }
    } else {
        // jika pake session
        return session()->get($key);
    }
}
