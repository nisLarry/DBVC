<?php
/**
 * Created by PhpStorm.
 * User: larry
 * Date: 2016/1/28
 * Time: 16:48
 */

namespace VCFiles;


use Lib\VersionControl\BaseFunction;

class VC_1454146130 extends BaseFunction
{
    public function up()
    {
        return "CREATE TABLE `Xx` (`id`  int(20) UNSIGNED NOT NULL AUTO_INCREMENT ,PRIMARY KEY (`id`));";
    }

    public function down()
    {
        return "DROP TABLE `Xx`;";
    }

    public function comment()
    {
        return "版控测试2，新增xx表单。";
    }

    public function author()
    {
        return "Larry.Li";
    }
}