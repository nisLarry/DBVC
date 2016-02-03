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

    public function dbvc_list()
    {
        $result = VCManager::getVersionFiles();
        echo json_encode($result);
    }

    public function auto_update()
    {
        VCManager::auto_update();
    }

    public function init()
    {
        VCManager::init();
    }

    public function up($vo_no)
    {
        VCManager::up($vo_no);
    }

    public function down($vo_no)
    {
        VCManager::down($vo_no);
    }





}