<?php

require_once(dirname(__FILE__).'/../Utils/Connection.php');
require_once(dirname(__FILE__).'/../Model/Sign.php');
require_once(dirname(__FILE__).'/../Model/User.php');
require_once(dirname(__FILE__).'/../Utils/DBoperation.php');

class SignController {
    private static $instance;
    private $signList;
    
    private function __construct() {
        $this->signList = new DBOperation("sign");
    }
   
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addSign($user,$sign) {
        $dataToInsert = array('supervisor_id' => $user->getID(),
                              'venue' => $sign->getVenue(),
                              'signtype' => $sign->getSignType());
        $this->signList->insertData($dataToInsert);
    }
    
    public function getAllSignList() {
        return $this->signList->getAll();
    }
    
}
?>
