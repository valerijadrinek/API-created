<?php
//for autoloder composer is important to file name matches a class name
//composer dump-autoload -runed in CMD to create autoload.php file in vendors
class TaskController {

    public function __construct(private TaskGateway $taskGateway, 
                                private int $user_id) {
         
    }


    //process request method
    public function processRequest(string $method, ?string $id) : void {
        if($id === null) {
            
            //get
            if($method == "GET") {
                echo json_encode($this->taskGateway->getAllFromUser($this->user_id));
            
            //post
            } elseif($method == "POST") {

               $data = (array) json_decode(file_get_contents("php://input"), true);
               //errors
               $errors = $this->getValidationErrors($data);

               if(!empty($errors)) {

                $this->respondUnprocessableEntity($errors);
                return;

               }

               //populating db task table
               $id = $this->taskGateway->createRecordForUser($this->user_id,$data);
               $this->respondCreated($id);
            } else {
               $this->respondMethodNotAllowed("GET, POST");
            }

        } else {

            //checking if TaskGateway method getOne returns false
            $task = $this->taskGateway->getOneForUser($this->user_id, $id);
            if($task === false) {
                  $this->respondNotFound($id);
                  return;
            }

            switch($method) {
                case "GET": 
                    echo json_encode($task);
                    break;

                case "PATCH":
                    $data = (array) json_decode(file_get_contents("php://input"), true);
                    //errors
                    $errors = $this->getValidationErrors($data, false);
     
                    if(!empty($errors)) {
     
                     $this->respondUnprocessableEntity($errors);
                     return;
     
                    }
                    

                    $rows = $this->taskGateway->updateRecordForUser($this->user_id, $id, $data);
                    echo json_encode(["message"=>"Records have been updated ", "rows" => $rows]);
                    break;

                case "DELETE":
                    $rows = $this->taskGateway->deleteRecordForUser($this->user_id, $id);
                    echo json_encode(["message"=>"Records have been deleted ", "rows" => $rows]);
                    break;

                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");
                

            }

        }
    }

    //error handling

    //unproccessible entity
    private function respondUnprocessableEntity(array $errors) : void {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
    }

    //not allowed
    private function respondMethodNotAllowed(string $allowed_methods) : void {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }


    //not found
    private function respondNotFound(string $id) : void {
        http_response_code(404);
        echo json_encode(["message" => "Task with id $id is not found"]);
    }


    //created respond
    private function respondCreated(string $id) :void {
        http_response_code(201);
        echo json_encode(["message" => "Task created", "id" => $id]);
    }


    //db validation errors
    private function getValidationErrors(array $data, bool $is_new = true) : array {
        $errors = [];

        if($is_new && empty($data["name"])) {
            $errors[] = "The name is required.";

        }

        if(!empty($data["priority"])) {
            if(filter_var($data["priority"], FILTER_VALIDATE_INT) === false) {

                $errors[] = "Priority must be an integer.";

            }
        }

        return $errors;
    }



}
