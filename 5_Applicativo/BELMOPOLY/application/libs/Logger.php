<?php
namespace libs;
class Logger
{
    private static $logFile = './application/logs/log.txt';

    public static function log($message)
    {
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] - $message" . PHP_EOL;
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }
}
