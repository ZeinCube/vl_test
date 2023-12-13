<?php

use App\Exception\ValidationException;
use App\Input\InputOptionParser;
use App\Interval\IntervalContainer;
use App\Log\LogParser;
use App\Log\LogProcessor;
use App\Util\Output;
use App\Validator\InputOptionsValidator;

require dirname(__DIR__) . '/vendor/autoload.php';

execute();

function execute(): void
{
    $inputParser = new InputOptionParser();
    $input = $inputParser->getAndParseOptions();
    $validator = new InputOptionsValidator();

    try {
        $validator->validate($input);
    } catch (ValidationException $e) {
        Output::printException("Exception occurred while validating input. Message: ", $e);

        return;
    }

    $logParser    = new LogParser();
    $logProcessor = new LogProcessor($input);

    while ($line = fgets(STDIN)) {
        $log = $logParser->parseLog($line);

        $logProcessor->processLog($log);
    }

    printIntervals($logProcessor->getIntervalContainer());
}

function printIntervals(IntervalContainer $container): void
{
    $container->sortIntervals();

    foreach ($container as $interval) {
        Output::printInterval($interval);
    }
}
