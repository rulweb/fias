<?php

namespace marvin255\fias\tests\service\downloader;

use marvin255\fias\tests\BaseTestCase;
use marvin255\fias\service\downloader\Curl;
use Mockery;
use RuntimeException;

class CurlTest extends BaseTestCase
{
    /**
     * @var string
     */
    protected $tempFile;

    public function testDownload()
    {
        $urlToDownload = $this->faker()->unique()->url;

        $curl = Mockery::mock(Curl::class . '[curlDownload]')->shouldAllowMockingProtectedMethods();
        $curl->shouldReceive('curlDownload')->once()->withArgs(function ($requestOptions) use ($urlToDownload) {
            return in_array($urlToDownload, $requestOptions)
                && isset($requestOptions[CURLOPT_FILE])
                && is_resource($requestOptions[CURLOPT_FILE]);
        })->andReturn([true, 200, null]);

        $curl->download($urlToDownload, $this->tempFile);
    }

    public function testCurlErrorException()
    {
        $urlToDownload = $this->faker()->unique()->url;

        $curl = Mockery::mock(Curl::class . '[curlDownload]')->shouldAllowMockingProtectedMethods();
        $curl->shouldReceive('curlDownload')->once()->withArgs(function ($requestOptions) use ($urlToDownload) {
            return in_array($urlToDownload, $requestOptions)
                && isset($requestOptions[CURLOPT_FILE])
                && is_resource($requestOptions[CURLOPT_FILE]);
        })->andReturn([false, 0, 'error']);

        $this->expectException(RuntimeException::class);
        $curl->download($urlToDownload, $this->tempFile);
    }

    public function testWrongResponseCodeException()
    {
        $urlToDownload = $this->faker()->unique()->url;

        $curl = Mockery::mock(Curl::class . '[curlDownload]')->shouldAllowMockingProtectedMethods();
        $curl->shouldReceive('curlDownload')->once()->withArgs(function ($requestOptions) use ($urlToDownload) {
            return in_array($urlToDownload, $requestOptions)
                && isset($requestOptions[CURLOPT_FILE])
                && is_resource($requestOptions[CURLOPT_FILE]);
        })->andReturn([true, 500, null]);

        $this->expectException(RuntimeException::class);
        $curl->download($urlToDownload, $this->tempFile);
    }

    public function setUp()
    {
        $this->tempFile = sys_get_temp_dir()
            . '/' . $this->faker()->unique()->word
            . '.' . $this->faker()->unique()->word;

        parent::setUp();
    }

    public function tearDown()
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }

        parent::tearDown();
    }
}
