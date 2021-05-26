<?php

namespace OnlineUniConvert\Laravel\Tests;


use OnlineUniConvert\OnlineUniConvert;

class OnlineUniConvertTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        config()->set('onlineuniconvert', [
            'apiKey' => 'test',
        ]);
    }

    public function testOnlineUniConvertClassIsBound()
    {
        $OnlineUniConvert = app(OnlineUniConvert::class);
        $this->assertInstanceOf(OnlineUniConvert::class, $OnlineUniConvert);

        $reflection = new \ReflectionClass($OnlineUniConvert);
        $property = $reflection->getProperty('options');
        $property->setAccessible(true);
        $options = $property->getValue($OnlineUniConvert);

        $this->assertEquals('test', $options['apiKey']);
    }

    public function testFacadeIsRegistered()
    {
        app()->bind(OnlineUniConvert::class, function () {
            return new class() {
                public function users()
                {
                    return 'users';
                }
            };
        });
        $this->assertEquals('users', \OnlineUniConvert\Laravel\Facades\OnlineUniConvert::users());
    }

}
