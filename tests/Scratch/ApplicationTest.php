<?php

namespace Tests\Scratch;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\ViewServiceProvider;
use LaravelBridge\Scratch\Application;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * @var Application
     */
    private $target;

    protected function setUp(): void
    {
        parent::setUp();

        $this->target = new Application();
    }

    protected function tearDown(): void
    {
        $this->target = null;

        parent::tearDown();
    }

    public function testGetInstanceInContainer(): void
    {
        $instance = Application::getInstance();

        $this->assertInstanceOf(Collection::class, $instance->get(Collection::class));
    }

    public function testShouldThrowExceptionWhenGetNotExistClass(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $this->target->get('whatever');
    }

    public function testCheckInstanceInContainer(): void
    {
        $this->target->setupView(__DIR__, __DIR__);

        $this->assertTrue($this->target->has('view'));
        $this->assertFalse($this->target->has('whatever'));
    }

    public function testInstance(): void
    {
        $this->target->setupRunningInConsole(false)
            ->setupView(__DIR__, __DIR__)
            ->bootstrap();

        $this->assertInstanceOf(ViewFactory::class, $this->target->make('view'));
    }

    public function testSetupCallableProvider(): void
    {
        $this->target->setupRunningInConsole(false)
            ->setupCallableProvider(function ($app) {
                $app['config']['view.paths'] = [__DIR__];
                $app['config']['view.compiled'] = __DIR__;

                return new ViewServiceProvider($app);
            });

        $this->target->bootstrap();

        $this->assertInstanceOf(ViewFactory::class, $this->target->make('view'));
    }

    public function testLangDirective(): void
    {
        $this->target->setupLocale('en')
            ->setupRunningInConsole(false)
            ->setupTranslator($this->resourcePath('lang'))
            ->setupView($this->resourcePath('views'), $this->storagePath('framework/views'))
            ->bootstrap();

        $actual = View::make('lang_test')->render();

        $this->assertSame('bar', $actual);
    }

    public function testLog(): void
    {
        $spy = new TestHandler();

        $logger = new Logger('test');
        $logger->pushHandler($spy);

        $this->target
            ->setupRunningInConsole(false)
            ->setupView(__DIR__, __DIR__)
            ->setupLogger('test', $logger, true)
            ->bootstrap();

        Log::info('log_test');

        $this->assertTrue($spy->hasInfoRecords());
    }
}
