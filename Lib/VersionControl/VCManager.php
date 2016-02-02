<?php

namespace Lib\VersionControl;


use Lib\Db\db_manager;

class VCManager
{
    private static $_db;
    private static $_now_version;
    private static $_latest_version;
    private static $_instance;

    private function __construct()
    {
        $this->setDb();
        $this->_setNowVersion();
        $this->_setLateVersion();
    }

    /**
     * 设置资料库
     */
    public function setDb()
    {
        self::$_db = db_manager::getInstance();
    }

    /**
     * 取得档案列表
     * @return array|bool\
     */
    private static function _getVersionFiles()
    {
        $file_path = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."VCFiles";
        $file_array = self::_scan_dir($file_path);
        return $file_array;
    }

    /**
     * 设置本地资料库目前版本
     */
    private static function _setNowVersion()
    {
        if(!(isset(self::$_db)))
        {
            exit("you don't set db;");
        }

        $result = self::$_db->query("SELECT vc_file FROM db_vc ORDER BY id DESC LIMIT 1;");

        if (!$result) {
            die('Invalid query: ' .self::$_db->error());
        }

        $row = mysqli_fetch_assoc($result);

        self::$_now_version = empty($row['vc_file'])?0:(int)$row['vc_file'];
    }

    /**
     * 取得目录下的档案资料，并排序。如果无资料从返回false
     * @param $dir
     * @return array|bool
     */
    private static function _scan_dir($dir) {
        $ignored = array('.', '..');
        $files = array();
        foreach (scandir($dir) as $file) {
            if (in_array($file, $ignored)) continue;
            preg_match('/VC_(\d{10}).php/i',$file,$match);
            $files[$file] = $match[1];
        }
        sort($files);
        return ($files) ? $files : false;
    }

    /**
     * 设置目前开发最新版本
     */
    private static function _setLateVersion()
    {
        $file_array = self::_getVersionFiles();
        self::$_latest_version = $file_array?(int)$file_array[count($file_array)-1]:0;
    }

    /**
     * 取得VCManager单例实体
     * @return VCManager
     */
    public static function get_instance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 检查版本是否存在於档案列表
     * @param $v_no
     * @return bool
     */
    private static function _checkVersionIsExist($v_no)
    {
        if($v_no == 1)return true;
        $file_arr = self::_getVersionFiles();
        if(in_array($v_no,$file_arr)) return true;
        return false;
    }


    /**
     * 取得错误讯息
     * @param $error_no
     */
    private static function _getError($error_no)
    {
        switch($error_no)
        {
            case 0:exit("This version number is not number.\n"); break;
            case 1:exit("This version number is not exist.\n"); break;
            case 2:exit("Update fail!.\n"); break;
            case 3:exit("Save dbvc_log fail!.".mysqli_error()."\n"); break;
            default:exit("I don't know this error.\n");break;
        }
    }


    /**
     * 升级到指定的资料库结构版本，如果没有输入，预设上升一个版本
     * @param int $v_no
     */
    public static function up($v_no = 1)
    {
        if(!is_numeric($v_no))
        {
            self::_getError(0);
        }

        if(!self::_checkVersionIsExist($v_no))
        {
            self::_getError(1);
        }

        $file_arr = self::_getVersionFiles();

        $local_now_version = self::getNowVersion();

        //取得正确的目标版本号
        $local_now_version_key = array_search( $local_now_version,$file_arr);
        if($local_now_version == 0 && $v_no == 1)
        {
            $target_version =$file_arr[0];
        }
        elseif($local_now_version > 0 && $v_no == 1)
        {
            $target_version = isset($file_arr[$local_now_version_key+1])?$file_arr[$local_now_version_key+1]:$file_arr[$local_now_version_key];
        }
        else{
            $target_version = $v_no;
        }

        //过滤版本号必须大於本地资料库版本
        $file_arr = array_filter($file_arr,function($versionNo) use ($local_now_version){
            return $versionNo > $local_now_version;
        });

        //过滤版本号必须小於等於目标版本
        $file_arr = array_filter($file_arr,function($versionNo) use ($target_version){
            return $versionNo <= $target_version;
        });


        //检查当前的版本是否为开发最新版本
        if(empty($file_arr))
        {
            exit("Now is the latest version!");
        }


        //执行资料库结构更新程序
        foreach($file_arr as $key => $value)
        {
            $version_class_name = "VCFiles\\VC_{$value}";
            $version_file = new $version_class_name();
            $result = self::$_db->query($version_file->up());
            if($result)
            {
                $time = time();
                $sql = "INSERT INTO db_vc(vc_file,create_date,create_author,v_comment) VALUES('{$value}','{$time}','{$version_file->author()}','{$version_file->comment()}');";
                $dbvc_log = self::$_db->query($sql);
                if($dbvc_log)
                {
                    echo "Version:{$value} update success!\n";
                }
                else
                {
                    self::_getError(3);
                }
            }
            else
            {
                self::_getError(2);
            }

        }

    }

    /**
     * 将资料库版本下降到指定版本，当不输入时，预设降低一个版本
     * @param int $v_no
     */
    public static function down($v_no = 1)
    {
        if(!is_numeric($v_no))
        {
            self::_getError(0);
        }

        if(!self::_checkVersionIsExist($v_no))
        {
            self::_getError(1);
        }

        $file_arr = self::_getVersionFiles();

        $local_now_version = self::getNowVersion();

        //取得正确的目标版本号
        $local_now_version_key = array_search( $local_now_version,$file_arr);
        if($local_now_version == 0 && $v_no == 1)//表示没有上一个版本
        {
            exit("Now is the first version!");
        }
        elseif($local_now_version > 0 && $v_no == 1)
        {
            $target_version = isset($file_arr[$local_now_version_key-1])?$file_arr[$local_now_version_key-1]:$file_arr[$local_now_version_key];
        }
        else{
            $target_version = $v_no;
        }

        //过滤版本号必须小於等於本地资料库版本
        $file_arr = array_filter($file_arr,function($versionNo) use ($local_now_version){
            return $versionNo <= $local_now_version;
        });

        //正常情况过滤版本号必须大於目标版本。但当本地版本将目标版本一极时，则过滤版本号要等於目标版本
        $file_arr = array_filter($file_arr,function($versionNo) use ($target_version,$local_now_version,$v_no){
            if($v_no==0)return $versionNo >= $target_version;
            if($target_version==$local_now_version)return $versionNo == $target_version;
            return $versionNo > $target_version;
        });

        krsort($file_arr);

        //执行资料库结构更新程序
        foreach($file_arr as $key => $value)
        {
            $version_class_name = "VCFiles\\VC_{$value}";
            $version_file = new $version_class_name();
            $result = self::$_db->query($version_file->down());
            if($result)
            {
                $sql = "DELETE FROM db_vc WHERE vc_file = '{$value}';";
                $dbvc_log = self::$_db->query($sql);
                if($dbvc_log)
                {
                    echo "Version:{$value} down success!\n";
                }
                else
                {
                    self::_getError(3);
                }
            }
            else
            {
                self::_getError(2);
            }

        }

    }

    /**
     * 更新到开发最新版本
     */
    public static function auto_update()
    {
        $latest_version = self::getLatestVersion();
        self::up($latest_version);
    }

    /**
     * 将资料库结构的版本还原到引入版控前的状态
     */
    public static function init()
    {

        $file_arr = self::_getVersionFiles();

        $local_now_version = self::getNowVersion();

        //取得正确的目标版本号
        $target_version = $file_arr[0];


        //过滤版本号必须小於等於本地资料库版本
        $file_arr = array_filter($file_arr,function($versionNo) use ($local_now_version){
            return $versionNo <= $local_now_version;
        });

        //正常情况过滤版本号必须大於目标版本。但当本地版本将目标版本一极时，则过滤版本号要等於目标版本
        $file_arr = array_filter($file_arr,function($versionNo) use ($target_version){
            return $versionNo >= $target_version;
        });

        krsort($file_arr);

        //执行资料库结构更新程序
        foreach($file_arr as $key => $value)
        {
            $version_class_name = "VCFiles\\VC_{$value}";
            $version_file = new $version_class_name();
            $result = self::$_db->query($version_file->down());
            if($result)
            {
                $sql = "DELETE FROM db_vc WHERE vc_file = '{$value}';";
                $dbvc_log = self::$_db->query($sql);
                if($dbvc_log)
                {
                    echo "Version:{$value} down success!\n";
                }
                else
                {
                    self::_getError(3);
                }
            }
            else
            {
                self::_getError(2);
            }

        }
    }


    /**
     * 取得目前本地版本
     * @return mixed
     */
    public static function getNowVersion()
    {
        return self::$_now_version;
    }

    /**
     * 取得开发最新版本
     * @return mixed
     */
    public static function getLatestVersion()
    {
        return self::$_latest_version;
    }

    public static function getVersionList()
    {
        $list = self::_getVersionFiles();
        return $list;
    }
}