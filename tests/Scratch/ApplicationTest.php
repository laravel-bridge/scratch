<?php

namespace Tests\Scratch;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\ViewServiceProvider;
use LaravelBridge\Scratch\Application;
use Mockery;
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

        $this->target = new Application(dirname(__DIR__) . '/Fixture');
    }

    protected function tearDown(): void
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldReturnSameInstanceWhenCreateFromBaseContainer(): void
    {
        $container = new Container();
        $container->instance('foo', 'foo');

        $target = Application::createFromBase($container);

        $this->assertSame('foo', $container->get('foo'));
        $this->assertSame('foo', $target->get('foo'));
        $this->assertInstanceOf(Repository::class, $target->get('config'));
    }

    public function testShouldThrowExceptionWhenGetNotExistClass(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $this->target->get('whatever');
    }

    public function testCheckInstanceInContainer(): void
    {
        $this->target->setupRunningInConsole(false)
            ->setupView(__DIR__, $this->vfs->url())
            ->bootstrap();

        $this->assertTrue($this->target->has('view'));
        $this->assertFalse($this->target->has('whatever'));
    }

    public function testInstance(): void
    {
        $this->target->setupRunningInConsole(false)
            ->setupView(__DIR__, $this->vfs->url())
            ->bootstrap();

        $this->assertInstanceOf(ViewFactory::class, $this->target->make('view'));
    }

    public function testSetupCallableProvider(): void
    {
        $this->target->setupRunningInConsole(false)
            ->setupCallableProvider(function ($app) {
                $app['config']['view.paths'] = [__DIR__];
                $app['config']['view.compiled'] = $this->vfs->url();

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
            ->setupView($this->resourcePath('views'), $this->vfs->url())
            ->withFacades()
            ->bootstrap();

        $actual = View::make('lang_test')->render();

        $this->assertSame('bar', $actual);
    }

    public function testLangDirectiveWithAppendViewPath(): void
    {
        $this->target->setupLocale('en')
            ->setupRunningInConsole(false)
            ->setupTranslator($this->resourcePath('lang'))
            ->setupView($this->resourcePath('views'), $this->vfs->url(), true)
            ->withFacades()
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
            ->setupView(__DIR__, $this->vfs->url())
            ->setupLogger('test', $logger, true)
            ->withFacades()
            ->withAliases()
            ->bootstrap();

        Log::info('log_test');

        $this->assertTrue($spy->hasInfoRecords());
    }

    public function testSetupConfig(): void
    {
        $this->target
            ->setupRunningInConsole(false)
            ->setupConfig('foo', 'bar')
            ->setupView(__DIR__, $this->vfs->url())
            ->bootstrap();

        $this->assertSame('bar', $this->target['config']['foo']);
    }

    public function testConfigurationLoader(): void
    {
        $this->target
            ->setupRunningInConsole(false)
            ->useConfigurationLoader()
            ->setupView(__DIR__, $this->vfs->url())
            ->bootstrap();

        $this->assertSame('baz', $this->target['config']['foo.bar']);
    }

    public function testConfigurationLoaderWillUseMethodConfigFirst(): void
    {
        $this->target
            ->setupRunningInConsole(false)
            ->useConfigurationLoader()
            ->setupView(__DIR__, $this->vfs->url())
            ->setupConfig('foo', ['bar' => 'from-setup'])
            ->bootstrap();

        $this->assertSame('from-setup', $this->target['config']['foo.bar']);
    }

    public function testWithoutLaravelProvider(): void
    {
        $this->target
            ->withoutLaravelProvider([PaginationServiceProvider::class])
            ->setupView(__DIR__, $this->vfs->url())
            ->bootstrap();

        $this->assertInstanceOf(ViewFactory::class, $this->target->make('view'));
    }

    public function testSetupProviderWillRegisterOnce(): void
    {
        $this->expectNotToPerformAssertions();

        $mock = Mockery::mock(ServiceProvider::class);
        $mock->shouldReceive('register');

        $this->target->setupProvider($mock, true);

        $mock->shouldHaveReceived('register')->once();
    }

    public function testSetupProviderAndBootstrapWillRegisterOnce(): void
    {
        $this->expectNotToPerformAssertions();

        $mock = Mockery::mock(ServiceProvider::class);
        $mock->shouldReceive('register');

        $this->target
            ->setupProvider($mock, true)
            ->bootstrap();

        $mock->shouldHaveReceived('register')->once();
    }
}
