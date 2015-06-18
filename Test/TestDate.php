<?php
include("../Model/Date.php");

$dateTest1 = new Date(31,12,2014);
assert($dateTest1->getDay() == "Wednesday");

$dateTest2 = new Date(31,12,2015);
assert($dateTest2->getDay() == "Thursday");

echo "TEST FINISHED";

?>
