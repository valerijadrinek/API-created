<?php
require __DIR__ . "/vendor/autoload.php";

if($_SERVER["REQUEST_METHOD"] === "POST") {

    //connection to db
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"],  $_ENV["DB_USER"], $_ENV["DB_PASS"]);
    $conn = $database->getConnection();

    //insert new user
    $sql = "INSERT INTO user (name, username, password_hash, api_key) VALUES(:name, :username, :password_hash, :api_key)";
    $stmt = $conn-> prepare($sql);


    $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $api_key = bin2hex(random_bytes(16));

    $stmt->bindValue(":name", $_POST["name"], PDO::PARAM_STR);
    $stmt->bindValue(":username", $_POST["username"], PDO::PARAM_STR);
    $stmt->bindValue(":password_hash", $password_hash, PDO::PARAM_STR);
    $stmt->bindValue(":api_key", $api_key, PDO::PARAM_STR);

    $stmt->execute();

    echo "Thank you for registering. Your API key is " . $api_key;
    exit();

}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Register form</title>
    <link rel="icon" type="image/png" href="./assets/favicon-32x32.png">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
</head>
<body>
    <main class="container">
<h1>Register</h1>
<!-- Form for registration -->
<form method="post" data-theme="dark">
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
