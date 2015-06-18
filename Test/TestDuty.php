<?php session_start(); ?>
<?php 
include("../Controller/DutyController.php");
$types = array('Grab Duty', 'Release Duty', 'Add Duty');
?>



<html>
<head>
<title>Duty</title>
	</head>
<body>
	<div id="main">
		<table id="structure">
		<tr>
			<td id="page">
				<h2>Edit Duty</h2>
				<?php if(isset($message)) echo "<span style='font-weight:bold'>{$message}</span><br /><br />"; ?>
				Fill in all the fields<br /><br />
				<form action="TestDuty.php" method="post" >
					<table >
							<td>Type:</td>
							<td>
								<?php
									echo '<select name="type">';
									foreach($types as $type){
										if($result2['type']==$type){
											echo '<option value="'.$type.'" selected="selected">'.$type.'</option>';
										} else {
											echo '<option value="'.$type.'">'.$type.'</option>';
										}
									}
									echo '</select>';
								?>
							</td>
						<tr>
							<td>Duty:</td>
							<td><input type="text" name="duty_id" maxlength="20" value="" /></td>
						</tr>
						<tr>
							<td colspan="2"><input type="submit" name="submit" value="Submit" /></td>
						</tr>
					</table>
				</form>
			</td>
		</tr>
		</table>
	</div>
</html>