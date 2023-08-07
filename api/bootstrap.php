<?php

//autoload.php composer
require dirname(__DIR__) . "/vendor/autoload.php";

//handling errors
set_error_handler("ErrorHandler::handlerError");
set_exception_handler("ErrorHandler::handlerException");

//database connection in .env
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

//header
header('Content-Type: application/json; charset=utf-8');