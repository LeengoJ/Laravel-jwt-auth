<?php
namespace App\Http\Controllers;

class Response {
    private $status;
    private $statusCode;
    private $message;
    private $data;

    public function __construct($status, $statusCode, $message, $data = array()) {
        $this->status = $status;
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->data = $data;
    }

    public static function success($message = "", $data = array()) {
        return new Response("success", 200, $message, $data);
    }

    public static function error($message = "", $statusCode = 500) {
        return new Response("error", $statusCode, $message, $statusCode);
    }
}

