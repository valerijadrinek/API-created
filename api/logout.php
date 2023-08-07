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


//conn to db
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"],  $_ENV["DB_USER"], $_ENV["DB_PASS"]);

//validate token
$refresh_token_gateway = new RefreshTokenGateway($database, $_ENV["SECRET_KEY"]);

$refresh_token_gateway->deleteToken($data["token"]);


