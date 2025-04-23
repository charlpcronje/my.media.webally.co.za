<?php
// Base Controller
class Controller {
    protected function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    protected function errorResponse($message, $status = 400) {
        $this->jsonResponse(['error' => $message], $status);
    }
    protected function validateRequest($fields, $source) {
        foreach ($fields as $field) {
            if (!isset($source[$field])) {
                $this->errorResponse("Missing field: $field", 422);
            }
        }
    }
}
