<?php

namespace marvin255\fias\tests;

use marvin255\fias\ServiceLocator;
use Mockery;

class ServiceLocatorTest extends BaseTestCase
{
    public function testResolve()
    {
        $service = Mockery::mock(ServiceLocator::class);
        $serviceClass = get_class($service);

        $flow = new ServiceLocator;
        $flow->register($service);

        $this->assertSame($service, $flow->resolve($serviceClass));
    }
}
