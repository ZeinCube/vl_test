<?php

namespace App\Log;

use DateTime;

class LogParser
{
    private const DATE_KEY = 3;
    private const RESPONSE_CODE_KEY = 8;
    private const RESPONSE_TIME_KEY = 10;

    /**
     * @param string $log
     *
     * @return Log
     */
    public function parseLog(string $log): Log
    {
        $trans = [' ' => ' ', '[' => '', ']' => ''];
        $log = strtr($log, $trans);
        $fields = explode(' ', $log);

        $logTime = DateTime::createFromFormat('d/m/Y:H:i:s', $fields[self::DATE_KEY]);

        $responseCode = (float)$fields[self::RESPONSE_CODE_KEY];
        $responseTime = (float)$fields[self::RESPONSE_TIME_KEY];

        return new Log($responseCode, $responseTime, $logTime);
    }
}