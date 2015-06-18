<?php session_start(); ?>
<?php 
include("Controller/UserController.php");

        if (isset($_POST['submit'])) {
		$check = 1;
		if(isset($_POST['email'])){
			$email = $_POST['email'];
			if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) { 
				$message = 'Invalid e-mail address<br />';
				$check = 0;
			}
		}
		if(isset($_POST['user_name']) && $_POST['user_name']==''){
			$message .= 'Username cannot be empty<br />';
			$check = 0;
		}	echo 'Z';
		if($check){
			$choice = true;
			$cell = $_POST['cell'];
			$position = $_POST['position'];
			
			function random_gen($length){
				$random= "";
				srand((double)microtime()*1000000);
				$char_list  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
				$char_list .= "abcdefghijklmnopqrstuvwxyz";
				$char_list .= "1234567890";
				// Add the special characters to $char_list if needed

				for($i = 0; $i < $length; $i++){
					$random .= substr($char_list,(rand()%(strlen($char_list))), 1);
				}
				return $random;
			}
			
			$password = "default";
$user1 = new Member(1, $_POST['user_name'], $_POST['matricnumber'], $_POST['number'], $_POST['email'], $_POST['cell'], $_POST['position'], 1);
			if(isset($_POST['matricnumber'])){
			$instance = UserController::getInstance();
                                if ($instance->addUser($user1, $password)) echo "Added " . $user1->getMatricNumber();
                                else {
					global $choice;
					$choice = false;
                                        echo "Student is already in database";
                                }
			}
		}
	}

$cells = array('Presidential','Center and Ops','Technical','Publicity','Marketing','Welfare','Training','Alumni');
$positions = array('Manager','Asst. Manager','Subcom','Chairman','Vice Chairman','Secretary','Treasurer','Asst. Treasurer','Advisor');
?>



<html>
<head>
<title>Add User</title>
	</head>
<body>
	<div id="main">
		<table id="structure">
		<tr>
			<td id="page">
				<h2>Add User</h2>
				<?php if(isset($message)) echo "<span style='font-weight:bold'>{$message}</span><br /><br />"; ?>
				Fill in all the fields<br /><br />
				<form action="TestAddUser.php" method="post" >
					<table >
						<tr>
							<td>Cell:</td>
							<td>
								<?php
									echo '<select name="cell">';
									foreach($cells as $cell){
										if($result2['cell']==$cell){
											echo '<option value="'.$cell.'" selected="selected">'.$cell.'</option>';
										} else {
											echo '<option value="'.$cell.'">'.$cell.'</option>';
										}
									}
									echo '</select>';
								?>
							</td>
						</tr>
						<tr>
							<td>Position:</td>
							<td>
								<?php
									echo '<select name="position">';
									foreach($positions as $position){
										if($result2['position']==$position){
											echo '<option value="'.$position.'" selected="selected">'.$position.'</option>';
										} else {
											echo '<option value="'.$position.'">'.$position.'</option>';
										}
									}
									echo '</select>';
								?>
							</td>
						</tr>
						<tr>
							<td>User Name:</td>
							<td><input type="text" name="user_name" maxlength="20" value="" /></td>
						</tr>
					
						<tr>
							<td>Matric Number:</td>
							<td><input type="text" name="matricnumber" maxlength="15" value="" /></td>
						</tr>
						<tr>
							<td>Email Address:</td>
							<td><input type="text" name="email" maxlength="60" value="" /></td>
						</tr>
						<tr>
							<td>HandPhone Number:</td>
							<td><input type="text" name="number" maxlength="20" value="" /></td>
						</tr>
						<tr>
							<td colspan="2"><input type="submit" name="submit" value="Add User" /></td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
		</table>
	</div>
</html>