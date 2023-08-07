<?php
declare(strict_types=1);
require __DIR__ . "/bootstrap.php";


//Front path controller
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$parts = explode("/", $path);

$resource = $parts[6];
$id = $parts[7] ?? null;



if($resource != 'tasks') {
    //header("{$_SERVER['SERVER_PROTOCOL']} 404 NOT FOUND");
    http_response_code(404);
    exit;
}


//connection to db
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"],  $_ENV["DB_USER"], $_ENV["DB_PASS"]);

//authentication
$user_gateway = new UserGateway($database);
$codec = new JWTcodec($_ENV["SECRET_KEY"]);

//$headers = apache_request_headers();-solved in access file

$authentication = new Authentication($user_gateway, $codec);
if ( ! $authentication->authenticateAccessToken()) {
    exit();
};

$user_id = $authentication->getUserId();

//gateway
$task_gateway = new TaskGateway($database);

//controller
$controller = new TaskController($task_gateway, $user_id);
$controller->processRequest($_SERVER['REQUEST_METHOD'], $id );

