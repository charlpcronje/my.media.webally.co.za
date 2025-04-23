<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Rating.php';

class RatingController extends Controller {
    public function rateMedia($db, $media_id, $user_id, $rating) {
        // Stub: implement rating
        $this->jsonResponse(['average_rating' => 0]);
    }
    public function getUserRating($db, $media_id, $user_id) {
        // Stub: implement get user rating
        $this->jsonResponse(['rating' => null]);
    }
}
