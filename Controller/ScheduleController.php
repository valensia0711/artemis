<?php

require_once(dirname(__FILE__).'/../Utils/Connection.php');
require_once(dirname(__FILE__).'/../Utils/DBoperation.php');
require_once(dirname(__FILE__).'/../Model/User.php');
require_once(dirname(__FILE__).'/../Model/Duty.php');

class ScheduleController {
    private static $instance;
    private $scheduleAvailability;
    
    private function __construct() {
        $this->scheduleAvailability = new DBOperation("schedule_availability");
    }
   
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setAvailability($user, $duty, $availability) {
        $queryCondition = array('supervisor_id' => $user->getID(),
                                'schedule_id' => $duty->getID());
        if (count($this->scheduleAvailability->get($queryCondition)) > 0) {
            $this->scheduleAvailability->deleteData($queryCondition);
        }
        $queryCondition['availability'] = $availability;
        $this->scheduleAvailability->insertData($queryCondition);
    }

    public function getAvailability($user, $duty) {
        $queryCondition = array('supervisor_id' => $user->getID(),
                                'schedule_id' => $duty->getID());
        if (count($this->scheduleAvailability->get($queryCondition)) == 0) {
            return "UNSET";
        }
        $availability = $this->scheduleAvailability->get($queryCondition)[0]['availability'];
        return $availability;
    }

    public function setAvailabilities($user, $availabilities) {
        foreach($availabilities as $availability) {
            setAvailability($user, $availability['duty'], $availability['availability']);
        }
    }
}
?>
