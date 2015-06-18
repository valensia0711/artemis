<?php
    session_start();
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    include_once(dirname(__FILE__).'/Utils/Mailer.php');
    include_once(dirname(__FILE__).'/Utils/Constants.php');
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
		header("Location: login");
		exit;
    }

    $user_controller = UserController::getInstance();
    if(!$user_controller->isAdmin($_SESSION['user_id'])){
        header("Location: index");
        exit;
    }

    function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

	if(isset($_POST['delete_user'])){
        $len = count($_POST['users']);
        for($i = 0; $i < $len; $i++){
            $user_controller->removeUser($_POST['users'][$i]);
        }
        $_SESSION['success'] = "User Deleted!";
        header("Location: members");
        exit;
    }

    if(isset($_POST['reset_password'])){
        $len = count($_POST['users']);
        for($i = 0; $i < $len; $i++){
            $new_password = generateRandomString();
            $user_controller->resetPassword($_POST['users'][$i], $new_password);
            $user[] = $user_controller->getUserInfo($_POST['users'][$i]);

            $mailSubject = "NUSSU commIT Account Password Reset";
            $mailBody = "Your password has been reset to: ".$new_password.".\nPlease login and change your password immediately".FOOTER_MESSAGE;
            sendMail($mailSubject, $mailBody, $user);
        }
        $_SESSION['success'] = "Password reset!";
        //TODO: resend email
        header("Location: members");
        exit;
    }

    if(isset($_POST['change_status'])){
        $len = count($_POST['users']);
        for($i = 0; $i < $len; $i++){
            $user_controller->changeUserStatus($_POST['users'][$i]);
        }
        $_SESSION['success'] = "Status Updated";
        header("Location: members");
        exit;
    }
?>