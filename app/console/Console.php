<?php


namespace app\console;


/**
 * Class Console
 * @package app\console
 */
class Console
{
    /**
     * @param string $value
     * @return false|int
     */
    public static function stdout(string $value): bool|int
    {
        return fwrite(\STDOUT, $value);
    }
}