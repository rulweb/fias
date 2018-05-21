<?php

declare(strict_types=1);

namespace marvin255\fias\task;

use marvin255\fias\TaskInterface;
use marvin255\fias\service\xml\ReaderInterface;
use marvin255\fias\service\filesystem\DirectoryInterface;
use marvin255\fias\service\database\DatabaseInterface;

/**
 * Задача, которая очищает указанную таблицу и вставляет в нее данные из xml.
 */
class InsertData implements TaskInterface
{
    /**
     * @var string
     */
    protected $tableName;
    /**
     * @var string
     */
    protected $filePattern;
    /**
     * @var string
     */
    protected $xmlPathToNode;
    /**
     * @var array
     */
    protected $xmlSelect;
    /**
     * @var \marvin255\fias\service\filesystem\DirectoryInterface
     */
    protected $workDirectory;
    /**
     * @var \marvin255\fias\service\xml\ReaderInterface
     */
    protected $reader;
    /**
     * @var \marvin255\fias\service\database\DatabaseInterface
     */
    protected $database;
    /**
     * @var int
     */
    protected $bulkSize;

    /**
     * @param string $tableName     Таблица, в которую будут загружены данные
     * @param string $filePattern   Шаблон имени файла для поиска файла в папке
     * @param string $xmlPathToNode Xpath для xml файла, по которому будут лежать целевые данные
     * @param array  $xmlSelect     Массив вида "имя поля, которое вернет итератор => имя поля в xml файле" для выборкиданных из файла
     * @param int    $bulkSize      Количество записей для одной итерации пакетной вставки
     */
    public function __construct(string $tableName, string $filePattern, string $xmlPathToNode, array $xmlSelect, int $bulkSize = 200)
    {
        $this->tableName = $tableName;
        $this->filePattern = $filePattern;
        $this->xmlPathToNode = $xmlPathToNode;
        $this->xmlSelect = $xmlSelect;
        $this->bulkSize = $bulkSize;
    }

    /**
     * @inheritdoc
     */
    public function run(): bool
    {
        $files = $this->workDirectory->findFilesByPattern($this->filePattern);

        if ($files) {
            $this->reader->open(
                reset($files)->getPathname(),
                $this->xmlPathToNode,
                $this->xmlSelect
            );
            $this->insertData();
            $this->reader->close();
        }

        return true;
    }

    /**
     * Сеттер для объекта с рабочей папкой, в которую нужно сохранить файл.
     *
     * @param \marvin255\fias\service\filesystem\DirectoryInterface $workDirectory
     *
     * @return self
     */
    public function setWorkDirectory(DirectoryInterface $workDirectory): InsertData
    {
        $this->workDirectory = $workDirectory;

        return $this;
    }

    /**
     * Сеттер для объекта, который читает данные из xml файла.
     *
     * @param \marvin255\fias\service\xml\ReaderInterface $reader
     *
     * @return self
     */
    public function setXmlReader(ReaderInterface $reader): InsertData
    {
        $this->reader = $reader;

        return $this;
    }

    /**
     * Сеттер для объекта базы данных.
     *
     * @param \marvin255\fias\service\database\DatabaseInterface $database
     *
     * @return self
     */
    public function setDatabase(DatabaseInterface $database): InsertData
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return "Insert data from {$this->filePattern} file to {$this->tableName} table";
    }

    /**
     * Загружает данные в базу данных.
     */
    protected function insertData()
    {
        $this->database->truncateTable($this->tableName);

        $bulk = [];
        foreach ($this->reader as $item) {
            $bulk[] = $item;
            if (count($bulk) === $this->bulkSize) {
                $this->database->bulkInsert($this->tableName, $bulk);
                $bulk = [];
            }
        }

        if ($bulk) {
            $this->database->bulkInsert($this->tableName, $bulk);
        }
    }
}
