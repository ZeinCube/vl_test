<?php

namespace App\Input;

use App\Exception\OptionParsingException;

class InputOptionParser
{
    public const UPTIME_PERCENT_OPTION = 'u';
    public const RESPONSE_TIME_LIMIT_OPTION = 't';

    public function getAndParseOptions(): Input
    {
        $options = $this->getOptions();

        return $this->parseInputOptions($options);
    }

    /**
     * @throws OptionParsingException
     */
    public function getOptions(): array
    {
        $optionsString = sprintf('%s:%s:', self::UPTIME_PERCENT_OPTION, self::RESPONSE_TIME_LIMIT_OPTION);
        $options = getopt($optionsString);

        if (!$options) {
            throw new OptionParsingException("Error occurred while parsing options");
        }

        return $options;
    }

    /**
     * @param array $options
     *
     * @return Input
     */
    public function parseInputOptions(array $options): Input
    {
        $uptimePercent = $options[self::UPTIME_PERCENT_OPTION] ?? null;
        $responseTimeLimit = $options[self::RESPONSE_TIME_LIMIT_OPTION] ?? null;

        return new Input($uptimePercent, $responseTimeLimit);
    }
}