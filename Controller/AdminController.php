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
        $result = VCManager::check_dbvc();
        echo json_encode($result);
    }

    public function install_dbvc()
    {
        $result = VCManager::install_dbvc();
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