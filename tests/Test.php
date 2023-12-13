<?php

namespace Test;

use App\Exception\LogParsingException;
use App\Exception\ValidationException;
use App\Input\Input;
use App\Input\InputOptionParser;
use App\Log\LogParser;
use App\Log\LogProcessor;
use App\Validator\InputOptionsValidator;
use PHPUnit\Framework\TestCase;

require '../vendor/autoload.php';

class Test extends TestCase
{
    public function testInputParsing()
    {
        $uptimePercent = 30.1;
        $responseTimeLimit = 99.9;

        $input = $this->prepareInput($uptimePercent, $responseTimeLimit);

        $this->assertEquals($uptimePercent, $input->getUptimePercent());
        $this->assertEquals($responseTimeLimit, $input->getResponseTimeLimit());
    }

    public function testInput()
    {
        $uptimePercent = 30.1;
        $responseTimeLimit = -1;

        $input = $this->prepareInput($uptimePercent, $responseTimeLimit);

        $validator = new InputOptionsValidator();

        try {
            $validator->validate($input);
        } catch (ValidationException $e) {
            $this->assertSame(
                "Response time Limit should be numeric value and greater than 0",
                $e->getMessage());
        }

        $uptimePercent = 101;
        $responseTimeLimit = 20;

        $input = $this->prepareInput($uptimePercent, $responseTimeLimit);

        try {
            $validator->validate($input);
        } catch (ValidationException $e) {
            $this->assertSame(
                "Uptime percent should be numeric value and equal or greater than 0 and less or equal than 100",
                $e->getMessage());
        }
    }

    public function testLogs()
    {
        $errorLogString = '192.168.32.181 - - [06/2017:16:47:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" test 2 20.510983 "-" "@list-item-updater" prio:0';
        $logParser = new LogParser();

        try {
            $logParser->parseLog($errorLogString);
        } catch (LogParsingException $e) {
            $this->assertEquals(
                "Error occurred while parsing log date. Log string: " . $logParser->prepareLogString($errorLogString),
                $e->getMessage()
            );
        }

        $errorLogString = '192.168.32.181 - - [14/06/2017:16:47:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" test 2 20.510983 "-" "@list-item-updater" prio:0';

        try {
            $logParser->parseLog($errorLogString);
        } catch (LogParsingException $e) {
            $this->assertEquals(
                "Error occurred while parsing log response code. Log string: " . $logParser->prepareLogString($errorLogString),
                $e->getMessage()
            );
        }

        $errorLogString = '192.168.32.181 - - [14/06/2017:16:47:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" 200 2 test "-" "@list-item-updater" prio:0';

        try {
            $logParser->parseLog($errorLogString);
        } catch (LogParsingException $e) {
            $this->assertEquals(
                "Error occurred while parsing log response time. Log string: " . $logParser->prepareLogString($errorLogString),
                $e->getMessage()
            );
        }

        $successLogString = '192.168.32.181 - - [14/06/2017:16:51:12 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=e356713 HTTP/1.1" 200 2 20.164372 "-" "@list-item-updater" prio:0';

        $log = $logParser->parseLog($successLogString);

        $this->assertEquals(20.164372, $log->getResponseTime());
        $this->assertEquals(200, $log->getResponseCode());
    }

    public function testIntervalHandling(): void
    {
        $uptimePercent = 90;
        $responseTimeLimit = 30;
        $input = $this->prepareInput($uptimePercent, $responseTimeLimit);
        $logs  = [
            '192.168.32.181 - - [14/06/2017:16:47:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" 200 2 500 "-" "@list-item-updater" prio:0',
            '192.168.32.181 - - [14/06/2017:16:48:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" 200 2 200 "-" "@list-item-updater" prio:0',
            '192.168.32.181 - - [14/06/2017:16:49:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" 200 2 200 "-" "@list-item-updater" prio:0',
            '192.168.32.181 - - [14/06/2017:16:50:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" 200 2 200 "-" "@list-item-updater" prio:0',
            '192.168.32.181 - - [14/06/2017:16:51:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" 200 2 200 "-" "@list-item-updater" prio:0',
            '192.168.32.181 - - [14/06/2017:16:52:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" 200 2 200 "-" "@list-item-updater" prio:0',
            '192.168.32.181 - - [14/06/2017:16:53:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" 200 2 200 "-" "@list-item-updater" prio:0',
            '192.168.32.181 - - [14/06/2017:16:54:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" 200 2 200 "-" "@list-item-updater" prio:0',
            '192.168.32.181 - - [14/06/2017:16:55:02 +1000] "PUT /rest/v1.4/documents?zone=default&_rid=6076537c HTTP/1.1" 200 2 200 "-" "@list-item-updater" prio:0',
        ];

        $logParser = new LogParser();
        $logProcessor = new LogProcessor($input);

        foreach ($logs as $log) {
            $log = $logParser->parseLog($log);
            $logProcessor->processLog($log);
        }

        $this->assertCount(1, $logProcessor->getIntervalContainer());
    }

    private function prepareInput(float $uptimePercent, float $responseTimeLimit): Input
    {
        $parser = new InputOptionParser();
        $testOptions = [
            InputOptionParser::UPTIME_PERCENT_OPTION => $uptimePercent,
            InputOptionParser::RESPONSE_TIME_LIMIT_OPTION => $responseTimeLimit,
        ];

        return $parser->parseInputOptions($testOptions);
    }
}