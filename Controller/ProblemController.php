<?php

require_once(dirname(__FILE__).'/../Utils/Connection.php');
require_once(dirname(__FILE__).'/../Model/Sign.php');
require_once(dirname(__FILE__).'/../Model/User.php');
require_once(dirname(__FILE__).'/../Utils/DBoperation.php');
require_once(dirname(__FILE__).'/UserController.php');
include_once(dirname(__FILE__).'/../Utils/Mailer.php');

class ProblemController {
    private static $instance;
    private $reportList;
    
    private function __construct() {
        $this->reportList = new DBOperation("problems");
    }
   
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addReport($report) {
        $userController = UserController::getInstance();
        $this->reportList->insertData($report);
        $notifyTargets = $userController->getProblemReportTargets();
        $title = "[NUSSU commIT PROBLEM REPORT] New problem report is added. ".$report['pc_number']." on ".$report['venue'];
        $str = "Dear Technical Cell,\n\n
                New computer problem is reported:\n
                PC Number: {$report['pc_number']}\n
                Venue: {$report['venue']}\n
                Description: {$report['description']}\n
                Critical: {$report['critical']}\n
                Please fix the problem as soon as possible.\n".FOOTER_MESSAGE;
        sendMail($title, $str, $notifyTargets);
    }

    public function changeBlockStatus($userID, $reportID) {
        $isBlocked = $this->reportList->get(array('id' => $reportID))[0]["blocked"];
        $this->reportList->updateData(array('id' => $reportID),
                                  array('blocked' => 1 - $isBlocked,
                                        'handler_id' => $userID));
    }

    public function changeCriticalStatus($userID, $reportID) {
        $isCritical = $this->reportList->get(array('id' => $reportID))[0]["critical"];
        $this->reportList->updateData(array('id' => $reportID),
                                  array('critical' => 1 - $isCritical,
                                        'handler_id' => $userID));
    }

    public function changeFixStatus($userID, $reportID) {
        $isFixed = $this->reportList->get(array('id' => $reportID))[0]["fixed"];
        $this->reportList->updateData(array('id' => $reportID),
                                  array('fixed' => 1 - $isFixed,
                                        'handler_id' => $userID));
    }

    public function changeFixableStatus($userID, $reportID) {
        $isFixable = $this->reportList->get(array('id' => $reportID))[0]["fixable"];
        $this->reportList->updateData(array('id' => $reportID),
                                  array('fixable' => 1 - $isFixable,
                                        'handler_id' => $userID));
    }

    public function changeRemarks($userID, $remark, $reportID) {
        $this->reportList->updateData(array('id' => $reportID),
                                  array('remarks' => $remark,
                                        'handler_id' => $userID));
    }
    
    public function getAllReportList() {
        return $this->reportList->getAll();
    }

    public function getUnfixedReportList() {
        return $this->reportList->get(array('fixed' => 0,
                                            'fixable' => 1));
    }

    public function getUnfixedCriticalReportList() {
        return $this->reportList->get(array('fixed' => 0,
                                            'critical' => 1));
    }
    
}
?>
