<?php

namespace marvin255\fias;

use marvin255\fias\job\ReadAndProcess;
use marvin255\fias\utils\filesystem\DirectoryInterface;
use marvin255\fias\utils\filesystem\FilterRegexp;
use marvin255\fias\utils\mysql\Inserter;
use marvin255\fias\utils\mysql\Loader;
use marvin255\fias\utils\xml\Reader;
use PDO;
use InvalidArgumentException;

/**
 * Фабрика, которая собирает объекты для сущностей фиас.
 *
 * Все сущности описны в массиве сущностей, на основании массива и типа работы
 * будет собран объект ReadAndProcess с соответствующими вложенными объектами
 * и настройками. Для настройки достаточно указать название таблицы в бд и, если
 * требуется, соответствия между полями в файле и в таблице бд.
 */
class FiasJobFactory
{
    /**
     * Объект pdo для подключения к бд.
     *
     * @var \PDO
     */
    protected $dbh = null;
    /**
     * Рабочий каталог, в котором расположены файлы для обработки.
     *
     * @var \marvin255\fias\utils\filesystem\DirectoryInterface
     */
    protected $workDir = null;
    /**
     * Массив с описаниями сущностей фиас.
     *
     * @var array
     */
    protected $entitiesDescription = [
        'Stead' => [
            'xml_path' => '/Steads/Stead',
            'primary' => 'STEADGUID',
            'xml_fields' => [
                'STEADGUID' => '@STEADGUID',
                'NUMBER' => '@NUMBER',
                'REGIONCODE' => '@REGIONCODE',
                'POSTALCODE' => '@POSTALCODE',
                'IFNSFL' => '@IFNSFL',
                'IFNSUL' => '@IFNSUL',
                'OKATO' => '@OKATO',
                'UPDATEDATE' => '@UPDATEDATE',
                'PARENTGUID' => '@PARENTGUID',
                'STEADID' => '@STEADID',
                'OPERSTATUS' => '@OPERSTATUS',
                'STARTDATE' => '@STARTDATE',
                'ENDDATE' => '@ENDDATE',
                'OKTMO' => '@OKTMO',
                'LIVESTATUS' => '@LIVESTATUS',
                'DIVTYPE' => '@DIVTYPE',
                'NORMDOC' => '@NORMDOC',
            ],
            'file_regexp' => 'AS_STEAD_.*\.XML',
        ],
        'ActualStatus' => [
            'xml_path' => '/ActualStatuses/ActualStatus',
            'primary' => 'ACTSTATID',
            'xml_fields' => [
                'ACTSTATID' => '@ACTSTATID',
                'NAME' => '@NAME',
            ],
            'file_regexp' => 'AS_ACTSTAT_.*\.XML',
        ],
        'CenterStatus' => [
            'xml_path' => '/CenterStatuses/CenterStatus',
            'primary' => 'CENTERSTID',
            'xml_fields' => [
                'CENTERSTID' => '@CENTERSTID',
                'NAME' => '@NAME',
            ],
            'file_regexp' => 'AS_CENTERST_.*\.XML',
        ],
        'CurrentStatus' => [
            'xml_path' => '/CurrentStatuses/CurrentStatus',
            'primary' => 'CURENTSTID',
            'xml_fields' => [
                'CURENTSTID' => '@CURENTSTID',
                'NAME' => '@NAME',
            ],
            'file_regexp' => 'AS_CURENTST_.*\.XML',
        ],
        'EstateStatus' => [
            'xml_path' => '/EstateStatuses/EstateStatus',
            'primary' => 'ESTSTATID',
            'xml_fields' => [
                'ESTSTATID' => '@ESTSTATID',
                'NAME' => '@NAME',
            ],
            'file_regexp' => 'AS_ESTSTAT_.*\.XML',
        ],
        'FlatType' => [
            'xml_path' => '/FlatTypes/FlatType',
            'primary' => 'FLTYPEID',
            'xml_fields' => [
                'FLTYPEID' => '@FLTYPEID',
                'NAME' => '@NAME',
                'SHORTNAME' => '@SHORTNAME',
            ],
            'file_regexp' => 'AS_FLATTYPE_.*\.XML',
        ],
        'HouseStateStatus' => [
            'xml_path' => '/HouseStateStatuses/HouseStateStatus',
            'primary' => 'HOUSESTID',
            'xml_fields' => [
                'HOUSESTID' => '@HOUSESTID',
                'NAME' => '@NAME',
            ],
            'file_regexp' => 'AS_HSTSTAT_.*\.XML',
        ],
        'IntervalStatus' => [
            'xml_path' => '/IntervalStatuses/IntervalStatus',
            'primary' => 'INTVSTATID',
            'xml_fields' => [
                'INTVSTATID' => '@INTVSTATID',
                'NAME' => '@NAME',
            ],
            'file_regexp' => 'AS_INTVSTAT_.*\.XML',
        ],
        'NormativeDocumentType' => [
            'xml_path' => '/NormativeDocumentTypes/NormativeDocumentType',
            'primary' => 'NDTYPEID',
            'xml_fields' => [
                'NDTYPEID' => '@NDTYPEID',
                'NAME' => '@NAME',
            ],
            'file_regexp' => 'AS_NDOCTYPE_.*\.XML',
        ],
        'OperationStatus' => [
            'xml_path' => '/OperationStatuses/OperationStatus',
            'primary' => 'OPERSTATID',
            'xml_fields' => [
                'OPERSTATID' => '@OPERSTATID',
                'NAME' => '@NAME',
            ],
            'file_regexp' => 'AS_OPERSTAT_.*\.XML',
        ],
        'RoomType' => [
            'xml_path' => '/RoomTypes/RoomType',
            'primary' => 'RMTYPEID',
            'xml_fields' => [
                'RMTYPEID' => '@RMTYPEID',
                'NAME' => '@NAME',
                'SHORTNAME' => '@SHORTNAME',
            ],
            'file_regexp' => 'AS_ROOMTYPE_.*\.XML',
        ],
        'AddressObjectType' => [
            'xml_path' => '/AddressObjectTypes/AddressObjectType',
            'primary' => 'KOD_T_ST',
            'xml_fields' => [
                'KOD_T_ST' => '@KOD_T_ST',
                'LEVEL' => '@LEVEL',
                'SOCRNAME' => '@SOCRNAME',
                'SCNAME' => '@SCNAME',
            ],
            'file_regexp' => 'AS_SOCRBASE_.*\.XML',
        ],
        'StructureStatus' => [
            'xml_path' => '/StructureStatuses/StructureStatus',
            'primary' => 'STRSTATID',
            'xml_fields' => [
                'STRSTATID' => '@STRSTATID',
                'NAME' => '@NAME',
                'SHORTNAME' => '@SHORTNAME',
            ],
            'file_regexp' => 'AS_STRSTAT_.*\.XML',
        ],
    ];

    /**
     * Конструктор.
     *
     * @param \PDO               $dbh     Объект pdo для подключения к бд
     * @param DirectoryInterface $workDir
     */
    public function __construct(PDO $dbh, DirectoryInterface $workDir)
    {
        $this->dbh = $dbh;
        $this->workDir = $workDir;
    }

    /**
     * Создает объект для чтения даных из файла и создания новых записей.
     *
     * @param string $entity    Название сущности фиас, длякоторой создается объект
     * @param string $tableName Название таблицы, в которую будет произведена запись
     * @param array  $fields    Массив с соответствием полей, вида "поле в таблице => поле в файле"
     *
     * @return \marvin255\fias\job\JobInterface
     */
    public function inserter($entity, $tableName, array $fields = [])
    {
        $description = $this->getEntityDescription($entity);
        $reader = $this->createReader($entity, $fields);
        $filter = $this->createFilter($entity);
        $inserter = new Inserter(
            $this->dbh,
            $tableName,
            $this->createPrimary($entity, $fields),
            array_keys($fields ?: $description['xml_fields'])
        );

        return new ReadAndProcess(
            $this->workDir,
            $reader,
            $inserter,
            $filter
        );
    }

    /**
     * Создает объект для чтения даных из файла и создания новых записей или
     * обновления старых, если такие будут найдены.
     *
     * @param string $entity    Название сущности фиас, длякоторой создается объект
     * @param string $tableName Название таблицы, в которую будет произведена запись
     * @param array  $fields    Массив с соответствием полей, вида "поле в таблице => поле в файле"
     *
     * @return \marvin255\fias\job\JobInterface
     */
    public function updater($entity, $tableName, array $fields = [])
    {
        $description = $this->getEntityDescription($entity);
        $reader = $this->createReader($entity, $fields);
        $filter = $this->createFilter($entity);
        $inserter = new Loader(
            $this->dbh,
            $tableName,
            $this->createPrimary($entity, $fields),
            array_keys($fields ?: $description['xml_fields'])
        );

        return new ReadAndProcess(
            $this->workDir,
            $reader,
            $inserter,
            $filter
        );
    }

    /**
     * Создает объект для чтения даных из файла иудаления указанных в нем записей.
     *
     * @param string $entity    Название сущности фиас, длякоторой создается объект
     * @param string $tableName Название таблицы, в которую будет произведена запись
     * @param array  $fields    Массив с соответствием полей, вида "поле в таблице => поле в файле"
     *
     * @return \marvin255\fias\job\JobInterface
     */
    public function deleter($entity, $tableName, array $fields = [])
    {
    }

    /**
     * Создает объект для чтения данных из xml файла.
     *
     * @param string $entity Название сущности фиас, для которой создается объект
     * @param array  $fields Массив с соответствием полей, вида "поле в таблице => поле в файле"
     *
     * @return \marvin255\fias\utils\xml\Reader
     *
     * @throws \InvalidArgumentException
     */
    protected function createReader($entity, array $fields)
    {
        $description = $this->getEntityDescription($entity);

        if (!empty($fields)) {
            $xmlFields = [];
            foreach ($fields as $table => $xml) {
                if (empty($description['xml_fields'][$xml])) {
                    throw new InvalidArgumentException(
                        "Can't find xml field for table field {$table} - {$xml}"
                    );
                }
                $xmlFields[$table] = $description['xml_fields'][$xml];
            }
        } else {
            $xmlFields = $description['xml_fields'];
        }

        return new Reader($description['xml_path'], $xmlFields);
    }

    /**
     * Создает массив с первичными ключами для поиска сущности в бд.
     *
     * @param string $entity Название сущности фиас, для которой создается объект
     * @param array  $fields Массив с соответствием полей, вида "поле в таблице => поле в файле"
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function createPrimary($entity, array $fields)
    {
        $description = $this->getEntityDescription($entity);

        $primary = is_array($description['primary'])
            ? $description['primary']
            : [$description['primary']];

        if (!empty($fields)) {
            $return = [];
            foreach ($primary as $primaryName) {
                $newKey = null;
                foreach ($fields as $table => $xml) {
                    if ($xml !== $primaryName) {
                        continue;
                    }
                    $newKey = $table;
                    break;
                }
                if (!$newKey) {
                    throw new InvalidArgumentException(
                        "Can't find field for primary {$primaryName}"
                    );
                }
                $return[] = $newKey;
            }
        } else {
            $return = $primary;
        }

        return $return;
    }

    /**
     * Создает объект для фильтрации файлов при поиске нужного.
     *
     * @param string $entity Название сущности фиас, для которой создается объект
     *
     * @return array
     */
    protected function createFilter($entity)
    {
        $description = $this->getEntityDescription($entity);
        $return = [];

        if (!empty($description['file_regexp'])) {
            $return[] = new FilterRegexp($description['file_regexp']);
        }

        return $return;
    }

    /**
     * Возвращает описание сущности по ее названию.
     *
     * @param string $name
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function getEntityDescription($name)
    {
        $return = !empty($this->entitiesDescription[$name])
            ? $this->entitiesDescription[$name]
            : null;

        if (!$return) {
            throw new InvalidArgumentException(
                "Can't find description for {$name} entity"
            );
        }

        return $return;
    }
}
