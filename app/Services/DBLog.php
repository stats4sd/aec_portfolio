<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Support\Str;

class DBLog
{
    public static $logLevels = array('FATAL', 'ERROR', 'WARN', 'INFO', 'DEBUG');

    public static function log(string $level, string $program, string $createdBy, string $message)
    {
        $systemLogLevel = Str::upper(env('DB_LOG_LEVEL', 'ERROR'));
        $systemLogLevelNumber = array_search($systemLogLevel, DBLog::$logLevels);
        $logLevelNumber = array_search($level, DBLog::$logLevels);

        if ($logLevelNumber <= $systemLogLevelNumber) {
            SystemLog::create([
                'level' => $level,
                'program' => $program,
                'created_by' => $createdBy,
                'message' => $message,
            ]);
        }
    }

    public static function fatal(string $program, string $createdBy, string $message)
    {
        DBLog::log('FATAL', $program, $createdBy, $message);
    }

    public static function error(string $program, string $createdBy, string $message)
    {
        DBLog::log('ERROR', $program, $createdBy, $message);
    }

    public static function warn(string $program, string $createdBy, string $message)
    {
        DBLog::log('WARN', $program, $createdBy, $message);
    }

    public static function info(string $program, string $createdBy, string $message)
    {
        DBLog::log('INFO', $program, $createdBy, $message);
    }

    public static function debug(string $program, string $createdBy, string $message)
    {
        DBLog::log('DEBUG', $program, $createdBy, $message);
    }
}
