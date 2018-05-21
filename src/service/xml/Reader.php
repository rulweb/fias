<?php

declare(strict_types=1);

namespace marvin255\fias\service\xml;

use XMLReader;
use DOMDocument;
use SimpleXMLElement;
use InvalidArgumentException;
use RuntimeException;

/**
 * Читает данные из файла в формате xml.
 *
 * Надстройка над XMLReader.
 */
class Reader implements ReaderInterface
{
    /**
     * Абсолютный путь до файла.
     *
     * @var string
     */
    protected $pathToFile = null;
    /**
     * Путь до узла, который нужно прочитать.
     *
     * @var string
     */
    protected $pathToNode = null;
    /**
     * Массив с путями до тех элементов, которые необходимо выбрать.
     *
     * @var array
     */
    protected $select = null;
    /**
     * Объект XMLReader для чтения документа.
     *
     * @var \XMLReader
     */
    protected $reader = null;
    /**
     * Текущее смещение внутри массива.
     *
     * @var int
     */
    protected $position = 0;
    /**
     * Массив с буффером, для isValid и current.
     *
     * @var array
     */
    protected $buffer = false;

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        if ($this->reader) {
            $this->reader->close();
        }
        $this->reader = null;
        $this->position = 0;
        $this->buffer = false;
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        if ($this->buffer === false) {
            $this->buffer = $this->getLine();
        }

        return $this->buffer;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        ++$this->position;
        $this->buffer = $this->getLine();
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        if ($this->buffer === false) {
            $this->buffer = $this->getLine();
        }

        return $this->buffer !== null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function open(string $source, string $pathToNode, array $select): ReaderInterface
    {
        $this->close();

        $realpath = realpath(trim($source));
        if (!$realpath || !file_exists($realpath) || !is_file($realpath) || !is_readable($realpath)) {
            throw new InvalidArgumentException(
                "Can't read xml file: {$source}"
            );
        }
        $this->pathToFile = $realpath;

        if (empty($pathToNode)) {
            throw new InvalidArgumentException('Empty path to node');
        }
        $this->pathToNode = $pathToNode;

        if (empty($select)) {
            throw new InvalidArgumentException('Nothing to select');
        }
        $this->select = $select;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function close(): ReaderInterface
    {
        if ($this->reader) {
            $this->reader->close();
        }

        $this->reader = null;
        $this->position = 0;
        $this->buffer = false;

        $this->pathToFile = null;
        $this->pathToNode = null;
        $this->select = [];

        return $this;
    }

    /**
     * Возвращает разобранную в массив строку из файла.
     *
     * @return array|null
     */
    protected function getLine()
    {
        $return = null;

        if ($reader = $this->getReader()) {
            $arPath = explode('/', $this->pathToNode);
            $nameFilter = array_pop($arPath);
            $currentDepth = $reader->depth;
            //пропускаем все элементы, у которых неподходящее имя
            while ($reader->depth === $currentDepth && $nameFilter !== $reader->name && $reader->next());
            //мы можем выйти из цикла, если найдем нужный элемент
            //или попадем на уровень выше - проверяем, что нашли нужный
            if ($nameFilter === $reader->name) {
                $doc = new DOMDocument;
                $node = simplexml_import_dom($doc->importNode($reader->expand(), true));
                $return = $this->parseElement($this->select, $node);
                //нужно передвинуть указатель, чтобы дважды не прочитать
                //один и тот же элемент
                $reader->next();
            }
        }

        return $return;
    }

    /**
     * Разбирает узел для того, чтобы вернуть из него данные.
     *
     * @param array             $select Массив параметров для выборки
     * @param \SimpleXMLElement $node   Представление узла в SimpleXMLElement
     *
     * @return array
     */
    protected function parseElement(array $select, SimpleXMLElement $node)
    {
        $return = [];
        foreach ($select as $key => $part) {
            $attributes = $node->attributes();
            if (preg_match('/^@(.+)/', $part, $matches)) {
                $return[$key] = (string) $attributes[$matches[1]];
            } else {
                $return[$key] = (string) $node->{$part};
            }
        }

        return $return;
    }

    /**
     * Возвращает объект XMLReader для чтения документа.
     *
     * @return \XMLReader
     *
     * @throws \marvin255\fias\reader\Exception
     */
    protected function getReader()
    {
        if ($this->reader === null) {
            if (empty($this->pathToFile)) {
                throw new RuntimeException('File is not open');
            }
            $reader = new XMLReader;
            $reader->open($this->pathToFile);
            $this->reader = $this->searchForPath($reader, $this->pathToNode);
        }

        return $this->reader;
    }

    /**
     * Ищет узел заданный в параметре, прежде, чем начать перебор
     * элементов.
     *
     * Если собранный путь лежит в начале строки, которую мы ищем,
     * то продолжаем поиск.
     * Если собранный путь совпадает с тем, что мы ищем,
     * то выходим из цикла.
     * Если путь не совпадает и не лежит в начале строки,
     * то пропускаем данный узел со всеми вложенными деревьями.
     *
     * @param \XMLReader $reader Объект, в котором ведем поиск
     * @param string     $path   Путь до узла, который нужно найти
     *
     * @return \XMLReader|null
     */
    protected function searchForPath(XMLReader $reader, $path)
    {
        $path = trim($path, '/');
        $arPath = explode('/', $path);
        array_pop($arPath);
        $path = implode('/', $arPath);

        $currentPath = [];
        $isCompleted = false;

        $readResult = $reader->read();
        while ($readResult) {
            if ($reader->nodeType !== XMLReader::ELEMENT) {
                $readResult = $reader->read();
                continue;
            }
            array_push($currentPath, $reader->name);
            $currentPathStr = implode('/', $currentPath);
            if ($path === $currentPathStr) {
                $isCompleted = true;
                $readResult = false;
            } elseif (mb_strpos($path, $currentPathStr) !== 0) {
                array_pop($currentPath);
                $readResult = $reader->next();
            } else {
                $readResult = $reader->read();
            }
        }

        if ($isCompleted) {
            //читаем следующий элемент, если его глубина меньше или равна найденной,
            //значит искомый элемент - пустой и мы пропускаем чтение
            $currentDepth = $reader->depth;
            $reader->read();
            $isCompleted = $currentDepth < $reader->depth;
        }

        return $isCompleted ? $reader : false;
    }

    /**
     * Деструктор.
     *
     * Закрывает файл, если он все еще открыт.
     */
    public function __destruct()
    {
        $this->close();
    }
}
