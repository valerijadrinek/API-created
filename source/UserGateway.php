<?php
 class UserGateway {

    //connection to db
    private PDO $conn;
    public function __construct(Database $database) 
    {
        $this->conn = $database->getConnection();
    }

    public function getByApiKey(string $key) : array | false {
      
        $sql = "SELECT * FROM user WHERE api_key = :api_key";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":api_key", $key, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);


        }

    public function getByUserName(string $username) : array | false {
            $sql = "SELECT * FROM user WHERE username = :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":username", $username, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }


    public function getByID(int $id) : array | false {
        $sql = "SELECT * FROM user WHERE id=:id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

 }

