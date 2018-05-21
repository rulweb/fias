<?php

namespace marvin255\fias\tests\service\fias;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\service\fias\UpdateSericeSoap;
use Mockery;
use SoapClient;

class UpdateSericeSoapTest extends BaseTestCase
{
    public function testGetUrlForCompleteData()
    {
        $result = new \stdClass;
        $result->GetLastDownloadFileInfoResult = new \stdClass;
        $result->GetLastDownloadFileInfoResult->FiasCompleteXmlUrl = $this->faker()->unique()->url;
        $result->GetLastDownloadFileInfoResult->VersionId = $this->faker()->unique()->randomNumber;

        $soapClient = Mockery::mock(SoapClient::class);
        $soapClient->shouldReceive('GetLastDownloadFileInfo')->once()->andReturn($result);

        $service = new UpdateSericeSoap($soapClient);

        $this->assertSame(
            [
                'url' => $result->GetLastDownloadFileInfoResult->FiasCompleteXmlUrl,
                'version' => $result->GetLastDownloadFileInfoResult->VersionId,
            ],
            $service->getUrlForCompleteData()
        );
    }

    public function testGetUrlForDeltaData()
    {
        $result = new \stdClass;
        $result->GetAllDownloadFileInfoResult = new \stdClass;
        $result->GetAllDownloadFileInfoResult->DownloadFileInfo = [];

        $totalDeltas = 10;
        $currentDelta = $this->faker()->unique()->numberBetween(1, $totalDeltas - 1);
        $nextDelta = $currentDelta + 1;
        $nextUrl = null;
        for ($i = 1; $i <= $totalDeltas; ++$i) {
            $delta = new \stdClass;
            $delta->VersionId = $i;
            $delta->FiasDeltaXmlUrl = $this->faker()->unique()->url;
            $result->GetAllDownloadFileInfoResult->DownloadFileInfo[] = $delta;
            if ($i === $nextDelta) {
                $nextUrl = $delta->FiasDeltaXmlUrl;
            }
        }
        shuffle($result->GetAllDownloadFileInfoResult->DownloadFileInfo);

        $soapClient = Mockery::mock(SoapClient::class);
        $soapClient->shouldReceive('GetAllDownloadFileInfo')->once()->andReturn($result);

        $service = new UpdateSericeSoap($soapClient);

        $this->assertSame(
            ['url' => $nextUrl, 'version' => $nextDelta],
            $service->getUrlForDeltaData($currentDelta)
        );
    }
}
