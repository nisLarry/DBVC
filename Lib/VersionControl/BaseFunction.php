<?php
/**
 * Created by PhpStorm.
 * User: larry
 * Date: 2016/1/28
 * Time: 14:27
 */

namespace Lib\VersionControl;


abstract class BaseFunction
{
    abstract public function up();
    abstract public function down();
    abstract public function comment();

}