<?php
namespace Lib\Db;
/**
 * DB實體.
 */
class db_manager
{
    private static $instance;
    private static $db_name;

    private function __construct()
    {
    }

    /**
     * 取得DB實體.
     *
     * @param String $db_config_file db_config位置
     *
     * @return db_manager 實例
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            $db_config = json_decode(file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'db_config.json'));
            self::$db_name = $db_config->dbName;
            self::$instance = mysqli_connect($db_config->host, $db_config->user, $db_config->pwd, $db_config->dbName) or die('連接資料庫錯誤，請檢查使用者帳戶及密碼是否有權限.');
            mysqli_query(self::$instance, "SET NAMES 'utf8'");
        }

        return self::$instance;
    }

    /**
     * 取得目前的DB名稱.
     *
     * @return [type] [description]
     */
    public static function getDbName()
    {
        return self::$db_name;
    }
    
}
