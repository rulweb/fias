<?php

namespace marvin255\fias\tests\service\bag;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\service\console\Logger;

class LoggerTest extends BaseTestCase
{
    public function testInfo()
    {
        $message = $this->faker()->unique()->word;

        $logger = new Logger;

        $this->expectOutputRegex('/' . preg_quote($message) . '/');
        $logger->info($message);
    }

    public function testError()
    {
        $message = $this->faker()->unique()->word;

        $logger = new Logger;

        $this->expectOutputRegex('/' . preg_quote($message) . '/');
        $logger->error($message);
    }
}
