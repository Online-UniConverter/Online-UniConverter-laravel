<?php

namespace OnlineUniConverter\Laravel\Tests;

abstract class BaseTest extends \PHPUnit\Framework\TestCase
{
    protected $onlineUniConverter;
    protected $client;
    protected $process_client;
    protected $config;

    function __construct()
    {
        date_default_timezone_set('Europe/London');
        parent::__construct();
        $this->config = \Mockery::mock('\OnlineUniConverter\Laravel\Config')->shouldReceive('get')->andReturn('VALUE');
        $this->onlineUniConverter = new \OnlineUniConverter\Laravel\OnlineUniConverter();
        //$client = $this->mockClient();
        //$this->onlineUniConverter->setClient($client);
    }

    /**
     * @throws \Exception
     */
    public function mockInputUpload()
    {
        $uploaded_file = \Mockery::mock(
            '\Symfony\Component\HttpFoundation\File\UploadedFile',
            [
                'getClientOriginalName' => 'image-1.jpg',
                'getFilename' => '/tmp/image-1.jpg',
                'getClientOriginalExtension' => 'jpg',
                'getPathname' => '/tmp/image-1.jpg'
            ]
        );
        $this->onlineUniConverter->from($uploaded_file);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function mockInputUrl()
    {
        $url = 'http://mirrors.creativecommons.org/presskit/logos/cc.logo.large.png';
        $this->onlineUniConverter->from($url);

        return $url;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function mockInputFilepath()
    {
        $file_path = __DIR__ . '/stubs/tc.jpg';
        $this->onlineUniConverter->from($file_path);

        return $file_path;
    }

    public function mockClient()
    {
        $this->client = \Mockery::mock(
            '\OnlineUniConverter\Laravel\HttpClientInterface'
        );
        return $this->client;
    }

    public function mockProcessClient()
    {
        $this->process_client = \Mockery::mock(
            '\OnlineUniConverter\Laravel\HttpClientInterface'
        );
        return $this->process_client;
    }
}