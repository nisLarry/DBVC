<?php

namespace Controller;


use Lib\Db\DBManager;
use Lib\VersionControl\VCManager;

class AdminController
{

    public function __construct()
    {
        VCManager::get_instance();
    }

    public function check_dbvc()
    {
        $result = DBManager::checkTable("db_vc");
        echo json_encode($result);
    }

    public function install_dbvc()
    {
        $result = DBManager::updateSql("CREATE TABLE `db_vc` ( `id`  bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '版本號' , `vc_file`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '版本檔名稱' , `create_date`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '建立日期' , `create_author`  varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '建立者' , `v_comment`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '版本註解' , PRIMARY KEY (`id`) ) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci COMMENT='資料庫版控表' AUTO_INCREMENT=4 CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0 ;");
        echo json_encode($result);
    }

    public function dbvc_list()
    {
        $result['vList'] = VCManager::getVersionFiles();
        $result['nowVersion']=VCManager::getNowVersion();
        echo json_encode($result);
    }

    public function auto_update()
    {
        $result = VCManager::auto_update();
        echo json_encode($result);
    }

    public function init()
    {
        $result = VCManager::init();
        echo json_encode($result);
    }

    public function up()
    {
        $result = VCManager::up();
        echo json_encode($result);
    }

    public function down()
    {
        $result = VCManager::down();
        echo json_encode($result);
    }





}