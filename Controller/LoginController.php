<?php

require_once(dirname(__FILE__).'/../Utils/Connection.php');
include_once(dirname(__FILE__).'/../Model/User.php');

class LoginController {
    private static $instance;

    private function __construct() {
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function login($userName, $hashedPassword){
        try {
            $conn = connect();
            $stmt = $conn->prepare('SELECT * FROM users WHERE name = :userName AND password = :hashedpassword LIMIT 1');
            $stmt->execute(array('userName' => $userName, 'hashedpassword' => $hashedPassword));

            $user = $stmt->fetch(PDO::FETCH_OBJ);
            $conn = null; //Close connection when no longer needed.
            if ($user) {
                if($user->status == 0){
                    return 0; //User not activated
                }
                return $user;
            } else{
                return -1; //User doesn't exist
            }
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }
}
?>