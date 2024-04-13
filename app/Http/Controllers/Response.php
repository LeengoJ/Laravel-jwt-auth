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

    public static function success( $data = array(),$message = "") {
        return ["status"=>"success", "statusCode"=>200, "message"=>$message, "data"=>$data];
    }

    public static function error($message = "", $statusCode = 500) {
        // return new Response({"error", $statusCode, $message, $statusCode});
        return ["status"=>"error", "statusCode"=>$statusCode, "message"=>$message, "data"=>[]];
    }
    public static function CVTM($validator) {
        $messages = "";
        foreach ($validator->errors()->toArray() as $subArray) {
            foreach ($subArray as $subSubArray) {
                $messages .= $subSubArray;
            }
        }
        // var_dump($messages);
        // $arr = array_values($messages);
        // var_dump($validator);
        // var_dump($validator->errors());
        // var_dump(get_object_vars(json_decode($validator->errors()->toJson())));
        // $values = array_values(get_object_vars(json_decode($validator->errors()->toJson())));
        // $string = implode('.', $arr);
        return $messages;
    }
}

