<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Auth.php';

class AuthController extends Controller {
    public function login($db, $data, $jwt_secret) {
        // Stub: implement login logic
        $this->jsonResponse(['token' => 'demo.jwt.token', 'user' => null]);
    }
    public function logout() {
        Auth::destroySession();
        $this->jsonResponse(['message' => 'Logged out']);
    }
    public function getCurrentUser() {
        // Stub: implement get current user
        $this->jsonResponse(['user' => null]);
    }
    public function verifyRole($role) {
        // Stub: implement role check
        return true;
    }
}
