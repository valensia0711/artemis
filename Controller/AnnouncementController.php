<?php
/**
* Manage adding announcement.
**/
require_once(dirname(__FILE__).'/../Utils/Connection.php');
require_once(dirname(__FILE__).'/../Model/User.php');
require_once(dirname(__FILE__).'/../Model/Duty.php');
require_once(dirname(__FILE__).'/../Model/Date.php');
require_once(dirname(__FILE__).'/../Utils/DBoperation.php');
require_once(dirname(__FILE__).'/../Model/Announcement.php');

class AnnouncementController {
    private static $instance;
    private $conn;
    private $annlist;

    private function __construct(){
        $this->conn = connect();
        $this->annlist = new DBOperation("announcement");
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function isAnnouncementExist($id, $conn){
        $stmt = $conn->prepare('SELECT count(*) FROM announcement WHERE id = :id');
        $stmt->execute(array('id' => $id));

        return $stmt->fetchColumn();
    }


    public function addAnnouncement($param){
        try{
            $title = $param['title'];
            $content = $param['content'];
            $time = $param['time'];
            $timeline = $param['timeline'];
            $conn = connect();
            $stmt = $conn->prepare('INSERT INTO announcement (id, title, content, time) VALUES (NULL, :title, :content, :time)');
            $stmt->execute(array('title'=>$title,
                                 'content'=>$content,
                                 'time'=>$time));
            if($stmt->rowCount() != 1){
                die("User insertion error");
            }
                //stub: send email
            $conn = null;
            return true;
        } catch (PDOException $e){
            echo 'ERROR: '. $e->getMessage();
        }
        return 1;
    }

    public function getAllAnnouncements(){
        try{
            $conn = connect();
            $stmt = $conn->prepare("SELECT * FROM announcement");
            $stmt->execute();
            $all_announcements = $this->annlist->getQuery('SELECT id, title, content, time FROM announcement');
            
            return $all_announcements;

        } catch (PDOException $e){
            echo 'ERROR: '. $e->getMessage();
        }
    }

    public function getAnnouncement($id){
        try{
            $conn = connect();
            if(self::isAnnouncementExist($id, $conn)){
                $stmt = $conn->prepare("SELECT title, content FROM announcement WHERE id = :id");
                $stmt->execute(array("id" => $id));
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else return false;
        } catch(PDOException $e){
            echo 'ERROR: '. $e->getMessage();
        }
    }

    public function editAnnouncement($param){
        try{
            $conn = connect();
            return 1;
        } catch (PDOException $e){
            echo 'ERROR: '. $e->getMessage();
        }
        return 1;
    }

    public function deleteAnnouncement($id){
        try{
            $conn = connect();
            if(self::isAnnouncementExist($id, $conn)){
                $stmt = $conn->prepare("DELETE FROM announcement WHERE id = :id");
                $stmt->execute(array("id" => $id));
                if($stmt->rowCount() != 1){
                    die("Announcement deletion error");
                }
                return true;
            }
            return false;
        } catch (PDOException  $e){
            echo 'ERROR: '. $e->getMessage();
        }
    }

    public function updateAnnouncement($id, $param){
        try{
            $conn = connect();
            $stmt = $conn->prepare("UPDATE announcement SET title = :title, content = :content WHERE id = :id");
            $stmt->execute(array("id"=>$id, "title"=>$param['title'], "content"=>$param['content']));
            if($stmt->rowCount() != 1){
                    die("Update profile error");
            }
            return 1;
        }catch(PDOException $e){
            echo 'ERROR: '. $e->getMessage();
        }
    }


}

?>