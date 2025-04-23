<?php
// Media model
class Media {
    public $id;
    public $title;
    public $description;
    public $type;
    public $file_path;
    public $thumbnail_path;
    public $duration;
    public $tags;
    public $average_rating;
    public $ratings_count;
    public $play_count;
    public $uploaded_by;
    public $created_at;
    public $updated_at;
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    public static function create($db, $data) {}
    public static function get($db, $id) {}
    public static function getAll($db, $filters = []) {}
    public static function update($db, $id, $data) {}
    public static function delete($db, $id) {}
    public static function incrementPlayCount($db, $id) {}
}
