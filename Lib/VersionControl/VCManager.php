<?php

namespace Lib\VersionControl;


use Lib\Db\DBManager;


/**
 * 錯誤訊息標示
 * Class VCErrorMsg
 * @package Lib\VersionControl
 */
abstract class VCErrorMsg
{
    const VERSION_NUMBER_IS_NOT_NUMBER = "VERSION_NUMBER_IS_NOT_NUMBER";
    const VERSION_NUMBER_IS_NOT_EXIST = "VERSION_NUMBER_IS_NOT_EXIST";
    const UPDATE_FAIL = "UPDATE_FAIL";
    const SAVE_DBVC_LOG_FAIL = "SAVE_DBVC_LOG_FAIL";
    const DOT_KNOW_ERROR = "DOT_KNOW_ERROR";
    const IS_LATEST_VERSION = "IS_LATEST_VERSION";
}

/**
 * 版本控制管理器
 * Class VCManager
 * @package Lib\VersionControl
 */
class VCManager
{
    private static $_now_version;
    private static $_latest_version;
    private static $_instance;
    private static $_message = array();

    private function __construct()
    {
        $this->_setNowVersion();
        $this->_setLateVersion();
        $this->_setMessage(VCErrorMsg::VERSION_NUMBER_IS_NOT_NUMBER, 0, "This version number is not number.\n")
            ->_setMessage(VCErrorMsg::VERSION_NUMBER_IS_NOT_EXIST, 0, "This version number is not exist.\n")
            ->_setMessage(VCErrorMsg::UPDATE_FAIL, 0, "Update fail!.\n")
            ->_setMessage(VCErrorMsg::SAVE_DBVC_LOG_FAIL, 0, "Save dbvc_log fail!.\n")
            ->_setMessage(VCErrorMsg::DOT_KNOW_ERROR, 0, "I don't know this error.\n")
            ->_setMessage(VCErrorMsg::IS_LATEST_VERSION, 0, "Now is the latest version!\n");
    }

    /**
     * 取得档案名稱列表
     * @return array|bool\
     */
    private static function _getVersionFileNames()
    {
        $file_path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "VCFiles";
        $file_array = self::_scan_dir($file_path);
        return $file_array;
    }

    /**
     * 取得版本詳細列表
     * @return array
     */
    public static function getVersionFiles()
    {
        $version_list = array();
        $database_list = array();
        $file_arr = self::_getVersionFileNames();
        foreach ($file_arr as $key => $value) {
            $version_class_name = "VCFiles\\VC_{$value}";
            $version_file = new $version_class_name();
            $version_list[$value] = array(
                "vc_file" => $value,
                "create_date" => "",
                "create_author" => $version_file->author(),
                "v_comment" => $version_file->comment()
            );
        }

        $database_arr = DBManager::selectSql("select * from db_vc;");
        foreach ($database_arr as $key => $value) {
            $database_list[$value['vc_file']] = array(
                "vc_file" => $value['vc_file'],
                "create_date" => $value['create_date'],
                "create_author" => $value['create_author'],
                "v_comment" => $value['v_comment']
            );
        }

        $database_key_arry = array_keys($database_list);

        $merge = array_map(function ($key) use ($database_key_arry, $version_list, $database_list) {

            if (in_array($key, $database_key_arry)) {
                return $database_list[$key];
            } else {
                return $version_list[$key];
            }

        }, array_keys($version_list));

        return $merge;

    }

    /**
     * 设置本地资料库目前版本
     */
    private static function _setNowVersion()
    {
        $result = DBManager::checkTable("db_vc");
        if($result)
        {
            $result = DBManager::selectSql("SELECT vc_file FROM db_vc ORDER BY id DESC LIMIT 1;");
            self::$_now_version = empty($result[0]['vc_file']) ? 0 : (int)$result[0]['vc_file'];
        }

    }

    /**
     * 取得目录下的档案资料，并排序。如果无资料从返回false
     * @param $dir
     * @return array|bool
     */
    private static function _scan_dir($dir)
    {
        $ignored = array('.', '..');
        $files = array();
        foreach (scandir($dir) as $file) {
            if (in_array($file, $ignored)) {
                continue;
            }
            preg_match('/VC_(\d{10}).php/i', $file, $match);
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
        $file_array = self::_getVersionFileNames();
        self::$_latest_version = $file_array ? (int)$file_array[count($file_array) - 1] : 0;
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
        if ($v_no == 1) {
            return true;
        }
        $file_arr = self::_getVersionFileNames();
        if (in_array($v_no, $file_arr)) {
            return true;
        }
        return false;
    }


    /**
     * 設置訊息
     * @param string $messageName 訊息標示
     * @param $status $訊息狀態
     * @param $message $訊息主體
     * @return $this
     */
    private function _setMessage($messageName, $status, $message)
    {
        self::$_message[$messageName] = array($status, $message);
        return $this;
    }


    /**
     * 獲取訊息
     * @param string $messageName 訊息標示
     * @return mixed
     */
    private static function _getMessage($messageName)
    {
        return self::$_message[$messageName];
    }


    /**
     * 升级到指定的资料库结构版本，如果没有输入，预设上升一个版本
     * @param int $v_no
     */
    public static function up($v_no = 1)
    {
        $return_message = array();

        if (!is_numeric($v_no)) {
            return array(self::_getMessage(VCErrorMsg::VERSION_NUMBER_IS_NOT_NUMBER));
        }

        if (!self::_checkVersionIsExist($v_no)) {
            return array(self::_getMessage(VCErrorMsg::VERSION_NUMBER_IS_NOT_EXIST));
        }

        $file_arr = self::_getVersionFileNames();

        $local_now_version = self::getNowVersion();

        //取得正确的目标版本号
        $local_now_version_key = array_search($local_now_version, $file_arr);
        if ($local_now_version == 0 && $v_no == 1) {
            $target_version = $file_arr[0];
        } elseif ($local_now_version > 0 && $v_no == 1) {
            $target_version = isset($file_arr[$local_now_version_key + 1]) ? $file_arr[$local_now_version_key + 1] : $file_arr[$local_now_version_key];
        } else {
            $target_version = $v_no;
        }

        //过滤版本号必须大於本地资料库版本
        $file_arr = array_filter($file_arr, function ($versionNo) use ($local_now_version) {
            return $versionNo > $local_now_version;
        });

        //过滤版本号必须小於等於目标版本
        $file_arr = array_filter($file_arr, function ($versionNo) use ($target_version) {
            return $versionNo <= $target_version;
        });


        //检查当前的版本是否为开发最新版本
        if (empty($file_arr)) {
            return array(self::_getMessage(VCErrorMsg::IS_LATEST_VERSION));
        }


        //执行资料库结构更新程序
        foreach ($file_arr as $key => $value) {
            $version_class_name = "VCFiles\\VC_{$value}";
            $version_file = new $version_class_name();
            $result = DBManager::updateSql($version_file->up());
            if ($result) {
                $time = time();
                $sql = "INSERT INTO db_vc(vc_file,create_date,create_author,v_comment) VALUES('{$value}','{$time}','{$version_file->author()}','{$version_file->comment()}');";
                $dbvc_log = DBManager::updateSql($sql);
                if ($dbvc_log) {
                    self::_setNowVersion();
                    $return_message[] = array(1, "Version:{$value} update success!\n");
                } else {
                    $return_message[] = self::_getMessage(VCErrorMsg::SAVE_DBVC_LOG_FAIL);
                }
            } else {
                $return_message[] = self::_getMessage(VCErrorMsg::UPDATE_FAIL);
            }

        }
        return $return_message;
    }

    /**
     * 将资料库版本下降到指定版本，当不输入时，预设降低一个版本
     * @param int $v_no
     */
    public static function down($v_no = 1)
    {
        $return_message = array();

        if (!is_numeric($v_no)) {
            return self::_getMessage(VCErrorMsg::VERSION_NUMBER_IS_NOT_NUMBER);
        }

        if (!self::_checkVersionIsExist($v_no)) {
            return self::_getMessage(VCErrorMsg::VERSION_NUMBER_IS_NOT_EXIST);
        }

        $file_arr = self::_getVersionFileNames();

        $local_now_version = self::getNowVersion();

        //取得正确的目标版本号
        $local_now_version_key = array_search($local_now_version, $file_arr);
        if ($local_now_version == 0 && $v_no == 1)//表示没有上一个版本
        {
            return self::_getMessage(VCErrorMsg::IS_LATEST_VERSION);
        } elseif ($local_now_version > 0 && $v_no == 1) {
            $target_version = isset($file_arr[$local_now_version_key - 1]) ? $file_arr[$local_now_version_key - 1] : $file_arr[$local_now_version_key];
        } else {
            $target_version = $v_no;
        }

        //过滤版本号必须小於等於本地资料库版本
        $file_arr = array_filter($file_arr, function ($versionNo) use ($local_now_version) {
            return $versionNo <= $local_now_version;
        });

        //正常情况过滤版本号必须大於目标版本。但当本地版本将目标版本一极时，则过滤版本号要等於目标版本
        $file_arr = array_filter($file_arr, function ($versionNo) use ($target_version, $local_now_version, $v_no) {
            if ($v_no == 0) {
                return $versionNo >= $target_version;
            }
            if ($target_version == $local_now_version) {
                return $versionNo == $target_version;
            }
            return $versionNo > $target_version;
        });

        krsort($file_arr);

        //执行资料库结构更新程序
        foreach ($file_arr as $key => $value) {
            $version_class_name = "VCFiles\\VC_{$value}";
            $version_file = new $version_class_name();
            $result = DBManager::updateSql($version_file->down());
            if ($result) {
                $sql = "DELETE FROM db_vc WHERE vc_file = '{$value}';";
                $dbvc_log = DBManager::updateSql($sql);
                if ($dbvc_log) {
                    self::_setNowVersion();
                    $return_message[] = array(1, "Version:{$value} down success!\n");
                } else {
                    $return_message[] = self::_getMessage(VCErrorMsg::SAVE_DBVC_LOG_FAIL);
                }
            } else {
                $return_message[] = self::_getMessage(VCErrorMsg::UPDATE_FAIL);
            }

        }
        return $return_message;
    }

    /**
     * 更新到开发最新版本
     */
    public static function auto_update()
    {
        $latest_version = self::getLatestVersion();
        $result = self::up($latest_version);
        return $result;

    }

    /**
     * 将资料库结构的版本还原到引入版控前的状态
     */
    public static function init()
    {

        $return_message = array();

        $file_arr = self::_getVersionFileNames();

        $local_now_version = self::getNowVersion();

        //取得正确的目标版本号
        $target_version = $file_arr[0];


        //过滤版本号必须小於等於本地资料库版本
        $file_arr = array_filter($file_arr, function ($versionNo) use ($local_now_version) {
            return $versionNo <= $local_now_version;
        });

        //正常情况过滤版本号必须大於目标版本。但当本地版本将目标版本一极时，则过滤版本号要等於目标版本
        $file_arr = array_filter($file_arr, function ($versionNo) use ($target_version) {
            return $versionNo >= $target_version;
        });

        krsort($file_arr);

        //执行资料库结构更新程序
        foreach ($file_arr as $key => $value) {
            $version_class_name = "VCFiles\\VC_{$value}";
            $version_file = new $version_class_name();
            $result = DBManager::updateSql($version_file->down());
            if ($result) {
                $sql = "DELETE FROM db_vc WHERE vc_file = '{$value}';";
                $dbvc_log = DBManager::updateSql($sql);
                if ($dbvc_log) {
                    self::_setNowVersion();
                    $return_message[] = array(1, "Version:{$value} down success!\n");
                } else {
                    $return_message[] = self::_getMessage(VCErrorMsg::SAVE_DBVC_LOG_FAIL);
                }
            } else {
                $return_message[] = self::_getMessage(VCErrorMsg::UPDATE_FAIL);
            }

        }
        return $return_message;
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

    /**
     * 取得版本列表
     * @return array|bool\
     */
    public static function getVersionList()
    {
        $list = self::_getVersionFileNames();
        return $list;
    }
}