<?php
class TaskGateway {

    private PDO $conn;
    public function __construct(Database $database) 
    {
        $this->conn = $database->getConnection();
    }


    //getAll()
    public function getAllFromUser(int $user_id) : array 
    {
      $sql = "SELECT * FROM task WHERE user_id=:user_id ORDER BY name";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
      $stmt->execute();
      //return $stmt->fetchAll(PDO::FETCH_ASSOC);-fetching all data with tinyint instead of bool

      //fetching bool
      $data = [];
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
         $row['is_completed'] = (bool) $row['is_completed'];
         $data[] = $row;
      }
      
      return $data;
    }

    //getOne()
    public function getOneForUser(int $user_id, string $id): array | false {

        $sql = "SELECT * FROM task WHERE id=:id AND user_id=:user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();  
        $data = $stmt->fetch(PDO::FETCH_ASSOC);    

        if($data !== false) {
         $data['is_completed'] = (bool) $data['is_completed'];
         
        }
        return $data;

    }

    //inserting data into db
    public function createRecordForUser(int $user_id, array $data) :string {
        $sql = "INSERT INTO task (name, priority, is_completed, user_id) VALUES(:name, :priority, :is_completed, :user_id)";
        $stmt = $this->conn->prepare($sql);
        //binding data into records
        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);

        if(empty($data["priority"])) {
            $stmt->bindValue(":priority", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_INT);
        }

        $stmt->bindValue("is_completed", $data["is_completed"] ?? false, PDO::PARAM_BOOL);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();


        //returning last inserted Id
        return $this->conn->lastInsertId();

    }

    //updating records
    public function updateRecordForUser(int $user_id, string $id, array $data) : int {

        $fields = [];

        if(!empty($data["name"])) {
            $fields["name"] = [$data["name"], PDO::PARAM_STR];
        }

        if(array_key_exists("priority", $data)) {
            $fields["priority"] = [$data["priority"], 
                                   $data["priority"] === null ? PDO::PARAM_NULL : PDO :: PARAM_INT];
        }

        if(array_key_exists("is_completed", $data)) {
            $fields["is_completed"] = [$data["is_completed"], PDO::PARAM_BOOL];
        }

        if(empty($fields)) {

            return 0;

        }  else {

        $sets = array_map(function($value) {

            return "$value = :$value";

        }, array_keys($fields));

        $sql = "UPDATE task " . " SET " . implode(", ", $sets) . " WHERE id=:id AND user_id=:user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        foreach($fields as $name => $values) {
            $stmt->bindValue(":$name", $values[0], $values[1]);
        }
 
        $stmt->execute();
        return $stmt->rowCount();
        }
    }

    //deleting record
    public function deleteRecordForUser(int $user_id, string $id) : int {

        $sql = "DELETE FROM task WHERE id=:id AND user_id=:user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();

    }
       

}