<?php
/**
* Manage duty grab, swap, release, get number of duty hours per month of a user.
**/
require_once(dirname(__FILE__).'/../Utils/Connection.php');
require_once(dirname(__FILE__).'/../Model/User.php');
require_once(dirname(__FILE__).'/../Model/Duty.php');
require_once(dirname(__FILE__).'/../Model/Date.php');
require_once(dirname(__FILE__).'/../Utils/DBoperation.php');

class DutyController {
    private static $instance;
    private $conn;
    private $dutySchedule;
    private $grabList;
    private $dropList;

    private function __construct() {
        $this->conn = connect();
        $this->dutySchedule = new DBOperation("duty_schedule");
        $this->grabList = new DBOperation("grabbed_duty");
        $this->dropList = new DBoperation("released_duty");
    }
   
    public static function getInstance() {
    	if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function assignPermanentDuty($user, $duty) {
        try{
            $stmt = $this->conn->prepare("UPDATE duty_schedule SET supervisor_{$duty->getLocation()} = :supervisor_id 
                WHERE id= :id");

            $stmt->execute(array('supervisor_id' => $user->getID(),
                                'id' => $duty->getID()));
            
            if($stmt->rowCount() != 1){
                die("Duty update error!");
            }
            
            $stmt = $this->conn->prepare("DELETE FROM released_duty WHERE schedule_id = :schedule_id
                AND venue = :venue");

            $stmt->execute(array('schedule_id' => $duty->getID(),
                                'venue' => $duty->getLocation()));
            
            
            $stmt = $this->conn->prepare("DELETE FROM grabbed_duty WHERE schedule_id = :schedule_id
                AND venue = :venue");

            $stmt->execute(array('schedule_id' => $duty->getID(),
                                'venue' => $duty->getLocation()));
            
            
            $_SESSION['success'] = 'Successfully assign permanent duty(ies)';
            return true;
        } catch(PDOException $e) {
            echo 'ERROR: ' . $e->getMessage();
        }
    }
    
    public function assignTemporaryDuty($user, $duty) {
        if ($this->getSupervisorID($duty) >= 0) {
            $oldUser = new User($this->getSupervisorID($duty), null, null, null, null, null, null, null);
            $this->releaseDuty($oldUser,$duty);
        }
        $this->grabDuty($user,$duty);
        $_SESSION['success'] = 'Successfully assign temporary duty(ies)';
        return true;
    }
    
    private function isOpeningDuty($duty) {
        for ($openingID = 1; $openingID <= 103; $openingID += 17) {
            if ($duty->getID() == $openingID) {
                return true;
            }
        }
        $previousDuty = new DailyDuty($duty->getID() - 1, null, null, null, $duty->getLocation(), $duty->getDate(), $duty->getMonth(), $duty->getYear());
        if ($this->getSupervisorID($previousDuty) == 0) {
            return true;
        }
        return false;
    }
    
    private function isClosingDuty($duty) {
        for ($closingID = 17; $closingID <= 119; $closingID += 17) {
            if ($duty->getID() == $closingID) {
                return true;
            }
        }
        $nextDuty = new DailyDuty($duty->getID() + 1, null, null, null, $duty->getLocation(), $duty->getDate(), $duty->getMonth(), $duty->getYear());
        if ($this->getSupervisorID($nextDuty) == 0) {
            return true;
        }
        return false;
    }
    
    private function isMoreThanKHours($user, $duty, $hoursLimit) {
        $startDuty = new DailyDuty($duty->getID() - 1, null, null, null, $duty->getLocation(), $duty->getDate(), $duty->getMonth(), $duty->getYear());
        $endDuty = new DailyDuty($duty->getID() + 1, null, null, null, $duty->getLocation(), $duty->getDate(), $duty->getMonth(), $duty->getYear());
        while ($startDuty->getID() >= 1 && $this->getDayNameDuty($startDuty->getID()) == $this->getDayNameDuty($duty->getID())
               && $this->getSupervisorID($startDuty) == $user->getID()) {
            $startDuty->setID($startDuty->getID() - 1);
        }
        while ($endDuty->getID() <= 119 && $this->getDayNameDuty($endDuty->getID()) == $this->getDayNameDuty($duty->getID())
                && $this->getSupervisorID($endDuty) == $user->getID()) {
            $endDuty->setID($endDuty->getID() + 1);
        }
        $startDuty->setID($startDuty->getID() + 1);
        $endDuty->setID($endDuty->getID() - 1);
        
        $startTime = substr($this->getTimeDuty($startDuty->getID()),0,5);
        $endTime = substr($this->getTimeDuty($endDuty->getID()),6,5);
        $interval = intval(substr($endTime,0,2)) * 100 + intval(substr($endTime,3,2)) -
                    intval(substr($startTime,0,2)) * 100 - intval(substr($startTime,3,2));
        return ($interval > $hoursLimit);
    }
    
    private function isConsecutiveDiffPlace($user, $duty) {
        $anotherDuty = new DailyDuty($duty->getID() - 1, null, null, null, 
                                     $duty->getLocation() == "yih" ? "cl" : "yih", 
                                     $duty->getDate(), $duty->getMonth(), $duty->getYear());
        if ($anotherDuty->getID() >= 1 && $this->getDayNameDuty($anotherDuty->getID()) == $this->getDayNameDuty($duty->getID())
            && $this->getSupervisorID($anotherDuty) == $user->getID()) {
            return true;
        }
        $anotherDuty = new DailyDuty($duty->getID() + 1, null, null, null, 
                                     $duty->getLocation() == "yih" ? "cl" : "yih", 
                                     $duty->getDate(), $duty->getMonth(), $duty->getYear());
        if ($anotherDuty->getID() <= 119 && $this->getDayNameDuty($anotherDuty->getID()) == $this->getDayNameDuty($duty->getID())
            && $this->getSupervisorID($anotherDuty) == $user->getID()) {
            return true;
        }
    }
    
    public function countDutyHours($userID, $day) {
        $day = new Date($day->getDate(), $day->getMonth(), $day->getYear());
        while ($day->getDay() != "Monday") {
            $day = $day->minusDay(1);
        }
        
        $hours = 0;
        for ($i = 0; $i < 7; ++$i) {
            for ($j = $i * 17 + 1; $j <= ($i + 1) * 17; ++$j) {
                foreach (array('yih','cl') as $k) {
                    $dutyCheck = new DailyDuty($j, null, null, null, $k, $day->getDate(), $day->getMonth(), $day->getYear());
                    if ($this->getSupervisorID($dutyCheck) == $userID) {
                        $hours += $this->getIntervalDuty($j);
                    }
                }
            }
            $day = $day->addDay(1);
        }
        return $hours;
    }
    
    public function grabDuty($user, $duty) {
        
        //if($user->getPosition() == 'Subcom' && $duty->getLocation() == 'YIH' && $duty->getTime() == '08.30-09.00'){
        //    return false;
        //}

        try{
            if($user->getPosition() == 'Subcom' && $duty->getLocation() == 'yih' && $this->isOpeningDuty($duty)){
                throw new Exception('You have the key to open the centre meh? Only MC can grab opening YIH duty.');
            }
            
            if($user->getPosition() == 'Subcom' && $duty->getLocation() == 'yih' && $this->isClosingDuty($duty)){
                throw new Exception('You have the key to close the centre meh? Only MC can grab closing YIH duty.');
            }
            
            if($user->getPosition() == 'Subcom' && $this->isMoreThanKHours($user, $duty, 400)) {
                throw new Exception('Subcom is not allowed to have more than 4 hours consecutive duty.');
            }
            
            if($user->getPosition() == 'Subcom' && $this->isConsecutiveDiffPlace($user, $duty)) {
                throw new Exception('Subcom is not allowed to have consecutive duty in different place.');
            }
            
            if($user->getPosition() == 'Subcom' && 
               $this->countDutyHours($user->getID(), $duty) + $this->getIntervalDuty($duty->getID()) > 14) {
                throw new Exception('Subcom is not allowed to have more than 14 duty hours in a week.');
            }
            
            $params = array('schedule_id' => $duty->getID(),
                            'date' => $duty->getDate(),
                            'month' => $duty->getMonth(),
                            'year' => $duty->getYear(),
                            'location' => $duty->getLocation());
            
            $otherLocation = ($duty->getLocation() == "yih" ? "cl" : "yih");
            $dutyOtherLocation = new DailyDuty($duty->getID(), null, null, null, $otherLocation, $duty->getDate(), $duty->getMonth(), $duty->getYear());
            
            if ($user->getID() > 0 && $this->getSupervisorID($dutyOtherLocation) == $user->getID()) {
                throw new Exception('The user have a duty in the same time in the other venues.');
            }
            
            $this->conn = connect();
            $released = $this->conn->prepare("SELECT count(*) FROM released_duty 
                WHERE schedule_id = :schedule_id AND date = :date AND month = :month 
                AND year = :year AND venue = :location");
            $released->execute($params);
            
            if($released->fetchColumn() == 1){
                //Remove from released_duty first to prevent multiple insert.
                $delete_released = $this->conn->prepare("DELETE FROM released_duty
                    WHERE schedule_id = :schedule_id AND date = :date AND month = :month 
                    AND year = :year AND venue = :location");
                $delete_released->execute($params);
                if($delete_released->rowCount() != 1){
                    throw new Exception('Duty grab failed, someone else may have grabbed it');
                }

                //Insert into grabbed_duty
                $grab = $this->conn->prepare("INSERT INTO grabbed_duty (supervisor_id, schedule_id, date, month, year, venue) 
                    VALUES (:supervisor_id, :schedule_id, :date, :month, :year, :location)");
                $params['supervisor_id'] = $user->getID();
                $grab->execute($params);
                if($grab->rowCount() != 1){
                    throw new Exception('Duty grab failed for unknown reason. Contact technical cell (James).');
                }
                $_SESSION['success'] = 'Successfully grabbed the duty(ies)';
                return true;
            }
            return false;
        } catch(Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: index");
            exit;
        }
    }
    
    public function releaseDuty($user, $duty) {
        try{
            $this->conn = connect();
            //Check in grabbed_duty first
            $params = array('supervisor_id' => $user->getID(),
                            'schedule_id' => $duty->getID(),
                            'date' => $duty->getDate(),
                            'month' => $duty->getMonth(),
                            'year' => $duty->getYear(),
                            'location' => $duty->getLocation());
            $userGrabbed = $this->conn->prepare("SELECT count(*) FROM grabbed_duty 
                WHERE supervisor_id = :supervisor_id AND schedule_id = :schedule_id AND date = :date 
                AND month = :month AND year = :year AND venue = :location");
            $userGrabbed->execute($params);
            if($userGrabbed->fetchColumn() == 1){
                //remove from grabbed_duty
                $removeGrabbed = $this->conn->prepare("DELETE FROM grabbed_duty
                    WHERE supervisor_id = :supervisor_id AND schedule_id = :schedule_id AND date = :date 
                    AND month = :month AND year = :year AND venue = :location");
                $removeGrabbed->execute($params);
                
                //insert into released_duty
                $release = $this->conn->prepare("INSERT INTO released_duty (supervisor_id, schedule_id, date, month, year, venue) 
                    VALUES (:supervisor_id, :schedule_id, :date, :month, :year, :location)");
                $release->execute($params);
                if($release->rowCount() != 1){
                    throw new Exception("Duty release insertion failed! Contact technical cell (James).");
                    //die("Duty release insertion failed!");
                }
                $_SESSION['success'] = 'Successfully dropped the duty(ies)';
                return true;
            }

            //Check in regular schedule
            $regularParams = array('supervisor_id' => $user->getID(),
                            'day' => $duty->getDay(),
                            'id' => $duty->getID());
            $this->conn = connect();
            $regular = $this->conn->prepare("SELECT count(*) FROM duty_schedule
                WHERE supervisor_{$duty->getLocation()} = :supervisor_id AND day = :day AND id = :id");
            $regular->execute($regularParams);
            
            if($regular->fetchColumn() == 1){
                //insert into released_duty
                $release = $this->conn->prepare("INSERT INTO released_duty (supervisor_id, schedule_id, date, month, year, venue) 
                    VALUES (:supervisor_id, :schedule_id, :date, :month, :year, :location)");
                $release->execute($params);
                if($release->rowCount() != 1){
                    throw new Exception("Duty release insertion failed!  Contact technical cell (James).");
                    //die("Duty release insertion failed!");
                }
                $_SESSION['success'] = 'Successfully dropped the duty(ies)';
                return true;
            }

            return false;
        } catch(Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: index");
            exit;
            //echo 'ERROR: ' . $e->getMessage();
        }
    }
    
    public function releaseDuties($user, $date, $duties) {
        foreach ($duties as $duty) {
            self::releaseDuty($user, $date, $duty);
        }
        return true;
    }
    
    /*public function getSupervisorID($duty) {
        $queryCondition = array('id' => $duty->getID());
        $dutySchedule = $this->dutySchedule->get($queryCondition)[0];
        $queryCondition = array('date' => $duty->getDate(),
                                'month' => $duty->getMonth(),
                                'year' => $duty->getYear(),
                                'schedule_id' => $duty->getID(),
                                'venue' => $duty->getLocation());
        $dropList = $this->dropList->get($queryCondition);  
        $grabList = $this->grabList->get($queryCondition);
        if (count($dropList) > 0 || count($grabList) > 0) {
            if (count($dropList) == count($grabList)) {
                return $grabList[count($grabList)-1]["supervisor_id"];
            } else if (count($dropList) - count($grabList) == 1) {
                return -1;
            }   
        } else {
            return $dutySchedule["supervisor_".$duty->getLocation()];
        }
    }*/
    
    public function getSupervisorID($duty) {
        $queryCondition = array('id' => $duty->getID());
        $dutySchedule = $this->dutySchedule->get($queryCondition)[0];
        $queryCondition = array('date' => $duty->getDate(),
                                'month' => $duty->getMonth(),
                                'year' => $duty->getYear(),
                                'schedule_id' => $duty->getID(),
                                'venue' => $duty->getLocation());
        $dropList = $this->dropList->get($queryCondition);  
        $grabList = $this->grabList->get($queryCondition);
        if (count($dropList) == 0 && count($grabList) == 0) {
            return $dutySchedule["supervisor_".$duty->getLocation()];
        } else if (count($dropList) > 0) {
            return $dropList[count($dropList)-1]["supervisor_id"] * (-1);
        } else if (count($grabList) > 0) {
            return $grabList[count($grabList)-1]["supervisor_id"];
        }
    }

    public function getDutySchedule($date, $month, $year)
    {
        $tempDate = new Date($date, $month, $year);
        $dayName = $tempDate->getDay();
        
        $queryCondition = array(); $queryCondition["day"] = $dayName;
        $dutySchedule = $this->dutySchedule->get($queryCondition);
        
        $queryCondition = array(); 
        $queryCondition["date"] = $date;
        $queryCondition["month"] = $month; 
        $queryCondition["year"] = $year;
        
        for ($i = 0; $i < count($dutySchedule); ++$i) {
            $queryCondition["schedule_id"] = $dutySchedule[$i]["id"];
            foreach (["yih","cl"] as $venue) {
                $duty = new DailyDuty($dutySchedule[$i]["id"], null, null, null, $venue, $date, $month, $year);
                $dutySchedule[$i]["supervisor_".$venue] = $this->getSupervisorID($duty);
            }
        }

        return $dutySchedule;
    }
    
    public function getOriginalDutySchedule($day)
    {
        $queryCondition = array('day' => $day);
        $dutySchedule = $this->dutySchedule->get($queryCondition);
        return $dutySchedule;
    }
    
    public function getIntervalDuty($dutySessionID) {
        $timeDuty = $this->getTimeDuty($dutySessionID);
        $startTime = intval(substr($timeDuty,0,2)) * 100 + intval(substr($timeDuty,3,2));
        $endTime = intval(substr($timeDuty,6,2)) * 100 + intval(substr($timeDuty,9,2));
        if (($endTime - $startTime) % 100 == 0) {
            return ($endTime - $startTime) / 100;
        } else {
            return floor(($endTime - $startTime) / 100) + 0.5;
        }
    }
    
    public function getTimeDuty($dutySessionID) {
        $queryCondition = array('id' => $dutySessionID);
        return $this->dutySchedule->get($queryCondition)[0]['time'];
    }
    
    public function getDayNameDuty($dutySessionID) {
        $queryCondition = array('id' => $dutySessionID);
        return $this->dutySchedule->get($queryCondition)[0]['day'];
    }
    
    public function getAvailableDuties($date, $month, $year, $venue) {
        if ($date == null) {
            return $this->dropList->getQuery("SELECT DISTINCT date,month,year,venue FROM released_duty ORDER BY year ASC, month ASC, date ASC");
        } else {
            $queryCondition = array('date' => $date,
                                    'month' => $month,
                                    'year' => $year,
                                    'venue' => $venue);
            return $this->dropList->get($queryCondition,"schedule_id ASC");
        }
    }
    
 //    public function swapDuty($firstUser, $secondUser, $firstDuty, $secondDuty) {
 //    	return true;
 //    }
    
 //    public function getHoursPerMonth($user, $date) {
 //    	$month = $date->getMonth();
 //    	$user = $user->getMatric();
 //    	$num_hours = 0;
 //    	// stub: connect to database
 //    	// stub: query duty based on user, month, year
 //    	// stub: calculate duty hours
 //    	return $num_hours;
 //    }
}

?>