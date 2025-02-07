<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\ErrorHandler\ErrorHandler;

ErrorHandler::register(null, false);
