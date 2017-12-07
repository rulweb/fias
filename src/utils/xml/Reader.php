<?php

namespace marvin255\fias\utils\xml;

use marvin255\fias\reader\ReaderInterface;
use marvin255\fias\reader\Exception;
use XMLReader;
use DOMDocument;
use SimpleXMLElement;
use InvalidArgumentException;

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
     * Конструктор.
     *
     * @param string $pathToNode Путь до узла, который нужно прочитать
     * @param array  $select     Массив параметров, который нужно выбрать из узла
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($pathToNode, array $select)
    {
        if (empty($pathToNode)) {
            throw new InvalidArgumentException('Empty path to node');
        }
        $this->pathToNode = $pathToNode;

        if (empty($select)) {
            throw new InvalidArgumentException('Nothing to select');
        }
        $this->select = $select;
    }

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
    public function open($source)
    {
        $this->close();

        $realpath = realpath(trim($source));
        if (!$realpath || !file_exists($realpath) || !is_file($realpath) || !is_readable($realpath)) {
            throw new InvalidArgumentException("Can\'t read file: $source");
        }
        $this->pathToFile = $realpath;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        if ($this->reader) {
            $this->reader->close();
        }
        $this->reader = null;
        $this->position = 0;
        $this->buffer = false;

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
            while ($reader->depth === $currentDepth && $nameFilter !== $reader->name) {
                if (!$reader->next()) {
                    break;
                }
            }
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
                throw new Exception('File is not open');
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

        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT) {
                continue;
            }
            array_push($currentPath, $reader->name);
            $currentPathStr = implode('/', $currentPath);
            if ($path === $currentPathStr) {
                $isCompleted = true;
                break;
            } elseif (mb_strpos($path, $currentPathStr) !== 0) {
                array_pop($currentPath);
                $reader->next();
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
