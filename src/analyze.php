<?php

use App\Exception\ValidationException;
use App\Input\InputOptionParser;
use App\Interval\IntervalContainer;
use App\Log\LogParser;
use App\Util\IntervalHandler;
use App\Util\Output;
use App\Validator\InputOptionsValidator;

require dirname(__DIR__) . '/vendor/autoload.php';

execute();

function execute(): void
{
    $inputParser = new InputOptionParser();
    $input = $inputParser->parseInputOptions();
    $validator = new InputOptionsValidator();

    try {
        $validator->validate($input);
    } catch (ValidationException $e) {
        Output::printException("Exception occurred while validating input. Message: ", $e);

        return;
    }

    $logParser = new LogParser();
    $limitChecker = new IntervalHandler($input->getUptimePercent(), $input->getResponseTimeLimit());
    $intervalContainer = new IntervalContainer();

    while ($line = fgets(STDIN)) {
        $log = $logParser->parseLog($line);

        if ($limitChecker->checkIsFailureLog($log)) {
            $log->setFailure(true);
        }

        $currentInterval = $intervalContainer->getCurrent();
        if ($currentInterval !== null && !$limitChecker->dryCheckIsIntervalFault($currentInterval, $log)) {
            $intervalContainer->closeCurrent();
        }

        $intervalContainer->addLogToInterval($log);
    }

    printIntervals($intervalContainer);
}

function printIntervals(IntervalContainer $container): void
{
    foreach ($container as $interval) {
        Output::printInterval($interval);
    }
}
