<?php

namespace marvin255\fias\tests\service\bag;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\service\bag\Bag;

class BagTest extends BaseTestCase
{
    public function testSet()
    {
        $paramName = $this->faker()->unique()->word;
        $paramValue = $this->faker()->unique()->word;
        $paramName2 = $this->faker()->unique()->word;
        $paramValue2 = $this->faker()->unique()->word;

        $flow = new Bag;
        $flow->set($paramName, $paramValue);

        $this->assertSame($paramValue, $flow->get($paramName));
        $this->assertSame($paramValue2, $flow->get($paramName2, $paramValue2));
        $this->assertSame(null, $flow->get($paramName2));
    }
}
