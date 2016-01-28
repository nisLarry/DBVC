<?php
/**
 * Created by PhpStorm.
 * User: larry
 * Date: 2016/1/28
 * Time: 14:36
 */

namespace Lib\CLI;


use Lib\VersionControl\VCManager;

class CLI
{
    public function create()
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

    public function up($v_no)
    {
        VCManager::up($v_no);
    }

    public function down($v_no)
    {
        VCManager::down($v_no);
    }

}