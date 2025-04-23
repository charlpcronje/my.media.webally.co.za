<?php
// File upload utility class
class FileUpload {
    public static function validateFileType($file, $allowedTypes) {
        $fileType = mime_content_type($file['tmp_name']);
        return in_array($fileType, $allowedTypes);
    }
    public static function generateUniqueFilename($originalName) {
        return uniqid() . '_' . basename($originalName);
    }
    public static function upload($file, $destination) {
        $filename = self::generateUniqueFilename($file['name']);
        $target = $destination . $filename;
        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $filename;
        } else {
            return false;
        }
    }
    public static function generateThumbnail($videoPath, $thumbnailPath) {
        // Stub: Implement thumbnail generation using ffmpeg or similar
        return true;
    }
}
