<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Cases;

use Hyperf\Database\ConnectionResolver;
use Hyperf\Database\Connectors\ConnectionFactory;
use Hyperf\Database\Connectors\MySqlConnector;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Database\Model\Register;
use Hyperf\Engine\Constant;
use Hyperf\Utils\ApplicationContext;
use HyperfTest\Model\SQLBag;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractTestCase.
 */
abstract class AbstractTestCase extends TestCase
{
    protected function setUp(): void
    {
        $container = \Mockery::mock(ContainerInterface::class);
        ApplicationContext::setContainer($container);

        $container->shouldReceive('has')->andReturn(true);
        $container->shouldReceive('get')->with('db.connector.mysql')->andReturn(new MySqlConnector());

        $connector = new ConnectionFactory($container);

        $dbConfig = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'hyperf',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ];

        $connection = $connector->make($dbConfig);

        $resolver = new ConnectionResolver(['default' => $connection]);

        Register::setConnectionResolver($resolver);
        Register::setEventDispatcher($dispatcher = \Mockery::mock(EventDispatcherInterface::class));
        $connection->setEventDispatcher($dispatcher);

        $dispatcher->shouldReceive('dispatch')->withAnyArgs()->andReturnUsing(function (object $event) {
            if ($event instanceof QueryExecuted) {
                SQLBag::instance()->insert($event);
            }
        });
    }

    protected function tearDown(): void
    {
        \Mockery::close();
    }

    public function runInCoroutine(callable $callable)
    {
        if (extension_loaded('swoole') || extension_loaded('swow')) {
            if (Constant::ENGINE === 'Swoole') {
                run($callable);
                return;
            }
        }

        $callable();
    }
}
