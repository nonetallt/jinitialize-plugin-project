<?php

if(!function_exists('str_until')) {
    function str_until(string $subject, string $until)
    {
        $stopAt = strpos($subject, $until);
        $cut = null;

        if($stopAt === false) {
            $cut = substr($subject, 0);

        }
        else {
            $cut = substr($subject, 0, $stopAt);

        }
        return $cut;

    }
}

if(!function_exists('str_after')) {
    function str_after(string $subject, string $until, bool $includeStop = true)
    {
        $startAt = strpos($subject, $until);
        $cut = null;

        if($startAt === false) {
            $cut = substr($subject, 0);

        }
        else {
            if(! $includeStop) $startAt++;
            $cut = substr($subject, $startAt);

        }
        return $cut;

    }
}
