<?php

namespace Lib\VersionControl;


abstract class BaseFunction
{
    abstract public function up();
    abstract public function down();
    abstract public function comment();
    abstract public function author();

}