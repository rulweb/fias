<?php

namespace marvin255\fias\tests\service\database;

use marvin255\fias\tests\DbTestCase;
use marvin255\fias\service\database\Mysql;
use marvin255\fias\service\database\Exception;
use PHPUnit\DbUnit\DataSet\CompositeDataSet;
use PDO;
use PDOStatement;
use Mockery;

class MysqlTest extends DbTestCase
{
    public function testTruncateTable()
    {
        $dbPdo = new Mysql($this->getPdo());

        $dbPdo->truncateTable('truncateTable');

        $queryTable = $this->getConnection()->createQueryTable(
            'truncateTable',
            'SELECT * FROM truncateTable'
        );
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_fixture/truncateTable_expected.xml')
            ->getTable('truncateTable');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testBulkInsert()
    {
        $dbPdo = new Mysql($this->getPdo());

        $dbPdo->bulkInsert('bulkInsertTable', [
            ['id' => 1, 'row1' => 'row 1 1', 'row2' => 'row 1 2'],
            ['id' => 2, 'row1' => 'row 2 1', 'row2' => 'row 2 2'],
        ]);
        $dbPdo->bulkInsert('bulkInsertTable', [
            ['id' => 3, 'row1' => 'row 3 1', 'row2' => 'row 3 2'],
            ['id' => 4, 'row1' => 'row 4 1', 'row2' => 'row 4 2'],
        ]);
        $dbPdo->bulkInsert('bulkInsertTable', [
            ['id' => 5, 'row1' => 'row 5 1', 'row2' => 'row 5 2'],
        ]);

        $queryTable = $this->getConnection()->createQueryTable(
            'bulkInsertTable',
            'SELECT * FROM bulkInsertTable'
        );
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_fixture/bulkInsertTable_expected.xml')
            ->getTable('bulkInsertTable');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testFetchItemByFieldValue()
    {
        $dbPdo = new Mysql($this->getPdo());

        $res1 = $dbPdo->fetchItemByFieldValue('fetchItemByFieldValueTable', 'row1', 'row 2 1');
        $res2 = $dbPdo->fetchItemByFieldValue('fetchItemByFieldValueTable', 'row1', 'row 3 3');

        $this->assertSame(['id' => '2', 'row1' => 'row 2 1'], $res1);
        $this->assertSame([], $res2);
    }

    public function testUpdateItemByFieldValue()
    {
        $dbPdo = new Mysql($this->getPdo());

        $dbPdo->updateItemByFieldValue('updateItemByFieldValueTable', 'row1', 'row 2 1', [
            'row1' => 'test',
        ]);

        $queryTable = $this->getConnection()->createQueryTable(
            'updateItemByFieldValueTable',
            'SELECT * FROM updateItemByFieldValueTable'
        );
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_fixture/updateItemByFieldValueTable_expected.xml')
            ->getTable('updateItemByFieldValueTable');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testInsertItem()
    {
        $dbPdo = new Mysql($this->getPdo());

        $dbPdo->insertItem('insertItemTable', ['id' => 10, 'row1' => 'test']);

        $queryTable = $this->getConnection()->createQueryTable(
            'insertItemTable',
            'SELECT * FROM insertItemTable'
        );
        $expectedTable = $this->createXmlDataSet(__DIR__ . '/_fixture/insertItemTable_expected.xml')
            ->getTable('insertItemTable');

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testFetchPdoException()
    {
        $dbPdo = new Mysql($this->getPdo());

        $this->expectException(Exception::class);
        $dbPdo->fetchItemByFieldValue('unexisted_table_' . mt_rand(), 'test', 1);
    }

    public function testFetchFalseException()
    {
        $pdoStatement = Mockery::mock(PDOStatement::class);
        $pdoStatement->shouldReceive('execute')->andReturn(false);
        $pdoStatement->shouldReceive('errorInfo')->andReturn([2 => 'error message']);

        $pdo = Mockery::mock(PDO::class);
        $pdo->shouldReceive('prepare')->andReturn($pdoStatement);

        $dbPdo = new Mysql($pdo);

        $this->expectException(Exception::class, 'error message');
        $dbPdo->fetchItemByFieldValue('unexisted_table_' . mt_rand(), 'test', 1);
    }

    public function testExecPdoException()
    {
        $dbPdo = new Mysql($this->getPdo());

        $this->expectException(Exception::class);
        $dbPdo->bulkInsert('unexisted_table_' . mt_rand(), [
            ['id' => 1, 'row1' => 'row 1 1'],
        ]);
    }

    public function testExecFalseException()
    {
        $pdoStatement = Mockery::mock(PDOStatement::class);
        $pdoStatement->shouldReceive('execute')->andReturn(false);
        $pdoStatement->shouldReceive('errorInfo')->andReturn([2 => 'error message']);

        $pdo = Mockery::mock(PDO::class);
        $pdo->shouldReceive('prepare')->andReturn($pdoStatement);

        $dbPdo = new Mysql($pdo);

        $this->expectException(Exception::class, 'error message');
        $dbPdo->truncateTable('truncateTable');
    }

    /**
     * @return \PHPUnit\DbUnit\DataSet\IDataSet
     */
    public function getDataSet()
    {
        $compositeDs = new CompositeDataSet;

        $compositeDs->addDataSet(
            $this->createXmlDataSet(__DIR__ . '/_fixture/truncateTable.xml')
        );
        $compositeDs->addDataSet(
            $this->createXmlDataSet(__DIR__ . '/_fixture/bulkInsertTable.xml')
        );
        $compositeDs->addDataSet(
            $this->createXmlDataSet(__DIR__ . '/_fixture/fetchItemByFieldValueTable.xml')
        );
        $compositeDs->addDataSet(
            $this->createXmlDataSet(__DIR__ . '/_fixture/updateItemByFieldValueTable.xml')
        );
        $compositeDs->addDataSet(
            $this->createXmlDataSet(__DIR__ . '/_fixture/insertItemTable.xml')
        );

        return $compositeDs;
    }

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $pdo = $this->getPdo();

        $pdo->exec('CREATE TABLE truncateTable (
            id int(11) not null,
            row1 varchar(30),
            row2 varchar(30),
            PRIMARY KEY(id)
        )');

        $pdo->exec('CREATE TABLE bulkInsertTable (
            id int(11) not null,
            row1 varchar(30),
            row2 varchar(30),
            PRIMARY KEY(id)
        )');

        $pdo->exec('CREATE TABLE fetchItemByFieldValueTable (
            id int(11) not null,
            row1 varchar(30),
            PRIMARY KEY(id)
        )');

        $pdo->exec('CREATE TABLE updateItemByFieldValueTable (
            id int(11) not null,
            row1 varchar(30),
            PRIMARY KEY(id)
        )');

        $pdo->exec('CREATE TABLE insertItemTable (
            id int(11) not null,
            row1 varchar(30),
            PRIMARY KEY(id)
        )');

        return parent::setUp();
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        $this->getPdo()->exec('DROP TABLE IF EXISTS truncateTable');
        $this->getPdo()->exec('DROP TABLE IF EXISTS bulkInsertTable');
        $this->getPdo()->exec('DROP TABLE IF EXISTS fetchItemByFieldValueTable');
        $this->getPdo()->exec('DROP TABLE IF EXISTS updateItemByFieldValueTable');
        $this->getPdo()->exec('DROP TABLE IF EXISTS insertItemTable');

        return parent::tearDown();
    }
}
