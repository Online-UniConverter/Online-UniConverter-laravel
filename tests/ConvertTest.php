<?php

namespace OnlineUniConverter\Laravel\Tests;

use OnlineUniConverter\Laravel\ConvertMultiple;

class ConvertTest extends BaseTest
{
    public function testConvertLocalFileWorks()
    {
        $convertLocalFile = new \OnlineUniConverter\Laravel\ConvertLocalFile(__DIR__ . '/stubs/tc.jpg');
        $this->assertSame('jpg', $convertLocalFile->getFormat());
        $this->assertSame('tc.jpg', $convertLocalFile->getFilename());
        $this->assertSame(__DIR__ . '/stubs', $convertLocalFile->getPath());
        $this->assertSame(__DIR__ . '/stubs' . '/' . 'tc.jpg', $convertLocalFile->getFilepath());
    }

    public function testConvertUploadedFileWorks()
    {
        $uploaded_file = \Mockery::mock(
            '\Symfony\Component\HttpFoundation\File\UploadedFile',
            [
                'getClientOriginalName' => 'tc.jpg',
                'getFilename' => __DIR__ . '/stubs/tc.jpg',
                'getClientOriginalExtension' => 'jpg',
                'getPathname' => __DIR__ . '/stubs/tc.jpg'
            ]
        );
        $convertLocalFile = new \OnlineUniConverter\Laravel\ConvertLocalFile($uploaded_file);
        $this->assertSame('jpg', $convertLocalFile->getFormat());
        $this->assertSame('tc.jpg', $convertLocalFile->getFilename());
        $this->assertSame(__DIR__ . '/stubs', $convertLocalFile->getPath());
        $this->assertSame(__DIR__ . '/stubs' . '/' . 'tc.jpg', $convertLocalFile->getFilepath());

    }

    public function testConvertLocalFileSaves()
    {
        $convertLocalFile = new \OnlineUniConverter\Laravel\ConvertLocalFile(__DIR__ . '/stubs/tc.jpg');

        $fileSystem = \Mockery::mock(
            '\Illuminate\Filesystem\Filesystem',
            [
                'isWritable' => true,
                'put' => true
            ]
        );
        $convertLocalFile->setFilesystem($fileSystem);
        $convertLocalFile->setData('BLOB');
        $this->assertTrue($convertLocalFile->save());
    }

    public function testExceptionIfFileNotWritable()
    {
        $this->expectException(\Exception::class);
        $convertLocalFile = new \OnlineUniConverter\Laravel\ConvertLocalFile(__DIR__ . '/stubs/tc_not_found.jpg');

        $fileSystem = \Mockery::mock(
            '\Illuminate\Filesystem\Filesystem',
            [
                'isWritable' => false,
                'put' => true
            ]
        );
        $convertLocalFile->setFilesystem($fileSystem);
        $convertLocalFile->setData('BLOB');
        $this->assertTrue($convertLocalFile->save());
    }

    public function testExceptionIfNotDataHasBeenSet()
    {
        $convertLocalFile = new \OnlineUniConverter\Laravel\ConvertLocalFile(__DIR__ . '/stubs/tc_not_data.jpg');

        $fileSystem = \Mockery::mock(
            '\Illuminate\Filesystem\Filesystem',
            [
                'isWritable' => true,
                'put' => true
            ]
        );
        $convertLocalFile->setFilesystem($fileSystem);
        $this->expectException(\Exception::class);
        $this->assertTrue($convertLocalFile->save());
    }

    public function testConvertLocalFileSetFormatWorksOnPath()
    {
        $convertLocalFile = new \OnlineUniConverter\Laravel\ConvertLocalFile(__DIR__ . '/stubs/tc.jpg');
        $convertLocalFile->setFormat('png');
        $this->assertSame(__DIR__ . '/stubs', $convertLocalFile->getPath());
        $this->assertSame('png', $convertLocalFile->getFormat());
    }

    public function testConvertLocalFileSetFilenameReturnsCorrectFormat()
    {
        $convertLocalFile = new \OnlineUniConverter\Laravel\ConvertLocalFile(__DIR__ . '/stubs/tc.jpg');
        $this->assertSame('jpg', $convertLocalFile->getFormat());
    }

    public function testCorrectFilenameIfExtensionGiven()
    {
        $convertLocalFile = new \OnlineUniConverter\Laravel\ConvertLocalFile(__DIR__ . '/stubs/tc.jpg');
        $convertLocalFile->setFilename('a-nice-pdf-file.pdf', 'jpg');
        $this->assertSame('a-nice-pdf-file.jpg', $convertLocalFile->getFilename());
        $this->assertSame('jpg', $convertLocalFile->getFormat());
    }

    public function testRemoteFileInput()
    {
        $convertRemoteFile = new \OnlineUniConverter\Laravel\ConvertRemoteFile('http://mirrors.creativecommons.org/presskit/icons/cc.large.png');
        $this->assertSame('png', $convertRemoteFile->getFormat());
        $this->assertSame('cc.large.png', $convertRemoteFile->getFilename());
        $this->assertSame('import/url', $convertRemoteFile->getOperation());
    }

    public function testOnlyOutputFormatGiven()
    {
        $inputConvertLocalFile = new \OnlineUniConverter\Laravel\ConvertLocalFile(__DIR__ . '/stubs/tc.jpg');
        $outputConvertLocalFile = new \OnlineUniConverter\Laravel\ConvertLocalFile('png');

        $this->assertSame('png', $outputConvertLocalFile->getFormat());
        $this->assertSame('jpg', $inputConvertLocalFile->getFormat());

        $this->assertSame('tc.jpg', $inputConvertLocalFile->getFilename());
        $this->assertEquals('', $outputConvertLocalFile->getFilename());

        $this->assertSame('.', $outputConvertLocalFile->getPath());
        $this->assertSame(__DIR__ . '/stubs', $inputConvertLocalFile->getPath());

        $outputConvertLocalFile->filenameCheck($inputConvertLocalFile);

        $this->assertSame('tc.png', $outputConvertLocalFile->getFilename());
        $this->assertSame($inputConvertLocalFile->getPath(), $outputConvertLocalFile->getPath());

    }

    public function testGuzzle6AdapterOutputNonFlatten()
    {
        $adapter = new \OnlineUniConverter\Laravel\HttpClientAdapter\GuzzleAdapter();

        $obj = new ConvertMultiple;
        $obj->file = @fopen(__DIR__ . '/stubs/tc.jpg', 'r');
        $obj->filename = 'tc.jpg';

        $outputMultipartContent = $adapter->getMultipartContent('file', $obj);

        $this->assertArrayHasKey('name', $outputMultipartContent[0]);
        $this->assertContains('file', $outputMultipartContent[0]);
        $this->assertArrayHasKey('contents', $outputMultipartContent[0]);
        $this->assertContains('tc.jpg', $outputMultipartContent[0]);
    }

    protected function tearDown(): void
    {
        $convertLocalFile = null;
    }
}