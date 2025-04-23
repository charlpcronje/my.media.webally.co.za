<?php
// Rating model
class Rating {
    public $id;
    public $media_id;
    public $user_id;
    public $rating;
    public $created_at;
    public $updated_at;
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    public static function addOrUpdate($db, $media_id, $user_id, $rating) {}
    public static function getAverage($db, $media_id) {}
    public static function getUserRating($db, $media_id, $user_id) {}
}
