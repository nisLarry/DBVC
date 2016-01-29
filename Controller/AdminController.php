<?php

namespace Controller;


use Lib\VersionControl\VCManager;

class AdminController
{
    public function dbvc_list()
    {

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