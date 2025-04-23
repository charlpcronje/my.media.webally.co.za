<?php
// Analytics model
class Analytics {
    public $id;
    public $media_id;
    public $user_id;
    public $session_id;
    public $device_type;
    public $browser;
    public $os;
    public $start_time;
    public $end_time;
    public $duration;
    public $completed;
    public $skipped;
    public $skip_position;
    public $created_at;
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }
    public static function recordStart($db, $data) {}
    public static function recordEnd($db, $data) {}
    public static function recordSkip($db, $data) {}
    public static function getAnalytics($db, $filters = []) {}
}
