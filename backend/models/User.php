<?php
// User model
class User {
    public $id;
    public $username;
    public $email;
    public $password;
    public $role;
    public $created_at;
    public $updated_at;
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    public static function create($db, $data) {}
    public static function authenticate($db, $username, $password) {}
    public static function getProfile($db, $id) {}
    public static function getAll($db) {}
}
