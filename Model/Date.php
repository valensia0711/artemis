<?php
class Date {
    private $date = "";
    private $month = "";
    private $year = "";
    
    public function __construct($date, $month, $year) {
    	$this->date = $date;
    	$this->month = $month;
    	$this->year = $year;
    }
    
    public static function getToday() {
        return new self(date("d"),date("m"),date("Y"));
    }
   
    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getMonth() {
        return $this->month;
    }

    public function setMonth($month) {
        $this->month = $month;
    }

    public function getYear() {
        return $this->year;
    }

    public function setYear($year) {
        $this->year = $year;
    }
       
    public function getDay() {
    	// stub: compute day
        $tempDate = $this->year."-".$this->month."-".$this->date;
        return date('l', strtotime( $tempDate));
    }
    
    public function printToString() {
        $monthName = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
        $string = $this->date."-".$monthName[$this->month-1]."-".$this->year;
        return $string;
    }
    
    public static function stringToDay($string) {
        $day = $string[0].$string[1];
        $month = $string[2].$string[3];
        $year = $string[4].$string[5].$string[6].$string[7];
        return new self(intval($day),intval($month),intval($year));
    }
    
    public function dayToString() {
        $date = $this->date;
        $month = $this->month;
        $year = $this->year;
        while (strlen($date) < 2) {
            $date = "0".$date;
        }
        while (strlen($month) < 2) {
            $month = "0".$month;
        }
        while (strlen($year) < 4) {
            $year = "0".$year;
        }
        return $date.$month.$year;
    }
    
    public function addDay($numberOfDays) {
        $date = new DateTime($this->year."-".$this->month."-".$this->date);
        $date->modify("+". $numberOfDays ." day");
        return new self($date->format('d'),$date->format('m'),$date->format('Y'));
    }
    
    public function minusDay($numberOfDays) {
        $date = new DateTime($this->year."-".$this->month."-".$this->date);
        date_sub($date, date_interval_create_from_date_string($numberOfDays.' days'));
        return new self($date->format('d'),$date->format('m'),$date->format('Y'));
    }
}
?>