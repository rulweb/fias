<?php

namespace marvin255\fias\service\fias;

use SoapClient;

/**
 * Объекта, который обращается к сервису обновления ФИАС.
 */
class UpdateSericeSoap implements UpdateServiceInterface
{
    /**
     * @const
     */
    const DEFAULT_FIAS_WSDL = 'http://fias.nalog.ru/WebServices/Public/DownloadService.asmx?WSDL';
    /**
     * @var \SoapClient
     */
    protected $soapClient;

    /**
     * Задает SoapClient, если объет не задан, то создает самостоятельно.
     *
     * @param \SoapClient $soapClient
     */
    public function __construct(SoapClient $soapClient = null)
    {
        if ($soapClient === null) {
            $soapClient = new SoapClient(self::DEFAULT_FIAS_WSDL, [
                'exceptions' => true,
            ]);
        }

        $this->soapClient = $soapClient;
    }

    /**
     * @inheritdoc
     */
    public function getUrlForCompleteData(): array
    {
        $res = $this->soapClient->GetLastDownloadFileInfo();

        return [
            'url' => $res->GetLastDownloadFileInfoResult->FiasCompleteXmlUrl,
            'version' => $res->GetLastDownloadFileInfoResult->VersionId,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getUrlForDeltaData(int $fiasVersion): array
    {
        $return = [];

        $res = $this->soapClient->GetAllDownloadFileInfo();
        $versions = [];
        $versionsSort = [];
        foreach ($res->GetAllDownloadFileInfoResult->DownloadFileInfo as $key => $version) {
            $versions[$key] = (array) $version;
            $versionsSort[$key] = (int) $version->VersionId;
        }
        array_multisort($versionsSort, SORT_ASC, $versions);

        foreach ($versions as $version) {
            if ($version['VersionId'] <= $fiasVersion) {
                continue;
            }
            $return = [
                'url' => $version['FiasDeltaXmlUrl'],
                'version' => $version['VersionId'],
            ];
            break;
        }

        return $return;
    }
}
