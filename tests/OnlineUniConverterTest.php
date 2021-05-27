<?php

namespace OnlineUniConverter\Laravel\Tests;


use OnlineUniConverter\OnlineUniConverter;

class OnlineUniConverterTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        config()->set('onlineuniconverter', [
            'apiKey' => 'test',
        ]);
    }

    public function testOnlineUniConverterClassIsBound()
    {
        $OnlineUniConverter = app(OnlineUniConverter::class);
        $this->assertInstanceOf(OnlineUniConverter::class, $OnlineUniConverter);

        $reflection = new \ReflectionClass($OnlineUniConverter);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);
        $options = $property->getValue($OnlineUniConverter);

        $this->assertEquals('test', $options['apiKey']);
    }

    public function testFacadeIsRegistered()
    {
        app()->bind(OnlineUniConverter::class, function () {
            return new class() {
                public function users()
                {
                    return 'users';
                }
            };
        });
        $this->assertEquals('users', \OnlineUniConverter\Laravel\Facades\OnlineUniConverter::users());
    }

}
