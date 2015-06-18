<?php 
include_once(dirname(__FILE__).'/../Controller/UserController.php');
include_once(dirname(__FILE__).'/../Controller/DutyController.php');

$user1 = new Member(1, "testuser1", "A0075102W", 1234, "a0075102@nus.edu.sg", "Technical", "Manager", 1);
$user2 = new Member(2, "testuser2", "A0075104W", 1234, "a0075103@nus.edu.sg", "Publicity", "Manager", 1);

$instance = UserController::getInstance();

if ($instance->addUser($user1, "password")) echo "Added " . $user1->getMatricNumber() . "</br>";
if ($instance->addUser($user2, "password")) echo "Added " . $user2->getMatricNumber() . "</br>";
if ($instance->removeUser($user1)) echo "Removed " . $user1->getMatricNumber() . "</br>";

$user3 = new Member(5, "BC", "B0075103", 12345, "test@yahoo.com", "Presidential", "Subcom", 1);
$user4 = new Member(4, "ABCDE", "A0075110", 123144, "harta_wijaya@nus.edu.sg", "Presidential", "Manager", 1);
$duty1 = new Duty(1, "Monday", 5, "08.00-08.30", "cl");

$instance = DutyController::getInstance();
if ($instance->assignPermanentDuty($user4, $duty1)) echo "Assigned permanent duty to " . $user4->getMatricNumber() . "</br>";
else echo "Error assigning permanent duty" . "</br>";

//Insert duty to released duty manually first (for now)
$duty2 = new DailyDuty(1, "Monday", 4, "08.30-09.00", "YIH", 30, "December", 2013);
if($instance->releaseDuty($user4, $duty2)) echo "Release from default" . "</br>";
else echo "Error release from default" . "</br>";
if ($instance->grabDuty($user3, $duty2)) echo "Grabbed by user3" . "</br>";
else echo "Error grab" . "</br>";
if($instance->releaseDuty($user3, $duty2)) echo "Release from grabbed" . "</br>";
else echo "Error release from grabbed" . "</br>";
if($instance->grabDuty($user4, $duty2)) echo "Grabbed back by user4" . "</br>";
else echo "Error grab back" . "</br>";

?>