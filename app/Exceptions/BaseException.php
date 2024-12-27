<?php

namespace App\Exceptions;

use Exception;

abstract class BaseException extends Exception
{
    protected $log_channel = "app";
    public abstract function render();
    public function report(){}
}
