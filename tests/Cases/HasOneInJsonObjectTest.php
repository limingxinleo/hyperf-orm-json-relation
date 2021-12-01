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

use HyperfTest\Model\JsonMain;
use HyperfTest\Model\SQLBag;

/**
 * @internal
 * @coversNothing
 */
class HasOneInJsonObjectTest extends AbstractTestCase
{
    public function testHasOne()
    {
        $this->runInCoroutine(function () {
            $main = JsonMain::query()->find(2);
            $worker = $main->workerInData;
            $this->assertSame(1, $worker->id);

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_main` where `json_main`.`id` = ? limit 1',
                'select * from `json_worker` where `json_worker`.`id` = ? and `json_worker`.`id` is not null limit 1',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }

    public function testHasOneEagerLoad()
    {
        $this->runInCoroutine(function () {
            $main = JsonMain::query()->find(2);
            $main->load('workerInData');
            $worker = $main->workerInData;
            $this->assertSame(1, $worker->id);

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_main` where `json_main`.`id` = ? limit 1',
                'select * from `json_worker` where `json_worker`.`id` in (?)',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }

    public function testHasOneEagerLoadForCollection()
    {
        $this->runInCoroutine(function () {
            $main = JsonMain::query()->find([1, 2]);
            $main->load('worker');
            $this->assertNull($main[0]->worker);
            $this->assertSame(1, $main[1]->worker->id);

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_main` where `json_main`.`id` in (?, ?)',
                'select * from `json_worker` where `json_worker`.`id` in (?, ?)',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });

        $this->runInCoroutine(function () {
            $main = JsonMain::query()->find([1, 2]);
            $main->load('workerInData');
            $this->assertNull($main[0]->workerInData);
            $this->assertSame(1, $main[1]->workerInData->id);

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_main` where `json_main`.`id` in (?, ?)',
                'select * from `json_worker` where `json_worker`.`id` in (?)',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }
}
