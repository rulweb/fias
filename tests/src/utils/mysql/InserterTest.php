<?php

namespace marvin255\fias\tests\utils\mysql;

use marvin255\fias\utils\mysql\Inserter;

class InserterTest extends \marvin255\fias\tests\DbTestCase
{
    public function testConstructorEmptyTableException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException');
        $inserter = new Inserter($pdo, '', 'primary', ['primary', 'row']);
    }

    public function testConstructorEmptyPrimaryException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException');
        $inserter = new Inserter($pdo, 'test', '', ['primary', 'row']);
    }

    public function testConstructorEmptyPrimaryArrayException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException', 1);
        $inserter = new Inserter($pdo, 'test', ['primary', ''], ['primary', 'row']);
    }

    public function testConstructorEmptyRowsException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException', 1);
        $inserter = new Inserter($pdo, 'test', 'primary', []);
    }

    public function testConstructorEmptyRowsArrayException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException', 1);
        $inserter = new Inserter($pdo, 'test', 'primary', ['primary', false]);
    }

    public function testProcess()
    {
        $data = [];
        $bulkCount = 2;
        for ($queryNumber = 0; $queryNumber < ($bulkCount * 2 + 1); ++$queryNumber) {
            $data[] = [
                'id' => "data_{$queryNumber}_id_" . mt_rand(),
                'row_1' => "data_{$queryNumber}_row_1_" . mt_rand(),
                'row_2' => "data_{$queryNumber}_row_2_" . mt_rand(),
            ];
        }
        $pdo = $this->getPdo();

        $inserter = new Inserter(
            $pdo,
            'inserter',
            'id',
            ['id', 'row_1', 'row_2'],
            $bulkCount
        );

        $inserter->open();
        foreach ($data as $item) {
            $inserter->process($item);
        }
        $inserter->close();

        $queryTable = $this->getConnection()->createQueryTable(
            'inserter',
            'SELECT * FROM inserter'
        );
        $expectedTable = $this->createArrayDataSet(['inserter' => $data])
            ->getTable('inserter');
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testProcessMultiplePrimary()
    {
        $data = [];
        $bulkCount = 2;
        for ($queryNumber = 0; $queryNumber < ($bulkCount * 2 + 1); ++$queryNumber) {
            $data[] = [
                'id' => "data_{$queryNumber}_id_" . mt_rand(),
                'row_1' => "data_{$queryNumber}_row_1_" . mt_rand(),
                'row_2' => "data_{$queryNumber}_row_2_" . mt_rand(),
            ];
        }
        $pdo = $this->getPdo();

        $inserter = new Inserter(
            $pdo,
            'inserter',
            ['id', 'row_1'],
            ['id', 'row_1', 'row_2'],
            $bulkCount
        );

        $inserter->open();
        foreach ($data as $item) {
            $inserter->process($item);
        }
        $inserter->close();

        $queryTable = $this->getConnection()->createQueryTable(
            'inserter',
            'SELECT * FROM inserter'
        );
        $expectedTable = $this->createArrayDataSet(['inserter' => $data])
            ->getTable('inserter');
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testProcessEmptyRowException()
    {
        $pdo = $this->getPdo();
        $inserter = new Inserter(
            $pdo,
            'inserter',
            'id',
            ['id', 'row_1', 'row_2']
        );

        $this->setExpectedException('\marvin255\fias\processor\Exception', 'row_2');
        $inserter->open();
        $inserter->process([
            'id' => 'data_id_' . mt_rand(),
            'row_1' => 'data_row_1_' . mt_rand(),
        ]);
        $inserter->close();
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createXMLDataSet(__DIR__ . '/_db/inserter.xml');
    }

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->getPdo()->exec('CREATE TABLE inserter (
            id int(11) not null,
            row_1 varchar(30),
            row_2 varchar(30),
            PRIMARY KEY(id)
        )');

        return parent::setUp();
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        $this->getPdo()->exec('DROP TABLE IF EXISTS inserter');

        return parent::tearDown();
    }
}
