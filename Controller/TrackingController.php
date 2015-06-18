<?php

require_once(dirname(__FILE__).'/../Utils/Connection.php');
require_once(dirname(__FILE__).'/../Utils/DBoperation.php');

class TrackingController {
    private static $instance;
    private $trackingDefault;
    private $trackingList;
    
    private function __construct() {
        $this->trackingList = new DBOperation("tracking");
        $this->trackingDefault = (new DBOperation("trackingdefault"))->getAll();
    }
   
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function addTrackingMonth($month,$year) {
        for ($i = 0; $i < count($this->trackingDefault); ++$i) {
            $row = $this->trackingDefault[$i];
            $insertValues = array('month'       => $month,
                                  'year'        => $year,
                                  'indexNo'     => $row['indexNo'],
                                  'treasurer'   => $row['treasurer'],
                                  'comcen'      => $row['comcen']);
            $this->trackingList->insertData($insertValues);
        }
    }
    
    //$user is either 'treasurer' or 'comcen'
    public function changeTracking($user,$month,$year,$indexNo,$changeTo) {
        $updateCondition = array('month'       => $month,
                                  'year'        => $year,
                                  'indexNo'     => $indexNo);
        if ($user == 'treasurer') {
            $updateValue = array('treasurer' => $changeTo);
        } else if ($user == 'comcen') {
            $updateValue = array('comcen' => $changeTo);
        }
        $this->trackingList->updateData($updateCondition, $updateValue);
    }
    
    public function getMonthData($month,$year) {
        $searchCondition = array('month' => $month,
                                 'year'  => $year);
        return $this->trackingList->get($searchCondition);
    }
    
    public function getTrackingDefault() {
        return $this->trackingDefault;
    }
    
    public function getCurrentStep($month, $year) {
        $monthData = $this->getMonthData($month,$year);
        for ($i = 0; $i < count($monthData); ++$i) {
            if ($monthData[$i]['treasurer'] == 0 || $monthData[$i]['comcen'] == 0) {
                return $monthData[$i]['indexNo'];
            }
        }
        return count($monthData) + 1;
    }
    
}

?>