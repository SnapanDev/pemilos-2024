<?php

namespace App\Http\Controllers;

use Psr\Log\LoggerInterface;

abstract class Controller
{
    protected LoggerInterface $logger;
}
