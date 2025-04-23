<?php
// Authentication utility class
class Auth {
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    public static function generateJWT($user, $secret) {
        $payload = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'exp' => time() + 3600
        ];
        return JWT::encode($payload, $secret, 'HS256');
    }
    public static function validateJWT($token, $secret) {
        try {
            return JWT::decode($token, $secret, ['HS256']);
        } catch (Exception $e) {
            return false;
        }
    }
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    public static function destroySession() {
        session_destroy();
    }
}
