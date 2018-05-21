<?php

namespace marvin255\fias\service\unpacker;

/**
 * Интерфейс для объекта, который распаковывает данные из архива.
 */
interface UnpackerInterface
{
    /**
     * Извлекает данные из указанного в первом параметре архива по
     * указанному во втором параметре пути.
     *
     * @param string $pathToArchive
     * @param string $pathToUnpack
     *
     * @throws \RuntimeException
     */
    public function unpack(string $pathToArchive, string $pathToUnpack);
}
