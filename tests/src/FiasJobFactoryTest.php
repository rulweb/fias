<?php

namespace marvin255\fias\tests;

use marvin255\fias\FiasJobFactory;

class FiasJobFactoryTest extends BaseTestCase
{
    public function testCreateInserter()
    {
        $pdo = new \PDO('sqlite::memory:');
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();

        $factory = new FiasJobFactory($pdo, $dir);

        $inserter = $factory->inserter('Object', 'objects', [
            'name' => 'OFFNAME',
            'id' => 'AOID',
        ]);

        $this->assertInstanceOf(
            '\\marvin255\\fias\\job\\ReadAndProcess',
            $inserter
        );
    }

    public function testCreateInserterWithoutFields()
    {
        $pdo = new \PDO('sqlite::memory:');
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();

        $factory = new FiasJobFactory($pdo, $dir);

        $inserter = $factory->inserter('Object', 'objects');

        $this->assertInstanceOf(
            '\\marvin255\\fias\\job\\ReadAndProcess',
            $inserter
        );
    }

    public function testCreateInserterWrongFieldNameException()
    {
        $field = 'non_existed_field_xml_' . mt_rand();
        $pdo = new \PDO('sqlite::memory:');
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();

        $factory = new FiasJobFactory($pdo, $dir);

        $this->setExpectedException('\InvalidArgumentException', $field);
        $inserter = $factory->inserter('Object', 'objects', [
            'name' => $field,
            'id' => 'AOID',
        ]);
    }

    public function testCreateInserterNoPrimaryFieldNameException()
    {
        $pdo = new \PDO('sqlite::memory:');
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();

        $factory = new FiasJobFactory($pdo, $dir);

        $this->setExpectedException('\InvalidArgumentException', 'AOID');
        $inserter = $factory->inserter('Object', 'objects', [
            'name' => 'OFFNAME',
        ]);
    }

    public function testCreateInserterWrongEntityException()
    {
        $entity = 'unexisted_entity_' . mt_rand();
        $pdo = new \PDO('sqlite::memory:');
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();

        $factory = new FiasJobFactory($pdo, $dir);

        $this->setExpectedException('\InvalidArgumentException', $entity);
        $inserter = $factory->inserter($entity, 'objects');
    }

    public function testCreateUpdater()
    {
        $pdo = new \PDO('sqlite::memory:');
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();

        $factory = new FiasJobFactory($pdo, $dir);

        $inserter = $factory->updater('Object', 'objects', [
            'name' => 'OFFNAME',
            'id' => 'AOID',
        ]);

        $this->assertInstanceOf(
            '\\marvin255\\fias\\job\\ReadAndProcess',
            $inserter
        );
    }

    public function testCreateUpdaterWithoutFields()
    {
        $pdo = new \PDO('sqlite::memory:');
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();

        $factory = new FiasJobFactory($pdo, $dir);

        $inserter = $factory->updater('Object', 'objects');

        $this->assertInstanceOf(
            '\\marvin255\\fias\\job\\ReadAndProcess',
            $inserter
        );
    }

    public function testCreateUpdaterWrongFieldNameException()
    {
        $field = 'non_existed_field_xml_' . mt_rand();
        $pdo = new \PDO('sqlite::memory:');
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();

        $factory = new FiasJobFactory($pdo, $dir);

        $this->setExpectedException('\InvalidArgumentException', $field);
        $inserter = $factory->updater('Object', 'objects', [
            'name' => $field,
            'id' => 'AOID',
        ]);
    }

    public function testCreateUpdaterNoPrimaryFieldNameException()
    {
        $pdo = new \PDO('sqlite::memory:');
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();

        $factory = new FiasJobFactory($pdo, $dir);

        $this->setExpectedException('\InvalidArgumentException', 'AOID');
        $inserter = $factory->updater('Object', 'objects', [
            'name' => 'OFFNAME',
        ]);
    }

    public function testCreateUpdaterWrongEntityException()
    {
        $entity = 'unexisted_entity_' . mt_rand();
        $pdo = new \PDO('sqlite::memory:');
        $dir = $this->getMockBuilder('\marvin255\fias\utils\filesystem\DirectoryInterface')
            ->getMock();

        $factory = new FiasJobFactory($pdo, $dir);

        $this->setExpectedException('\InvalidArgumentException', $entity);
        $inserter = $factory->updater($entity, 'objects');
    }
}
