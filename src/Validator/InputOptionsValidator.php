<?php

namespace App\Validator;

use App\Exception\ValidationException;
use App\Input\Input;

class InputOptionsValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(Input $input): void
    {
        if (!is_float($input->getUptimePercent()) || $input->getUptimePercent() <= 0 || $input->getUptimePercent() > 100) {
            throw new ValidationException('Uptime percent should be numeric value and equal or greater than 0 and less or equal than 100');
        }

        if (!is_float($input->getResponseTimeLimit()) || $input->getResponseTimeLimit() < 0) {
            throw new ValidationException('Response time Limit should be numeric value and greater than 0');
        }
    }
}