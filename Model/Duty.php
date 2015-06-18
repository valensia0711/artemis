<?php
class Duty {
    private $id;
    private $day;
    private $user;
    private $time;
    private $location;
    
    public function __construct($id, $day, $user, $time, $location) {
        $this->id = $id;
    	$this->day = $day;
        $this->user = $user;
        $this->time = $time;
        $this->location = $location;
    }
    
    public function getID() {
    	return $this->id;
    }
    
    public function setID($id) {
    	$this->id = $id;
    }
   
    public function getDay() {
        return $this->day;
    }

    public function setDay($day) {
        $this->day = $day;
    }
    
    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }
    
    public function getTime() {
        return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation($location) {
        $this->location = $location;
    }
   }
class DailyDuty extends Duty {
	private $date;
	private $month;
	private $year;

    public function __construct($id, $day, $user, $time, $location, $date, $month, $year) {
    	parent::__construct($id, $day, $user, $time, $location);
    	$this->date = $date;
    	$this->month = $month;
    	$this->year = $year;
    }
    public function getDate() {
        return  $this->date;
    }
    
    public function getMonth() {
    	return $this->month;
   	}
   public function getYear() {
   	return $this->year;
   }
}

?>