<?php

namespace OnlineUniConverter\Laravel\Tests;

class FunctionalTest extends BaseTest
{
    protected $fileSystem;
    protected $response;

    protected function setUp(): void
    {
        $this->fileSystem = \Mockery::mock('\Illuminate\Filesystem\Filesystem');
        $this->response = \Mockery::mock('\GuzzleHttp\Message\Response', ['__construct' => null]);
    }

    public function testOnlineUniConverterApiKey()
    {
        $this->assertFalse($this->onlineUniConverter->hasApiKey());
        $this->onlineUniConverter->setApiKey('API_KEY');
        $this->assertTrue($this->onlineUniConverter->hasApiKey());
        $this->assertSame($this->onlineUniConverter->getApiKey(), 'API_KEY');
    }

    public function testInputFilepath()
    {
        $file_path = $this->mockInputFilepath();
        $this->assertSame($this->onlineUniConverter->getInput()->getFormat(), 'jpg');
        $this->assertSame($this->onlineUniConverter->getInput()->getOperation(), 'import/upload');
    }

    public function testMakeWithOneInput()
    {
        $this->onlineUniConverter->setApiKey('API_KEY');
        $this->fileSystem->shouldReceive('isFile')->once()->andReturn(true);
        $this->onlineUniConverter->setFilesystem($this->fileSystem);
        $this->response->url = 'http://process-url';
        $client = $this->mockClient();
        $client->shouldReceive('post')->andReturn($this->response);
        $this->onlineUniConverter->setClient($client);
        $this->onlineUniConverter->from(__DIR__ . '/stubs/tc.jpg');
        $this->assertSame($this->onlineUniConverter->getInputFormat(), 'jpg');
        $this->assertSame($this->onlineUniConverter->getOutputFormat(), null);
        $this->assertSame($this->onlineUniConverter->getInput()->getOperation(), 'import/upload');
    }

    public function testMakeWithTwoInputs()
    {
        $this->onlineUniConverter->setApiKey('API_KEY');
        $this->fileSystem->shouldReceive('isFile')->once()->andReturn(true);
        $this->onlineUniConverter->setFilesystem($this->fileSystem);
        $this->response->url = 'http://process-url';
        $client = $this->mockClient();
        $client->shouldReceive('post')->andReturn($this->response);
        $this->onlineUniConverter->setClient($client);
        $this->onlineUniConverter->from(__DIR__ . '/stubs/tc.jpg')->to('png');
        $this->assertSame($this->onlineUniConverter->getInputFormat(), 'jpg');
        $this->assertSame($this->onlineUniConverter->getOutputFormat(), 'png');
        $this->assertSame($this->onlineUniConverter->getInput()->getOperation(), 'import/upload');
    }

    public function testUsingFileMethod()
    {
        $this->onlineUniConverter->setApiKey('API_KEY');
        $this->fileSystem->shouldReceive('isFile')->once()->andReturn(true);
        $this->onlineUniConverter->setFilesystem($this->fileSystem);
        $this->onlineUniConverter->from(__DIR__ . '/stubs/tc.jpg');
        $this->assertSame($this->onlineUniConverter->getInputFormat(), 'jpg');
        $this->assertSame($this->onlineUniConverter->getInput()->getOperation(), 'import/upload');
    }

    public function testUsingFileUploadMethod()
    {
        $this->onlineUniConverter->setApiKey('API_KEY');
        $uploaded_file = \Mockery::mock('\Symfony\Component\HttpFoundation\File\UploadedFile',
            [
                'getClientOriginalName' => 'image-1.jpg',
                'getFilename' => '/tmp/image-1.jpg',
                'getClientOriginalExtension' => 'jpg',
                'getPathname' => '/tmp/image-1.jpg'
            ]
        );
        $this->onlineUniConverter->from($uploaded_file);
        $this->assertSame($this->onlineUniConverter->getInputFormat(), 'jpg');
        $this->assertSame($this->onlineUniConverter->getInput()->getOperation(), 'import/upload');
    }
}