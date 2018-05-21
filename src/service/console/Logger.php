<?php

declare(strict_types=1);

namespace marvin255\fias\service\console;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Объект, который выводит отладочную информацию в консоль.
 */
class Logger extends AbstractLogger
{
    /**
     * @inheritdoc
     */
    public function log($level, $message, array $context = [])
    {
        if ($level === LogLevel::ERROR) {
            $message = "\033[31m{$message}\033[0m";
        } else {
            $message = "\033[32m{$message}\033[0m";
        }

        echo $message . "\r\n";
    }
}
