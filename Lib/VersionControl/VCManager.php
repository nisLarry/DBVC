<?php

namespace Lib\VersionControl;


use Lib\Db\db_manager;

class VCManager
{
    private $_db;
    private $_now_version;
    private $_late_version;
    private static $_instance;
    private $run_vc_array;

    private function __construct()
    {
        $this->setDb();
        $this->setNowVersion();
        $this->setLateVersion();
    }

    public function setDb()
    {
        $this->_db = db_manager::getInstance();
    }

    private function setNowVersion()
    {
        if(!(isset($this->_db)))
        {
            exit("you don't set db;");
        }

        $result = $this->_db->query("SELECT vc_file FROM db_vc ORDER BY id DESC LIMIT 1;");

        if (!$result) {
            die('Invalid query: ' . $this->_db->error());
        }

        $row = mysqli_fetch_assoc($result);

        $this->_now_version = empty($row['vc_file'])?0:$row['vc_file'];
        echo $this->_now_version;
    }

    private function setLateVersion()
    {
        //print_r( scandir( dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."VCFiles") );
        //TODO
        //$this->_late_version = $late_version;
    }

    public static function get_instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function auto_update()
    {

    }

    public static function init()
    {

    }

    public static function up($v_no = 1)
    {

    }

    public static function down($v_no = 1)
    {

    }

    private function _getLateVersion()
    {
        return $this->_late_version;
    }

    private function _getNowVersion()
    {
        return $this->_now_version;
    }

    public static function getNowVersion()
    {
        return self::_getLateVersion();
    }
    public static function get_latest_version()
    {
        return self::_getNowVersion();
    }
}