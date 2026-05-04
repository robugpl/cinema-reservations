<?php

namespace App\Sales\Domain\Exception;

use DomainException;

class OrderCannotBeEmpty extends DomainException
{
    public function __construct()
    {
        parent::__construct('Order cannot be empty.');
    }
}
