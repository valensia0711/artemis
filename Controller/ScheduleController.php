<?php

require_once(dirname(__FILE__).'/../Utils/Connection.php');
require_once(dirname(__FILE__).'/../Utils/DBoperation.php');
require_once(dirname(__FILE__).'/../Model/User.php');
require_once(dirname(__FILE__).'/../Model/Duty.php');
require_once(dirname(__FILE__).'/../Controller/UserController.php');
require_once(dirname(__FILE__).'/../Controller/DutyController.php');

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
        $_SESSION['success'] = 'Successfully set availability(ies)';
        return true;
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

    public function getAvailable($user) {
        $queryCondition = array('supervisor_id' => $user->getID(),
                                'availability' => 'AVAILABLE');
        $availability = $this->scheduleAvailability->get($queryCondition);
        return $availability;
    }

    public function clearAllAvailability() {
        $queryCondition = array();
        $this->scheduleAvailability->deleteData($queryCondition);
        $_SESSION['success'] = 'Successfully clear availability(ies)';
        return true;
    }

    public function setAvailabilities($user, $availabilities) {
        foreach($availabilities as $availability) {
            setAvailability($user, $availability['duty'], $availability['availability']);
        }
    }

    public function automateScheduling() {
        $closedScheduleIDs = array();
        $closedScheduleIDs['yih'] = [1, 17, 18, 34, 35, 51, 52, 68, 69, 85, 86, 98, 99, 100, 101, 102, 103, 104, 105, 112, 113, 114, 115, 116, 117, 118, 119];
        $closedScheduleIDs['cl'] = [86, 87, 88, 89, 103, 104, 105, 106, 115, 116, 117, 118, 119];

        $userController = UserController::getInstance();
        $users = $userController->getAllActiveSubcoms();
        $usersAndAvailabilities = array();
        foreach ($users as $user) {
            $userClass = new User($user['id'], null, null, null, null, null, null, null);
            $availables = array();
            $availables_array = $this->getAvailable($userClass);
            for ($i = 0; $i < count($availables_array); ++$i) {
                array_push($availables, $availables_array[$i]['schedule_id']);
            }
            array_push($usersAndAvailabilities, [$user['id'], $availables]);
        }
        for ($i = 0; $i < count($usersAndAvailabilities); ++$i) {
            for ($j = $i; $j < count($usersAndAvailabilities); ++$j) {
                if (count($usersAndAvailabilities[$i][1]) > count($usersAndAvailabilities[$j][1])) {
                    $temp = $usersAndAvailabilities[$i];
                    $usersAndAvailabilities[$i] = $usersAndAvailabilities[$j];
                    $usersAndAvailabilities[$j] = $temp;
                }
            }
        }
        $assignedTo = array();
        define("AVAILABLE", -1);
        foreach (['yih', 'cl'] as $location) {
            for ($i = 1; $i <= 119; ++$i) {
                if (in_array($i,$closedScheduleIDs[$location]) || in_array($i+1,$closedScheduleIDs[$location]) || in_array($i-1,$closedScheduleIDs[$location])) {
                    $assignedTo[$location][$i] = 0;
                    //echo $location." ".$i."<br/>";
                } else {
                    $assignedTo[$location][$i] = AVAILABLE;
                }
            }
        }
        $dutyController = DutyController::getInstance();
        for ($i = 0; $i < count($usersAndAvailabilities); ++$i) {
            asort($usersAndAvailabilities[$i][1]);
            $allocated = false;
            foreach (['yih', 'cl'] as $location) {
                if ($allocated) {
                    break;
                }
                for ($j = 0; $j < count($usersAndAvailabilities[$i][1]); ++$j) {
                    if ($allocated) {
                        break;
                    }
                    $hours = $dutyController->getIntervalDuty($usersAndAvailabilities[$i][1][$j]);
                    for ($k = $j+1; $k < count($usersAndAvailabilities[$i][1]); ++$k) {
                        if ($usersAndAvailabilities[$i][1][$k] != $usersAndAvailabilities[$i][1][$k-1] + 1) {
                            break;
                        }
                        if ($dutyController->getDayNameDuty($usersAndAvailabilities[$i][1][$k]) 
                            != $dutyController->getDayNameDuty($usersAndAvailabilities[$i][1][$k-1])) {
                            break;
                        }
                        $hours += $dutyController->getIntervalDuty($usersAndAvailabilities[$i][1][$k]);
                        if ($hours == 2) {
                            //echo $location." ".$usersAndAvailabilities[$i][0]." ".$usersAndAvailabilities[$i][1][$j]." ".$usersAndAvailabilities[$i][1][$k]."<br/>";
                            $canTakeSlots = true;
                            for ($id = $usersAndAvailabilities[$i][1][$j]; $id <= $usersAndAvailabilities[$i][1][$k]; ++$id) {
                                //echo $assignedTo[$location][$id]."<br/>";
                                if ($assignedTo[$location][$id] != AVAILABLE) {
                                    //echo $assignedTo[$location][$id]."<br/>";
                                    $canTakeSlots = false;
                                    break;
                                }
                            }
                            //echo $location." ".$usersAndAvailabilities[$i][0]." ".$usersAndAvailabilities[$i][1][$j]." ".$usersAndAvailabilities[$i][1][$k]."<br/>";
                            if ($canTakeSlots) {
                                for ($id = $usersAndAvailabilities[$i][1][$j]; $id <= $usersAndAvailabilities[$i][1][$k]; ++$id) {
                                    $assignedTo[$location][$id] = $usersAndAvailabilities[$i][0];
                                }
                                $allocated = true;
                            }
                        }
                        if ($hours >= 2) {
                            break;
                        }
                    }
                }
            }
        }

        $MCs = $userController->getAllActiveMCs();
        $usersAndAvailabilities = array();
        foreach ($MCs as $MC) {
            $userClass = new User($MC['id'], null, null, null, null, null, null, null);
            $availables = array();
            $availables_array = $this->getAvailable($userClass);
            for ($i = 0; $i < count($availables_array); ++$i) {
                array_push($availables, $availables_array[$i]['schedule_id']);
            }
            array_push($usersAndAvailabilities, [$MC['id'], $availables]);
        }
        /*while (true) {
            $someAssigningGoingOn = false;
            for ($i = 0; $i < count($usersAndAvailabilities); ++$i) {
                if ($someAssigningGoingOn) {
                    break;
                }
                $supervisorID = $MCs[$i]['id'];
                //echo $supervisorID."<br/>";
                foreach (['yih', 'cl'] as $location) {
                    if ($someAssigningGoingOn) {
                        break;
                    }
                    for ($dutyID = 1; $dutyID <= 119; ++$dutyID) {
                        if ($assignedTo[$location][$dutyID] == AVAILABLE) {
                            if (in_array($dutyID, $usersAndAvailabilities[$i][1])) {
                                $assignedTo[$location][$dutyID] = $usersAndAvailabilities[$i][0];
                                $someAssigningGoingOn = true;
                                break;
                            }
                        }
                    }
                }
            }
            if (!$someAssigningGoingOn) {
                break;
            }
        }*/
        $hoursAllocated = array();
        for ($i = 0; $i < count($usersAndAvailabilities); ++$i) {
            $hoursAllocated[$usersAndAvailabilities[$i][0]] = 0;
        }

        for ($i = 0; $i < count($usersAndAvailabilities); ++$i) {
            asort($usersAndAvailabilities[$i][1]);
            $allocated = false;
            foreach (['yih', 'cl'] as $location) {
                if ($allocated) {
                    break;
                }
                for ($j = 0; $j < count($usersAndAvailabilities[$i][1]); ++$j) {
                    if ($allocated) {
                        break;
                    }
                    $hours = $dutyController->getIntervalDuty($usersAndAvailabilities[$i][1][$j]);
                    for ($k = $j+1; $k < count($usersAndAvailabilities[$i][1]); ++$k) {
                        if ($usersAndAvailabilities[$i][1][$k] != $usersAndAvailabilities[$i][1][$k-1] + 1) {
                            break;
                        }
                        if ($dutyController->getDayNameDuty($usersAndAvailabilities[$i][1][$k]) 
                            != $dutyController->getDayNameDuty($usersAndAvailabilities[$i][1][$k-1])) {
                            break;
                        }
                        $hours += $dutyController->getIntervalDuty($usersAndAvailabilities[$i][1][$k]);
                        if ($hours >= 5) {
                            //echo $location." ".$usersAndAvailabilities[$i][0]." ".$usersAndAvailabilities[$i][1][$j]." ".$usersAndAvailabilities[$i][1][$k]."<br/>";
                            $canTakeSlots = true;
                            for ($id = $usersAndAvailabilities[$i][1][$j]; $id <= $usersAndAvailabilities[$i][1][$k]; ++$id) {
                                //echo $assignedTo[$location][$id]."<br/>";
                                if ($assignedTo[$location][$id] != AVAILABLE) {
                                    //echo $assignedTo[$location][$id]."<br/>";
                                    $canTakeSlots = false;
                                    break;
                                }
                            }
                            //echo $location." ".$usersAndAvailabilities[$i][0]." ".$usersAndAvailabilities[$i][1][$j]." ".$usersAndAvailabilities[$i][1][$k]."<br/>";
                            if ($canTakeSlots) {
                                $hoursAllocated[$usersAndAvailabilities[$i][0]] = $hours;
                                for ($id = $usersAndAvailabilities[$i][1][$j]; $id <= $usersAndAvailabilities[$i][1][$k]; ++$id) {
                                    $assignedTo[$location][$id] = $usersAndAvailabilities[$i][0];
                                }
                                $allocated = true;
                            }
                        }
                        if ($hours >= 5) {
                            break;
                        }
                    }
                }
            }
        }

        for ($i = 0; $i < count($usersAndAvailabilities); ++$i) {
            if ($hoursAllocated[$usersAndAvailabilities[$i][0]] > 0) {
                continue;
            }
            for ($tries = 0; $tries < 3; ++$tries) {
                $allocated = false;
                foreach (['yih', 'cl'] as $location) {
                    if ($allocated) {
                        break;
                    }
                    for ($j = 0; $j < count($usersAndAvailabilities[$i][1]); ++$j) {
                        if ($allocated) {
                            break;
                        }
                        $hours = $dutyController->getIntervalDuty($usersAndAvailabilities[$i][1][$j]);
                        for ($k = $j+1; $k < count($usersAndAvailabilities[$i][1]); ++$k) {
                            if ($usersAndAvailabilities[$i][1][$k] != $usersAndAvailabilities[$i][1][$k-1] + 1) {
                                break;
                            }
                            if ($dutyController->getDayNameDuty($usersAndAvailabilities[$i][1][$k]) 
                                != $dutyController->getDayNameDuty($usersAndAvailabilities[$i][1][$k-1])) {
                                break;
                            }
                            $hours += $dutyController->getIntervalDuty($usersAndAvailabilities[$i][1][$k]);
                            if ($hours >= 2) {
                                //echo $location." ".$usersAndAvailabilities[$i][0]." ".$usersAndAvailabilities[$i][1][$j]." ".$usersAndAvailabilities[$i][1][$k]."<br/>";
                                $canTakeSlots = true;
                                for ($id = $usersAndAvailabilities[$i][1][$j]; $id <= $usersAndAvailabilities[$i][1][$k]; ++$id) {
                                    //echo $assignedTo[$location][$id]."<br/>";
                                    if ($assignedTo[$location][$id] != AVAILABLE) {
                                        //echo $assignedTo[$location][$id]."<br/>";
                                        $canTakeSlots = false;
                                        break;
                                    }
                                }
                                //echo $location." ".$usersAndAvailabilities[$i][0]." ".$usersAndAvailabilities[$i][1][$j]." ".$usersAndAvailabilities[$i][1][$k]."<br/>";
                                if ($canTakeSlots) {
                                    $hoursAllocated[$usersAndAvailabilities[$i][0]] = $hours;
                                    for ($id = $usersAndAvailabilities[$i][1][$j]; $id <= $usersAndAvailabilities[$i][1][$k]; ++$id) {
                                        $assignedTo[$location][$id] = $usersAndAvailabilities[$i][0];
                                    }
                                    $allocated = true;
                                }
                            }
                            if ($hours >= 2) {
                                break;
                            }
                        }
                    }
                }
            }
        }

        $assigned = array();
        foreach (['yih', 'cl'] as $location) {
            for ($i = 1; $i <= 119; ++$i) {
                if ($assignedTo[$location][$i] == AVAILABLE) {
                    $assignedTo[$location][$i] = 0;
                } else {
                    array_push($assigned, $assignedTo[$location][$i]);
                }
            }
        }
        $errorMessage = "";
        $allUsers = $userController->getAllActiveMembers();
        for ($i = 0; $i < count($allUsers); ++$i) {
            if (!in_array($allUsers[$i]['id'], $assigned)) {
                if ($errorMessage != "") {
                    $errorMessage = $errorMessage.", ";
                }
                $errorMessage .= $allUsers[$i]['name'];
            }
        }
        if ($errorMessage != "") {
            $_SESSION['warning'] = $errorMessage." are not assigned yet";
        }
        return $assignedTo;
    }
}
?>