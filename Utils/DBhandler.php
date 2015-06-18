<?php
require_once(dirname(__FILE__).'/Constants.php');

Class DBHandler
{
    private $con;

    public function DBHandler()
    {   
        $this->con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
    }

    public function doQuery($query)
    {
        global $db_handler_success;
        mysqli_query($this->con,$query);
        return $db_handler_success;
    }

    public function getQuery($query)
    {
        $result = mysqli_query($this->con,$query);
        $data = array();
        $i = 0;
        while ($data[$i++] = mysqli_fetch_array($result));
        unset($data[count($data)-1]);

        return $data;
    }
};
?>