<?php

require 'vendor/autoload.php';

use Lib\Builder\BaseFileBuilder;
use Lib\VersionControl\VCManager;

class CLI
{
    public function __construct()
    {
        VCManager::get_instance();
    }
    
    private function _check_dbvc()
    {
        $result = VCManager::check_dbvc();
        if(!$result) exit("Table `dbvc` is not installed. Please use install command;");
    }
    

    private function _showMessage($result)
    {
        foreach($result as $key => $value)
        {
            echo $value[1];
        }
    }
    
    public function install()
    {
        $result = VCManager::install_dbvc();
        if($result)
        {
            echo " DBVC install is success!";
        }
        else
        {
            echo " DBVC install is fail! ";
        }
    }
    

    public function create()
    {
        $this->_check_dbvc();
        $builder = new BaseFileBuilder('VCFiles');
        echo "\033[31m{$builder->builder()} \033[0m be created.";
    }

    public function auto_update()
    {
        $this->_check_dbvc();
        $result = VCManager::auto_update();
        $this->_showMessage($result);
    }

    public function init()
    {
        $this->_check_dbvc();
        $result = VCManager::init();
        $this->_showMessage($result);
    }

    public function up($v_no = 1)
    {
        $this->_check_dbvc();
        if ($v_no == null) {
            $v_no = 1;
        }
        $result = VCManager::up($v_no);
        $this->_showMessage($result);
    }

    public function down($v_no = 1)
    {
        $this->_check_dbvc();
        if ($v_no == null) {
            $v_no = 1;
        }
        $result = VCManager::down($v_no);
        $this->_showMessage($result);
    }

    public function help()
    {
        $help_arr = array(
            'help' => 'show all command.',
            'install' => 'install DBVC.',
            'getlist' => 'show all version files.',
            'create' => 'create a new version file.',
            'up [v_no]' => 'up database structure to assign version. you can enter a version number. default up one version.',
            'down [v_no]' => 'down database structure to assign version. you can enter a version number. default down one version.',
            'init' => 'database structure initialization. ',
            'auto_update' => 'up database structure to the latest version.'
        );

        foreach ($help_arr as $key => $value) {
            echo "\033[1;31m{$key} \033[0m : \033[1;32m{$value} \r\n\033[0m";
        }

    }

    public function getlist()
    {
        $this->_check_dbvc();
        $localVersion = VCManager::getNowVersion();
        $latestVersion = VCManager::getLatestVersion();
        $list = VCManager::getVersionList();

        echo "file version list:\n";
        foreach ($list as $key => $value) {
            if ($localVersion == $value) {
                echo "\033[1;31m{$value}  is now version. \r\n\033[0m";
            } elseif ($latestVersion == $value) {
                echo "\033[1;32m{$value}  is latest version. \r\n\033[0m";
            } else {
                echo $value . "\n";
            }

        }
    }
}

$cli = new CLI();
$command = $argv[1];
if (!method_exists($cli, $command)) {
    exit("I don't know '{$command}' command. Maybe you can use 'help' to get command list.");
}

$field = isset($argv[2]) ? $argv[2] : null;
call_user_func(array($cli, $command), $field);



