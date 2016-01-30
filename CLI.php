<?php

require 'vendor/autoload.php';

use Lib\Builder\BaseFileBuilder;
use Lib\VersionControl\VCManager;

class CLI
{

    public function create()
    {
        $builder = new BaseFileBuilder('VCFiles');
        echo "\033[31m{$builder->builder()} \033[0m be created.";
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

    public function help()
    {
        $help_arr = array(
            'help'=>'show all command.',
            'create'=>'create a new version file.',
            'up [v_no]'=>'up database structure to assign version. you can enter a version number. default up one version.',
            'down [v_no]'=>'down database structure to assign version. you can enter a version number. default down one version.',
            'init'=>'database structure initialization. ',
            'auto_update'=>'up database structure to the latest version.'
        );

        foreach($help_arr as $key => $value)
        {
            echo "\033[1;31m{$key} \033[0m : \033[1;34m{$value} \r\n\033[0m";
        }

    }

    public function test()
    {
        VCManager::get_instance();
        VCManager::up();
    }

}

$cli = new CLI();
$command = $argv[1];
$field = isset($argv[2])?$argv[2]:null;
call_user_func(array($cli,$command),$field);
