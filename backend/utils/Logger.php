<?php
// Logger utility class
class Logger {
    public static function log($message, $level = 'info') {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date][$level] $message\n";
        file_put_contents(__DIR__.'/../logs/app.log', $logMessage, FILE_APPEND);
    }
    public static function error($message) {
        self::log($message, 'error');
    }
    public static function warning($message) {
        self::log($message, 'warning');
    }
    public static function info($message) {
        self::log($message, 'info');
    }
}
