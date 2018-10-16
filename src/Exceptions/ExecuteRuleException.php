<?php

namespace RuLong\UserAccount\Exceptions;

use RuntimeException;

class ExecuteRuleException extends RuntimeException
{

    public function __construct($message)
    {
        parent::__construct($message);
    }
}
