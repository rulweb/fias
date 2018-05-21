<?php

namespace marvin255\fias\tests;

use marvin255\fias\TaskFactory;
use marvin255\fias\task\InsertData;
use marvin255\fias\task\UpdateData;
use InvalidArgumentException;

class TaskFactoryTest extends BaseTestCase
{
    public function testInserter()
    {
        $factory = new TaskFactory;

        $inserter = $factory->inserter('StructureStatus', 'structure_status');

        $this->assertInstanceOf(InsertData::class, $inserter);
    }

    public function testUpdater()
    {
        $factory = new TaskFactory;

        $updater = $factory->updater('StructureStatus', 'structure_status');

        $this->assertInstanceOf(UpdateData::class, $updater);
    }

    public function testInvalidEntityException()
    {
        $factory = new TaskFactory;

        $this->expectException(InvalidArgumentException::class);
        $inserter = $factory->inserter('StructureStatus_unexisted', 'structure_status');
    }
}
