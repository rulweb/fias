<?php

declare(strict_types=1);

namespace marvin255\fias\task;

use marvin255\fias\TaskInterface;
use marvin255\fias\service\xml\ReaderInterface;
use marvin255\fias\service\filesystem\DirectoryInterface;
use marvin255\fias\service\database\DatabaseInterface;
use RuntimeException;

/**
 * Задача, которая обновляет данные в таблице согласно файлу.
 */
class UpdateData implements TaskInterface
{
    /**
     * @var string
     */
    protected $tableName;
    /**
     * @var string
     */
    protected $primaryName;
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
     * @param string $tableName     Таблица, в которую будут загружены данные
     * @param string $primaryName   Название поля с первичным ключем, по которому будт произведен поиск
     * @param string $filePattern   Шаблон имени файла для поиска файла в папке
     * @param string $xmlPathToNode Xpath для xml файла, по которому будут лежать целевые данные
     * @param array  $xmlSelect     Массив вида "имя поля, которое вернет итератор => имя поля в xml файле" для выборкиданных из файла
     */
    public function __construct(string $tableName, string $primaryName, string $filePattern, string $xmlPathToNode, array $xmlSelect)
    {
        $this->tableName = $tableName;
        $this->primaryName = $primaryName;
        $this->filePattern = $filePattern;
        $this->xmlPathToNode = $xmlPathToNode;
        $this->xmlSelect = $xmlSelect;
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
            $this->proceedData();
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
    public function setWorkDirectory(DirectoryInterface $workDirectory): UpdateData
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
    public function setXmlReader(ReaderInterface $reader): UpdateData
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
    public function setDatabase(DatabaseInterface $database): UpdateData
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return "Update data from {$this->filePattern} file in {$this->tableName} table";
    }

    /**
     * Загружает данные в базу данных.
     *
     * @throws \RuntimeException
     */
    protected function proceedData()
    {
        foreach ($this->reader as $item) {
            if (!isset($item[$this->primaryName])) {
                throw new RuntimeException(
                    "Can't find primary key {$this->primaryName} in dataset: "
                    . json_encode($item, JSON_UNESCAPED_UNICODE)
                );
            }
            $searchResult = $this->database->fetchItemByFieldValue(
                $this->tableName,
                $this->primaryName,
                $item[$this->primaryName]
            );
            if (empty($searchResult)) {
                $this->database->insertItem($this->tableName, $item);
            } else {
                $this->database->updateItemByFieldValue(
                    $this->tableName,
                    $this->primaryName,
                    $item[$this->primaryName],
                    $item
                );
            }
        }
    }
}
