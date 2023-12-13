<?php

namespace App\Log;

use App\Exception\LogParsingException;
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
     *
     * @throws LogParsingException
     */
    public function parseLog(string $log): Log
    {
        $log = $this->prepareLogString($log);
        $fields = explode(' ', $log);

        $logTime = DateTime::createFromFormat('d/m/Y:H:i:s', $fields[self::DATE_KEY]);

        if ($logTime === false) {
            throw new LogParsingException("Error occurred while parsing log date. Log string: " . $log);
        }

        $responseCodeValue = $fields[self::RESPONSE_CODE_KEY];

        if (!is_numeric($responseCodeValue)) {
            throw new LogParsingException("Error occurred while parsing log response code. Log string: " . $log);
        }

        $responseTimeValue = $fields[self::RESPONSE_TIME_KEY];

        if (!is_numeric($responseTimeValue)) {
            throw new LogParsingException("Error occurred while parsing log response time. Log string: " . $log);
        }

        $responseTime = (float)$responseTimeValue;
        $responseCode = (int)$responseCodeValue;

        return new Log($responseCode, $responseTime, $logTime);
    }

    public function prepareLogString(string $log): string
    {
        $trans = [' ' => ' ', '[' => '', ']' => ''];

        return strtr($log, $trans);
    }
}