<?php
class Authentication {
    private int $user_id;
    public function __construct(private UserGateway $user_gateway, 
                                private JWTcodec $codec) {

    }

    public function authenticateApiKey():bool {
    //api key
    if(empty($_SERVER ["HTTP_X_API_KEY"])) {
       http_response_code(400);
       echo json_encode(["message" => "API key is missing"]);
       return false;
    }

    $api_key = $_SERVER["HTTP_X_API_KEY"];
    $user = $this->user_gateway->getByApiKey($api_key);

    if( $user === false) {
        http_response_code(401);
        echo json_encode(["message"=>"Access denied becouse the API key is invalid"]);
        return false;
    };
    $this->user_id = $user ["id"];
    return true;
    }


    //userId
    public function getUserId() : int {
        return $this->user_id;
    }


    //access token
    public function authenticateAccessToken():bool {
      if( ! preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches)) {
        http_response_code(400);
        echo json_encode(["message"=>"Access denied becouse the acces token is invalid"]);
        return false;
      }

      try {
      $data = $this->codec->decode($matches[1]); 
      }

      // catching exceptions
      catch (TokenExpiredException) {
        http_response_code(401);
        json_encode(["message" => "Token has expired."]);
        return false;
      } 
      catch(InvalidSignatureException) {
        http_response_code(401);
        echo json_encode(["message" => "Invalid signature"]);
        return false;

      }
      catch (Exception $e) {
        http_response_code(400);
        echo json_encode(["message" => $e->getMessage()]);
        return false;
      }

      $this->user_id = $data["sub"];

      return true;
    }



}