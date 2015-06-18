<?php 
	include_once(dirname(__FILE__).'/../Controller/LoginController.php');
	
	$instance = LoginController::getInstance();
	//Already logged-in
	if((isset($_SESSION['user_id'] )&& isset($_SESSION['user_name']))){
		header("Location: TestLogin.php?message=You%20are%20already%logged in");
		exit;
	}
	
	if(isset($_POST['submit'])){
	
		$user_name = $_POST['user_name'];
		$hashedPassword = sha1($_POST['password']);

		$login = $instance->login($user_name, $hashedPassword);
		
		if($login == 0){
			header("Location: TestLogin.php?message=Your%20account%20is%20not%20activated");
			exit;
		} else if($login == -1){
			header("Location: TestLogin.php?message=Incorrect%20username%20and%20password%20combination");
			exit;
		} else{
			$_SESSION['user_name'] = $login->name;
			$_SESSION['user_id'] = $login->id;
			if($login->isAdmin == 1){
				$_SESSION['user']="admin";
				header("Location: TestLogin.php?message=You%20are%20Admin");
			}
			else {
				$_SESSION['user']="member";
				header("Location: TestLogin.php?message=You%20are%20normal%20user");
			}
		}
	}	
?>

<html>
	<head>
		<title>Login page</title>
	</head>
	<body onload="document.loginform.user_name.focus();">	
		<div id="header">
			<h1 align="center">NUSSU commIT</h1>
		</div>
		<div id="main">
			<table id="structure">
			<tr>
				<td id="page">
					<h2>User Login</h2>
					<form action="TestLogin.php" method="post" name="loginform">
					<table>
						<tr>
							<td>Username:</td>
							<td><input type="text" name="user_name" maxlength="30" value=""/></td>
						</tr>
						<tr>
							<td>Password:</td>
							<td><input type="password" name="password" maxlength="30" value=""/></td>
						</tr>
						<tr>
							<td colspan="2"><input type="submit" name="submit" value="Login" /></td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
		</table>
	</div>
	<?php //include("includes/footer.php"); ?>
</body>
</html>
