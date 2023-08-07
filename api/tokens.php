<?php

//creating an access token
$payload = [//can contain roles, permissions and other data
    "sub" => $user["id"], //sub is predefined credential in JWT for id, meaning subject
    "name" => $user["name"], 
    "exp" => time() + 300 //300s = 5 minutes
];


$access_token = $codec->encode($payload);

//creating a refresh token
$refresh_token_expiry = time() + 432000;
$refresh_token = $codec->encode([
    "sub" => $user["id"],
    "exp" => $refresh_token_expiry  //7 days
]);

echo json_encode([
    "access_token" => $acces_token,
    "refresh_token" => $refresh_token
]);

require __DIR__ . "/tokens.php";