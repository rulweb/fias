<?php

namespace marvin255\fias\tests\utils\mysql;

use marvin255\fias\utils\mysql\Deleter;

class DeleterTest extends \marvin255\fias\tests\DbTestCase
{
    public function testConstructorEmptyTableException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException');
        $deleter = new Deleter($pdo, '', 'primary', ['primary', 'row']);
    }

    public function testConstructorEmptyPrimaryException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException');
        $deleter = new Deleter($pdo, 'test', '', ['primary', 'row']);
    }

    public function testConstructorEmptyPrimaryArrayException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException', 1);
        $deleter = new Deleter($pdo, 'test', ['primary', ''], ['primary', 'row']);
    }

    public function testConstructorEmptyRowsException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException', 1);
        $deleter = new Deleter($pdo, 'test', 'primary', []);
    }

    public function testConstructorEmptyRowsArrayException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException', 1);
        $deleter = new Deleter($pdo, 'test', 'primary', ['primary', false]);
    }

    public function testProcess()
    {
        $data = [
            ['id' => 1],
            ['id' => 3],
            ['id' => 4],
        ];
        $pdo = $this->getPdo();

        $deleter = new Deleter(
            $pdo,
            'deleter',
            'id',
            ['id', 'row1', 'row2'],
            2
        );

        $this->assertSame(
            $deleter,
            $deleter->open()
        );
        foreach ($data as $item) {
            $deleter->process($item);
        }
        $this->assertSame(
            $deleter,
            $deleter->close()
        );

        $queryTable = $this->getConnection()->createQueryTable(
            'deleter',
            'SELECT * FROM deleter'
        );
        $expectedTable = $this->createFlatXmlDataSet(__DIR__ . '/_db/deleter_expected.xml')
            ->getTable('deleter');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createFlatXmlDataSet(__DIR__ . '/_db/deleter.xml');
    }

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->getPdo()->exec('CREATE TABLE deleter (
            id int(11) not null,
            row1 varchar(30),
            row2 varchar(30),
            PRIMARY KEY(id)
        )');

        return parent::setUp();
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        $this->getPdo()->exec('DROP TABLE IF EXISTS deleter');

        return parent::tearDown();
    }
}
