<?php

namespace marvin255\fias\tests\utils\mysql;

use marvin255\fias\utils\mysql\Loader;

class LoaderTest extends \marvin255\fias\tests\DbTestCase
{
    public function testConstructorEmptyTableException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException');
        $inserter = new Loader($pdo, '', 'primary', ['primary', 'row']);
    }

    public function testConstructorEmptyPrimaryException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException');
        $inserter = new Loader($pdo, 'test', '', ['primary', 'row']);
    }

    public function testConstructorEmptyPrimaryArrayException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException', 1);
        $inserter = new Loader($pdo, 'test', ['primary', ''], ['primary', 'row']);
    }

    public function testConstructorEmptyRowsException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException', 1);
        $inserter = new Loader($pdo, 'test', 'primary', []);
    }

    public function testConstructorEmptyRowsArrayException()
    {
        $pdo = $this->getPdo();
        $this->setExpectedException('\InvalidArgumentException', 1);
        $inserter = new Loader($pdo, 'test', 'primary', ['primary', false]);
    }

    public function testProcess()
    {
        for ($queryNumber = 4; $queryNumber <= 6; ++$queryNumber) {
            $data[] = [
                'id' => $queryNumber,
                'row1' => "row_1_{$queryNumber}",
                'row2' => "row_2_{$queryNumber}",
            ];
        }
        $data[] = [
            'id' => '2',
            'row1' => 'new_1_2',
            'row2' => 'new_2_2',
        ];

        $pdo = $this->getPdo();

        $inserter = new Loader(
            $pdo,
            'loader',
            'id',
            ['id', 'row1', 'row2'],
            2
        );

        $inserter->open();
        foreach ($data as $item) {
            $inserter->process($item);
        }
        $inserter->close();

        $queryTable = $this->getConnection()->createQueryTable(
            'loader',
            'SELECT * FROM loader'
        );
        $expectedTable = $this->createFlatXmlDataSet(__DIR__ . '/_db/loader_expected.xml')
            ->getTable('loader');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testProcessPrimaryArray()
    {
        for ($queryNumber = 4; $queryNumber <= 6; ++$queryNumber) {
            $data[] = [
                'id' => $queryNumber,
                'row1' => "row_1_{$queryNumber}",
                'row2' => "row_2_{$queryNumber}",
            ];
        }
        $data[] = [
            'id' => '2',
            'row1' => 'row_1_2',
            'row2' => 'new_2_2',
        ];

        $pdo = $this->getPdo();

        $inserter = new Loader(
            $pdo,
            'loader',
            ['id', 'row1'],
            ['id', 'row1', 'row2'],
            2
        );

        $inserter->open();
        foreach ($data as $item) {
            $inserter->process($item);
        }
        $inserter->close();

        $queryTable = $this->getConnection()->createQueryTable(
            'loader',
            'SELECT * FROM loader'
        );
        $expectedTable = $this->createFlatXmlDataSet(__DIR__ . '/_db/loader_expected_primary_array.xml')
            ->getTable('loader');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testProcessEmptyRowException()
    {
        $pdo = $this->getPdo();
        $inserter = new Loader(
            $pdo,
            'loader',
            'id',
            ['id', 'row1', 'row2']
        );

        $this->setExpectedException('\marvin255\fias\processor\Exception', 'row2');
        $inserter->open();
        $inserter->process([
            'id' => 'data_id_' . mt_rand(),
            'row1' => 'data_row_1_' . mt_rand(),
        ]);
        $inserter->close();
    }

    public function testProcessEmptyPrimaryException()
    {
        $pdo = $this->getPdo();
        $inserter = new Loader(
            $pdo,
            'loader',
            ['id', 'row2'],
            ['id', 'row1']
        );

        $this->setExpectedException('\marvin255\fias\processor\Exception', 'row2');
        $inserter->open();
        $inserter->process([
            'id' => 'data_id_' . mt_rand(),
            'row1' => 'data_row_1_' . mt_rand(),
        ]);
        $inserter->close();
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        return $this->createFlatXmlDataSet(__DIR__ . '/_db/loader.xml');
    }

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->getPdo()->exec('CREATE TABLE loader (
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
        $this->getPdo()->exec('DROP TABLE IF EXISTS loader');

        return parent::tearDown();
    }
}
