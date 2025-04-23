<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Media.php';

class MediaController extends Controller {
    public function listMedia($db, $filters = []) {
        // Stub: implement list media
        $this->jsonResponse(['media' => []]);
    }
    public function getMedia($db, $id) {
        // Stub: implement get single media
        $this->jsonResponse(['media' => null]);
    }
    public function playMedia($db, $id) {
        // Stub: implement play media
        $this->jsonResponse(['play_url' => null]);
    }
    public function addMedia($db, $data) {
        // Stub: implement add media
        $this->jsonResponse(['message' => 'Media added']);
    }
    public function updateMedia($db, $id, $data) {
        // Stub: implement update media
        $this->jsonResponse(['message' => 'Media updated']);
    }
    public function deleteMedia($db, $id) {
        // Stub: implement delete media
        $this->jsonResponse(['message' => 'Media deleted']);
    }
}
