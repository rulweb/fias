<?php

namespace marvin255\fias\tests\task;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\task\Cleanup;
use marvin255\fias\service\filesystem\DirectoryInterface;
use Mockery;

class CleanupTest extends BaseTestCase
{
    public function testRun()
    {
        $directory = Mockery::mock(DirectoryInterface::class);
        $directory->shouldReceive('deleteChildren')->once();

        $cleanup = new Cleanup;
        $cleanup->setWorkDirectory($directory);

        $this->assertSame(true, $cleanup->run());
    }

    public function testGetDescription()
    {
        $cleanup = new Cleanup;

        $this->assertNotEmpty($cleanup->getDescription());
    }
}
