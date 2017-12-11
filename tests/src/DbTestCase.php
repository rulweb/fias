<?php

namespace marvin255\fias\tests;

abstract class DbTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    /**
     * @var \PDO
     */
    private static $pdo = null;
    /**
     * @var \PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    private $conn = null;

    /**
     * @var \PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    final public function getConnection()
    {
        if ($this->conn === null) {
            $this->conn = $this->createDefaultDBConnection($this->getPdo(), ':memory:');
        }

        return $this->conn;
    }

    /**
     * @return \PDO
     */
    protected function getPdo()
    {
        if (self::$pdo == null) {
            self::$pdo = new \PDO('sqlite::memory:');
        }

        return self::$pdo;
    }
}
