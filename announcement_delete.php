<?php
    session_start();
    include_once(dirname(__FILE__).'/Controller/UserController.php');
    include_once(dirname(__FILE__).'/Controller/AnnouncementController.php');
    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])){
		header("Location: login");
		exit;
    }
    $announcement_controller = AnnouncementController::getInstance();

    if(!isset($_GET['id'])){
        header("Location: index");
        exit;
    }

    $id = $_GET['id'];
    if($announcement_controller->deleteAnnouncement($id)){
        $_SESSION['success'] = "Successfully deleted the announcement.";
        header("Location: announcement");
        exit;
    }else{
        $_SESSION['error'] = "Failed to delete announcement.";;
        header("Location: announcement");
        exit;
    }
