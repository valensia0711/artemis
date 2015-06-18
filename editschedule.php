<?php
    session_start();
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
        header("Location: login");
        exit;
    }	
?>
<?php include(dirname(__FILE__).'/Controller/DutyController.php');?>
<?php include(dirname(__FILE__).'/Controller/UserController.php');?>
<?php
    $dutyController = DutyController::getInstance();
    $userController = UserController::getInstance();
    $userID = $_SESSION['user_id'];
    if ($userController->isAdmin($userID) == 0) {
        header("Location: index");
		exit;
    }
    if(isset($_SESSION['error'])){
        echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
        unset($_SESSION['error']);
    }
    if(isset($_SESSION['success'])){
        echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';
        unset($_SESSION['success']);
    }
    if (isset($_POST['assign_to'])) {
        $newUser = new User($_POST['assign_to'], null, null, null, null, null, null, null);
        for ($i = 1; $i <= 119; ++$i) {
            foreach (['yih','cl'] as $j) {
                if (isset($_POST[$j."_".$i])) {
                    $duty = new Duty($i, null, null, null, $j);
                    $dutyController->assignPermanentDuty($newUser,$duty);
                }
            }
        }
        header("Location: index");
        exit;
    }
    $dayList = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
    $day = "Monday";
    $dutySchedule = $dutyController->getOriginalDutySchedule($day);
?>

<html>
	<head>
		<title>NUSSU commIT</title>
		<link href="includes/css/bootstrap.min.css" rel="stylesheet">
		<link href="includes/css/bootstrap-theme.min.css" rel="stylesheet">
        <link href="includes/css/style.css" rel="stylesheet">
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="includes/js/bootstrap.min.js"></script>
	</head>
    <body>
        <?php $page = "home"; ?>
        <?php include(dirname(__FILE__).'/includes/header.php');?>
        <div class="container">
            <div class="row">
                <div class="col-sm-7">
                    <h1>Edit Permanent Schedule</h1>
                </div>
                <div class="col-sm-5 well">
                    <a class="btn btn-default" href="edittempschedule">Edit Temporary</a> 
                    <button class="btn btn-default" onclick="clearSelection('yih');clearSelection('cl')">Clear Selection</button>
                </div>
            </div>
        <div class="row">
        <form action="editschedule" method="post">
            <p align="center">
            <label for="assign_to">Assign slot to:</label>
            <select name="assign_to">
                <?php
                    $allUsers = $userController->getAllUser();
                    usort($allUsers, function($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    });
                    echo "<option value='0'>NO_DUTY</option>";
                    for ($i = 0; $i < count($allUsers); ++$i) {
                        echo "<option value='".$allUsers[$i]['id']."'>".$allUsers[$i]['name']."</option>";
                    }
                ?>
                <input type='submit' class='btn btn-primary'/>
            </select>
            </p>
            <table border=1 class="table edittable">
                <tr class='table_header'>
                    <td style="width: 6%">Date</td>
                    <td style="width: 4.8%">Venue</td>
                    <?php
                    
                    for ($i = 0; $i < count($dutySchedule); ++$i)
                    {
                        echo "<th class=\"breakword timeslot\">".$dutySchedule[$i]["time"]."</th>";
                    }
                    ?>
                </tr>

                <?php
                function printTable($location) {
                    global $userController;
                    global $dutySchedule;
                    global $day;

                        for ($j = 0; $j < count($dutySchedule); ++$j)
                        {
                            $name = $userController->getUserName($dutySchedule[$j]["supervisor_".$location]);
                            $dutyID = $dutySchedule[$j]["id"];
                            $onclickFunction = "\"cellClickHandler('" . $location . "', " . $dutyID . ", '" . $day ."')\"";
                            $onmouseoverFunction = "\"cellMouseoverHandler('" . $location . "', " . $dutyID . ", '" . $day ."')\"";
                            $id = "cell_" . $location . "_" . $dutyID . "_" . $day;
                            if ($name == "Drop") {
                                echo "<td class='dropped_cell' id=" . $id . " onclick=" . $onclickFunction . " onmouseover=" . $onmouseoverFunction . ">";
                            } else if ($name == "NO_DUTY") {
                                echo "<td class='noduty_cell' id=" . $id . " onclick=" . $onclickFunction . " onmouseover=" . $onmouseoverFunction . ">";
                            } else {
                                echo "<td id=" . $id . " onclick=" . $onclickFunction . " onmouseover=" . $onmouseoverFunction . ">";
                            }
                            echo $name;
                            echo "<input type='checkbox' name='".$location."_".$dutyID."' style='display:none' />";
                            echo "</td>";
                        }
                }


                for ($i = 0; $i < 7; ++$i)
                {
                    echo "<tr class='blank_row'/>";
                    $day = $dayList[$i];
                    $dutySchedule = $dutyController->getOriginalDutySchedule($day);
                    $cellClass = ($i % 2 == 0 ? "yellow_cell" : "white_cell");
                    echo "<tr class=$cellClass>\n";
                        echo "<th rowspan=2 class=\"breakword\">".$day."</th>";
                        echo "<th>YIH</th>";
                        printTable("yih");
                    echo "</tr>\n";
                    echo "<tr class=$cellClass>\n";
                        echo "<th>CL</th>";
                        printTable("cl");
                    echo "</tr>\n";
                }
                ?>
            </table>
        </form>
        </div>
        </div>
        <?php include(dirname(__FILE__).'/includes/footer.php');?>
  </body>
  <script>
    var currentSelection = null;
    var selections = {};

    function clearSelection(location, day) {
      $('input').each(function() {
        if ($(this).attr('type') === 'checkbox') {
          var par = $(this).parent();
          var dutyDay = par.attr('id').split('_')[3];
          var dutyLoc = par.attr('id').split('_')[1];
          console.log(par.attr('id'));
          console.log(dutyDay);
          console.log(dutyLoc);
          if (dutyDay === day && dutyLoc == location) {
            $(this).prop('checked', false);
            par.removeClass('selected_cell');
          }
        }
      });
    }

    function select(dutyLocation, dutyId, dutyDay) {
      var name = dutyLocation + '_' + dutyId;
      $('input[name=' + name + ']').prop('checked', true);
      $('#cell_' + name + '_' + dutyDay).addClass('selected_cell');
    }

    function getCell(dutyLocation, dutyId) {
      var cellId = "cell_" + dutyLocation + "_" + dutyId;
      console.log(cellId);
      return $('#' + cellId);
    }

    function startSelection(dutyLocation, dutyId, dutyDay) {
      clearSelection(dutyLocation, dutyDay);
      selections[dutyLocation + "_" + dutyDay] = [];
      selections[dutyLocation + "_" + dutyDay][0] = dutyId;
      currentSelection = dutyLocation + "_" + dutyDay;
      select(dutyLocation, dutyId, dutyDay);
    }

    function endSelection(dutyLocation, dutyId, dutyDay) {
      var selectionStart = selections[currentSelection][0];
      var startLoop = Math.min(selectionStart, dutyId);
      var endLoop = Math.max(selectionStart, dutyId);
      clearSelection(dutyLocation, dutyDay);
      for (var i = startLoop; i <= endLoop; i++) {
        select(dutyLocation, i, dutyDay);
      }
      currentSelection = null;
    }

    function cellClickHandler(dutyLocation, dutyId, dutyDay) {
      if (currentSelection === null) {
        startSelection(dutyLocation, dutyId, dutyDay);
      } else {
        if (currentSelection === dutyLocation + "_" + dutyDay) {
          endSelection(dutyLocation, dutyId, dutyDay);
        }
      }
    }
    function cellMouseoverHandler(dutyLocation, dutyId, dutyDay) {
      if (currentSelection === dutyLocation + "_" + dutyDay) {
        var selectionStart = selections[currentSelection][0];
        var startLoop = Math.min(selectionStart, dutyId);
        var endLoop = Math.max(selectionStart, dutyId);
        clearSelection(dutyLocation, dutyDay);
        for (var i = startLoop; i <= endLoop; i++) {
          select(dutyLocation, i, dutyDay);
        }
      }
    }

  </script>

</html>
