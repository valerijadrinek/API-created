<?php
declare(strict_types=1);

require __DIR__ . "/bootstrap.php";

if($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Allow: POST");
    exit;
}

$data = (array) json_decode(file_get_contents("php://input"), true);

//checking for empty input
if(! array_key_exists("token", $data) 
  ) {

    http_response_code(400);
    echo json_encode(["message" => "Missing token."]);
    exit;
   }

$codec = new JWTcodec($_ENV["SECRET_KEY"]);

try {
$payload = $codec->decode($data["token"]);
}

catch(Exception ) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid token."]);
    exit;
}

$user_id = $payload["sub"];

//conn to db
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"],  $_ENV["DB_USER"], $_ENV["DB_PASS"]);

//validate token
$refresh_token_gateway = new RefreshTokenGateway($database, $_ENV["SECRET_KEY"]);
$refres_token = $refresh_token_gateway->getByToken($data["token"]);

if($refres_token === false) {

    http_response_code(400);
    echo json_encode(["message" => "Invalid or expired token"]);
    exit;

}

$user_gateway = new UserGateway($database);

$user = $user_gateway->getByID($user_id);

if($user === false) {
    http_response_code(401);
    echo json_encode(["message" => "Invalid authentication."]);
    exit;
}

//tokens
require __DIR__ . "/tokens.php";



$refresh_token_gateway->deleteToken($data["token"]);
$refresh_token_gateway->createRecordToken($refresh_token, $refresh_token_expiry);