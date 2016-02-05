<?php
/**
 * Created by PhpStorm.
 * User: larry
 * Date: 2016/1/28
 * Time: 16:48
 */

namespace VCFiles;


use Lib\VersionControl\BaseFunction;

class VC_1454567215 extends BaseFunction
{
    public function up()
    {
        return "ALTER TABLE `user` ADD COLUMN `name`  varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'name' AFTER `id`;";
    }

    public function down()
    {
        return "ALTER TABLE `user` DROP COLUMN `name`;";
    }

    public function comment()
    {
        return "user table add column `name`";
    }

    public function author()
    {
        return "Larry.Li";
    }
}