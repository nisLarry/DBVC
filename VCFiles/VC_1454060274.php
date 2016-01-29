<?php
/**
 * Created by PhpStorm.
 * User: larry
 * Date: 2016/1/28
 * Time: 16:48
 */

namespace VCFiles;


use Lib\VersionControl\BaseFunction;

class VC_1454060274 extends BaseFunction
{
    public function up()
    {
        return "CREATE TABLE `User` (`id`  int(20) UNSIGNED NOT NULL AUTO_INCREMENT ,PRIMARY KEY (`id`));";
    }

    public function down()
    {
        return "DROP TABLE `User`;";
    }

    public function comment()
    {
        return "版控测试1，新增使用者表单。";
    }

    public function author()
    {
        return "Larry.Li";
    }
}