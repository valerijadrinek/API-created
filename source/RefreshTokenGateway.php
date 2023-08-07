<?php

class RefreshTokenGateway {

    //db connection
    private PDO $conn;
    private string $key;

    public function __construct(Database $database,
                                string $key) {
        $this->conn = $database->getConnection();
        $this->key = $key;
    }

    //creating hashed refresh token record in db
    public function createRecordToken (string $token, int $expiry) : bool {

        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "INSERT INTO refresh_token (token_hash, expired_at) VALUES (:token_hash, :expired_at)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":token_hash", $hash, PDO::PARAM_STR);
        $stmt->bindValue("expired_at", $expiry, PDO::PARAM_INT);

        return $stmt->execute();

    }

    //deleting token after it's been used or expired
    public function deleteToken (string $token) : int {

         $hash = hash_hmac("sha256", $token, $this->key);
         $sql = "DELETE FROM refresh_token WHERE token_hash=:token_hash";

         $stmt = $this->conn->prepare($sql);
         $stmt->bindValue(":token_hash", $hash, PDO::PARAM_STR);

         $stmt->execute();

         return $stmt->rowCount();

    }

    //selecting by tokens
    public function getByToken(string $token) : array | false {

        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "SELECT * FROM refresh_token WHERE token_hash=:token_hash";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":token_hash", $hash, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    //deleting expired tokens

    public function deleteExpiredTokens() : int
 {
    $sql = "DELETE FROM refresh_token WHERE expired_at < UNIX_TIMESTAMP()";

         $stmt = $this->conn->query($sql);

        return $stmt->rowCount();


 }

}