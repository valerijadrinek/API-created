<?php

//in Trowable we can get access to different error methods- look in php docs
class ErrorHandler {


    //error handler
    public static function handlerError(int $errno, string $errstr, string $errfile, int $errline) : void {

        throw new ErrorException($errstr, 0, $errno, $errfile, $errline );

    }

    //exception handler
    public static function handlerException (Throwable $e) : void {
        
        http_response_code(500);//generic code for errors

        echo json_encode([
            "code"=>$e->getCode(),
            "message"=>$e->getMessage(),
            "file"=>$e->getFile(),
            "line"=>$e->getLine()
        ]);
    }
}