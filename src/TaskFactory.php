<?php

namespace marvin255\fias;

use marvin255\fias\task\InsertData;
use marvin255\fias\task\UpdateData;
use InvalidArgumentException;

/**
 * Фабрика, которая собирает объекты для сущностей фиас.
 *
 * Все сущности описны в массиве сущностей, на основании массива и типа работы
 * будет собран объект задачи с соответствующими настройками.
 * Для настройки достаточно указать название таблицы в бд и, если
 * требуется, соответствия между полями в файле и в таблице бд.
 */
class TaskFactory
{
    /**
     * Массив с описаниями сущностей фиас.
     *
     * @var array
     */
    protected $entitiesDescription = [
        'Object' => [
            'xmlPathToNode' => '/AddressObjects/Object',
            'primary' => 'AOID',
            'xmlSelect' => [
                'AOID' => '@AOID',
                'AOGUID' => '@AOGUID',
                'PARENTGUID' => '@PARENTGUID',
                'NEXTID' => '@PARENTGUID',
                'FORMALNAME' => '@FORMALNAME',
                'OFFNAME' => '@OFFNAME',
                'SHORTNAME' => '@SHORTNAME',
                'AOLEVEL' => '@AOLEVEL',
                'REGIONCODE' => '@REGIONCODE',
                'AREACODE' => '@AREACODE',
                'AUTOCODE' => '@AUTOCODE',
                'CITYCODE' => '@CITYCODE',
                'CTARCODE' => '@CTARCODE',
                'PLACECODE' => '@PLACECODE',
                'PLANCODE' => '@PLANCODE',
                'STREETCODE' => '@STREETCODE',
                'EXTRCODE' => '@EXTRCODE',
                'SEXTCODE' => '@SEXTCODE',
                'PLAINCODE' => '@PLAINCODE',
                'CURRSTATUS' => '@CURRSTATUS',
                'ACTSTATUS' => '@ACTSTATUS',
                'LIVESTATUS' => '@LIVESTATUS',
                'CENTSTATUS' => '@CENTSTATUS',
                'OPERSTATUS' => '@OPERSTATUS',
                'IFNSFL' => '@IFNSFL',
                'IFNSUL' => '@IFNSUL',
                'TERRIFNSFL' => '@TERRIFNSFL',
                'TERRIFNSUL' => '@TERRIFNSUL',
                'OKATO' => '@OKATO',
                'OKTMO' => '@OKTMO',
                'POSTALCODE' => '@POSTALCODE',
                'STARTDATE' => '@STARTDATE',
                'ENDDATE' => '@ENDDATE',
                'UPDATEDATE' => '@UPDATEDATE',
                'DIVTYPE' => '@DIVTYPE',
            ],
            'insertFilePattern' => 'AS_ADDROBJ_*.XML',
        ],
        'House' => [
            'xmlPathToNode' => '/Houses/House',
            'primary' => 'HOUSEID',
            'xmlSelect' => [
                'HOUSEID' => '@HOUSEID',
                'HOUSEGUID' => '@HOUSEGUID',
                'AOGUID' => '@AOGUID',
                'HOUSENUM' => '@HOUSENUM',
                'STRSTATUS' => '@STRSTATUS',
                'ESTSTATUS' => '@ESTSTATUS',
                'STATSTATUS' => '@STATSTATUS',
                'IFNSFL' => '@IFNSFL',
                'IFNSUL' => '@IFNSUL',
                'OKATO' => '@OKATO',
                'OKTMO' => '@OKTMO',
                'POSTALCODE' => '@POSTALCODE',
                'STARTDATE' => '@STARTDATE',
                'ENDDATE' => '@ENDDATE',
                'UPDATEDATE' => '@UPDATEDATE',
                'COUNTER' => '@COUNTER',
                'DIVTYPE' => '@DIVTYPE',
            ],
            'insertFilePattern' => 'AS_HOUSE_*.XML',
            'bulkSize' => 100,
        ],
        'NormativeDocument' => [
            'xmlPathToNode' => '/NormativeDocumentes/NormativeDocument',
            'primary' => 'NORMDOCID',
            'xmlSelect' => [
                'NORMDOCID' => '@NORMDOCID',
                'DOCNAME' => '@DOCNAME',
                'DOCDATE' => '@DOCDATE',
                'DOCNUM' => '@DOCNUM',
                'DOCTYPE' => '@DOCTYPE',
            ],
            'insertFilePattern' => 'AS_NORMDOC_*.XML',
        ],
        'Room' => [
            'xmlPathToNode' => '/Rooms/Room',
            'primary' => 'ROOMID',
            'xmlSelect' => [
                'ROOMID' => '@ROOMID',
                'ROOMGUID' => '@ROOMGUID',
                'HOUSEGUID' => '@HOUSEGUID',
                'REGIONCODE' => '@REGIONCODE',
                'FLATNUMBER' => '@FLATNUMBER',
                'FLATTYPE' => '@FLATTYPE',
                'POSTALCODE' => '@POSTALCODE',
                'UPDATEDATE' => '@UPDATEDATE',
                'OPERSTATUS' => '@OPERSTATUS',
                'STARTDATE' => '@STARTDATE',
                'ENDDATE' => '@ENDDATE',
                'LIVESTATUS' => '@LIVESTATUS',
                'NORMDOC' => '@NORMDOC',
            ],
            'insertFilePattern' => 'AS_ROOM_*.XML',
            'bulkSize' => 100,
        ],
        'Stead' => [
            'xmlPathToNode' => '/Steads/Stead',
            'primary' => 'STEADGUID',
            'xmlSelect' => [
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
            'insertFilePattern' => 'AS_STEAD_*.XML',
        ],
        'ActualStatus' => [
            'xmlPathToNode' => '/ActualStatuses/ActualStatus',
            'primary' => 'ACTSTATID',
            'xmlSelect' => [
                'ACTSTATID' => '@ACTSTATID',
                'NAME' => '@NAME',
            ],
            'insertFilePattern' => 'AS_ACTSTAT_*.XML',
        ],
        'CenterStatus' => [
            'xmlPathToNode' => '/CenterStatuses/CenterStatus',
            'primary' => 'CENTERSTID',
            'xmlSelect' => [
                'CENTERSTID' => '@CENTERSTID',
                'NAME' => '@NAME',
            ],
            'insertFilePattern' => 'AS_CENTERST_*.XML',
        ],
        'CurrentStatus' => [
            'xmlPathToNode' => '/CurrentStatuses/CurrentStatus',
            'primary' => 'CURENTSTID',
            'xmlSelect' => [
                'CURENTSTID' => '@CURENTSTID',
                'NAME' => '@NAME',
            ],
            'insertFilePattern' => 'AS_CURENTST_*.XML',
        ],
        'EstateStatus' => [
            'xmlPathToNode' => '/EstateStatuses/EstateStatus',
            'primary' => 'ESTSTATID',
            'xmlSelect' => [
                'ESTSTATID' => '@ESTSTATID',
                'NAME' => '@NAME',
            ],
            'insertFilePattern' => 'AS_ESTSTAT_*.XML',
        ],
        'FlatType' => [
            'xmlPathToNode' => '/FlatTypes/FlatType',
            'primary' => 'FLTYPEID',
            'xmlSelect' => [
                'FLTYPEID' => '@FLTYPEID',
                'NAME' => '@NAME',
                'SHORTNAME' => '@SHORTNAME',
            ],
            'insertFilePattern' => 'AS_FLATTYPE_*.XML',
        ],
        'HouseStateStatus' => [
            'xmlPathToNode' => '/HouseStateStatuses/HouseStateStatus',
            'primary' => 'HOUSESTID',
            'xmlSelect' => [
                'HOUSESTID' => '@HOUSESTID',
                'NAME' => '@NAME',
            ],
            'insertFilePattern' => 'AS_HSTSTAT_*.XML',
        ],
        'IntervalStatus' => [
            'xmlPathToNode' => '/IntervalStatuses/IntervalStatus',
            'primary' => 'INTVSTATID',
            'xmlSelect' => [
                'INTVSTATID' => '@INTVSTATID',
                'NAME' => '@NAME',
            ],
            'insertFilePattern' => 'AS_INTVSTAT_*.XML',
        ],
        'NormativeDocumentType' => [
            'xmlPathToNode' => '/NormativeDocumentTypes/NormativeDocumentType',
            'primary' => 'NDTYPEID',
            'xmlSelect' => [
                'NDTYPEID' => '@NDTYPEID',
                'NAME' => '@NAME',
            ],
            'insertFilePattern' => 'AS_NDOCTYPE_*.XML',
        ],
        'OperationStatus' => [
            'xmlPathToNode' => '/OperationStatuses/OperationStatus',
            'primary' => 'OPERSTATID',
            'xmlSelect' => [
                'OPERSTATID' => '@OPERSTATID',
                'NAME' => '@NAME',
            ],
            'insertFilePattern' => 'AS_OPERSTAT_*.XML',
        ],
        'RoomType' => [
            'xmlPathToNode' => '/RoomTypes/RoomType',
            'primary' => 'RMTYPEID',
            'xmlSelect' => [
                'RMTYPEID' => '@RMTYPEID',
                'NAME' => '@NAME',
                'SHORTNAME' => '@SHORTNAME',
            ],
            'insertFilePattern' => 'AS_ROOMTYPE_*.XML',
        ],
        'AddressObjectType' => [
            'xmlPathToNode' => '/AddressObjectTypes/AddressObjectType',
            'primary' => 'KOD_T_ST',
            'xmlSelect' => [
                'KOD_T_ST' => '@KOD_T_ST',
                'LEVEL' => '@LEVEL',
                'SOCRNAME' => '@SOCRNAME',
                'SCNAME' => '@SCNAME',
            ],
            'insertFilePattern' => 'AS_SOCRBASE_*.XML',
        ],
        'StructureStatus' => [
            'xmlPathToNode' => '/StructureStatuses/StructureStatus',
            'primary' => 'STRSTATID',
            'xmlSelect' => [
                'STRSTATID' => '@STRSTATID',
                'NAME' => '@NAME',
                'SHORTNAME' => '@SHORTNAME',
            ],
            'insertFilePattern' => 'AS_STRSTAT_*.XML',
        ],
    ];

    /**
     * Создает объект для чтения даных из файла и создания новых записей.
     *
     * @param string $entity    Название сущности фиас, длякоторой создается объект
     * @param string $tableName Название таблицы, в которую будет произведена запись
     * @param array  $fields    Массив с соответствием полей, вида "поле в таблице => поле в файле"
     *
     * @return \marvin255\fias\TaskInterface
     */
    public function inserter(string $entity, string $tableName, array $fields = null): TaskInterface
    {
        $entityDescription = $this->getEntityDescription($entity);

        $filePattern = $entityDescription['insertFilePattern'];
        $pathToNode = $entityDescription['xmlPathToNode'];
        $select = $fields ?: $entityDescription['xmlSelect'];
        $bulk = !empty($entityDescription['bulkSize']) ? $entityDescription['bulkSize'] : 200;

        return new InsertData($tableName, $filePattern, $pathToNode, $select, $bulk);
    }

    /**
     * Создает объект для чтения даных из файла и создания новых записей.
     *
     * @param string $entity    Название сущности фиас, длякоторой создается объект
     * @param string $tableName Название таблицы, в которую будет произведена запись
     * @param string $primary   Название первичного ключа для таблицы
     * @param array  $fields    Массив с соответствием полей, вида "поле в таблице => поле в файле"
     *
     * @return \marvin255\fias\TaskInterface
     */
    public function updater(string $entity, string $tableName, string $primary = null, array $fields = null): TaskInterface
    {
        $entityDescription = $this->getEntityDescription($entity);

        $filePattern = $entityDescription['insertFilePattern'];
        $pathToNode = $entityDescription['xmlPathToNode'];
        $select = $fields ?: $entityDescription['xmlSelect'];
        $primary = $primary ?: $entityDescription['primary'];

        return new UpdateData($tableName, $primary, $filePattern, $pathToNode, $select);
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
    protected function getEntityDescription(string $name): array
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
