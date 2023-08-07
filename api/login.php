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
if(! array_key_exists("username", $data) || 
   ! array_key_exists("password", $data)) {

    http_response_code(400);
    echo json_encode(["message" => "Missing login credentials."]);
    exit;
   }

   //connection to db
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"],  $_ENV["DB_USER"], $_ENV["DB_PASS"]);

$user_gateway = new UserGateway($database);
$user = $user_gateway->getByUserName($data["username"]);

//checking for login credentials
if($user === false) {
    http_response_code(401);
    echo json_encode(["message" => "Incorrect input credentials"]);
    exit;
}

if ( ! password_verify($data["password"], $user["password_hash"])) {
    http_response_code(401);
    echo json_encode(["message" => "Incorrect input credentials"]);
    exit;
}


$codec = new JWTcodec($_ENV["SECRET_KEY"]);
$refresh_token_gateway = new RefreshTokenGateway($database, $_ENV["SECRET_KEY"]);
$refresh_token_gateway->createRecordToken($refresh_token, $refresh_token_expiry);



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Login form</title>
    <link rel="icon" type="image/png" href="./assets/favicon-32x32.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
</head>
<body data-theme="dark">

    <main class="container" >
<h1>Register</h1>
<!-- Form for registration -->
<form method="post" >
    <label for="name">Name
        <input type="text" name="name" id="name">
    </label>

    <label for="username">
        <input type="text" name="username" id="username">
    </label>

    <label for="password">
        <input type="password" name="password" id="password">
    </label>

    <button>Register</button>

</form>
</main>

</body>

</html>
