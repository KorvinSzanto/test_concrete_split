<?php
namespace Concrete\Core\Foundation\Bus;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;

class BusServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app->singleton('bus/list', function($app) {
            $locator = new InMemoryLocator();
            $config = $app->make('config');
            foreach($config->get('app.commands') as $command => $handler) {
                $locator->addHandler($app->make($handler), $command);
            }

            return $locator;
        });

        $this->app->singleton(CommandBus::class, function($app) {
            $locator = $app->make('bus/list');
            $handlerMiddleware = new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                $locator,
                new HandleClassNameInflector()
            );
            $commandBus = new CommandBus([$handlerMiddleware]);
            return $commandBus;
        });

        $this->app->bind('bus', CommandBus::class);
    }
}
