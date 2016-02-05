<?php
namespace Lib\Db;
/**
 * DB管理器.
 */
class DBManager
{
    private static $_instance;
    private static $_db_name;
    private static $_db;

    private function __construct()
    {
        $db_config = json_decode(file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'db_config.json'));
        self::$_db_name = $db_config->dbName;
        self::$_db = mysqli_connect($db_config->host, $db_config->user, $db_config->pwd, $db_config->dbName) or die('連接資料庫錯誤，請檢查使用者帳戶及密碼是否有權限.');
        mysqli_query(self::$_db, "SET NAMES 'utf8'");
    }

    /**
     * 取得DB實體.
     *
     * @return DBManager 實例
     */
    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 取得目前的DB名稱.
     *
     * @return [type] [description]
     */
    public static function getDbName()
    {
        self::getInstance();
        return self::$_db_name;
    }

    /**
     * 執行查詢sql腳本
     * @param $sql
     */
    public static function selectSql($sql)
    {
        self::getInstance();
        $result = self::$_db->query(strip_tags($sql)) or die('執行SQL腳本發生錯誤'.self::$_db->error);
        $result_arr = array();

        while($row = mysqli_fetch_assoc($result))
        {
            $result_arr[] = $row;
        }
        return $result_arr;
    }

    /**
     * 執行更新sql腳本
     * @param $sql
     * @return bool
     */
    public static function updateSql($sql)
    {
        self::getInstance();
        $result = self::$_db->query(strip_tags($sql)) or die(self::$_db->error);
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }


    /**
     * 檢查表是否存在
     * @param $tableName
     * @return int
     */
    public static function checkTable($tableName)
    {
        self::getInstance();
        $result =  self::$_db->query("SHOW TABLES LIKE '{$tableName}'");
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            return 1;
        } else {
            return 0;
        }
    }

}
