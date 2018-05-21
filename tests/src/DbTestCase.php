<?php

namespace marvin255\fias\tests;

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\TestCaseTrait;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PDO;

abstract class DbTestCase extends \PHPUnit\Framework\TestCase
{
    use MockeryPHPUnitIntegration;
    use TestCaseTrait;

    /**
     * @var \PDO
     */
    private static $pdo = null;
    /**
     * @var \PHPUnit\DbUnit\Database\Connection
     */
    private $conn = null;
    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var \PHPUnit\DbUnit\Database\Connection
     */
    final public function getConnection(): Connection
    {
        if ($this->conn === null) {
            $this->conn = $this->createDefaultDBConnection($this->getPdo(), ':memory:');
        }

        return $this->conn;
    }

    /**
     * @return \PDO
     */
    protected function getPdo(): PDO
    {
        if (self::$pdo == null) {
            self::$pdo = new PDO('sqlite::memory:');
        }

        return self::$pdo;
    }

    /**
     * @return \Faker\Generator
     */
    public function faker(): \Faker\Generator
    {
        if ($this->faker === null) {
            $this->faker = \Faker\Factory::create();
        }

        return $this->faker;
    }
}
