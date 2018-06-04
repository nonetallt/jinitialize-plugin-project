<?php

namespace Nonetallt\Jinitialize\Plugin\Project;

class Paths
{
    public static function validate($value, callable $cb)
    {
        $dir      = is_dir($value);
        $writable = is_writable($value);

        if($dir && $writable) return true;

        $message = '';

        if(!$dir) $message      .= "Path $value is not a directory\n";
        if(!$writable) $message .= "Path $value is not a writable\n";

        $cb($messsage);
    }
}
