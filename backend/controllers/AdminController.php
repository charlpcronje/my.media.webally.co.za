<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AdminController extends Controller {
    public function listUsers($db) {
        // Stub: implement list users
        $this->jsonResponse(['users' => []]);
    }
    public function createUser($db, $data) {
        // Stub: implement create user
        $this->jsonResponse(['message' => 'User created']);
    }
    public function updateUser($db, $id, $data) {
        // Stub: implement update user
        $this->jsonResponse(['message' => 'User updated']);
    }
    public function deleteUser($db, $id) {
        // Stub: implement delete user
        $this->jsonResponse(['message' => 'User deleted']);
    }
    public function dashboard($db) {
        // Stub: implement admin dashboard data
        $this->jsonResponse(['stats' => []]);
    }
}
