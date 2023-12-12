<?php

namespace App\Input;

class InputOptionParser
{
    private const UPTIME_PERCENT_OPTION = 'u';
    private const RESPONSE_TIME_LIMIT_OPTION = 't';

    public function parseInputOptions(): Input
    {
        $optionsString = sprintf('%s:%s:', self::UPTIME_PERCENT_OPTION, self::RESPONSE_TIME_LIMIT_OPTION);
        $options = getopt($optionsString);

        $uptimePercent = $options[self::UPTIME_PERCENT_OPTION] ?? null;
        $responseTimeLimit = $options[self::RESPONSE_TIME_LIMIT_OPTION] ?? null;

        return new Input($uptimePercent, $responseTimeLimit);
    }
}