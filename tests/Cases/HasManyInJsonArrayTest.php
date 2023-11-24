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
class HasManyInJsonArrayTest extends AbstractTestCase
{
    public function testHasManyInJson()
    {
        $this->runInCoroutine(function () {
            $main = JsonMain::query()->find(1);

            $workers = $main->workerModels;
            $i = 1;
            while ($worker = $workers->shift()) {
                $this->assertSame($i++, $worker->id);
            }

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_main` where `json_main`.`id` = ? limit 1',
                'select * from `json_worker` where `json_worker`.`id` in (?, ?, ?) and `json_worker`.`id` is not null',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }

    public function testEagerLoadHasManyInJson()
    {
        $this->runInCoroutine(function () {
            $mains = JsonMain::query()->find([1, 2]);
            $mains->load('workerModels');
            foreach ($mains as $main) {
                $main->workerModels;
            }

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_main` where `json_main`.`id` in (?, ?)',
                'select * from `json_worker` where `json_worker`.`id` in (?, ?, ?)',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }

    public function testHasManyInJsonData()
    {
        $this->runInCoroutine(function () {
            $mains = JsonMain::query()->find([1, 2])->getDictionary();
            $this->assertTrue($mains[1]->workersInData->isEmpty());
            $this->assertSame(2, $mains[2]->workersInData->count());

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_main` where `json_main`.`id` in (?, ?)',
                'select * from `json_worker` where 0 = 1 and `json_worker`.`id` is not null',
                'select * from `json_worker` where `json_worker`.`id` in (?, ?) and `json_worker`.`id` is not null',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }

    public function testEagerLoadHasManyInJsonData()
    {
        $this->runInCoroutine(function () {
            $mains = JsonMain::query()->find([1, 2]);
            $mains->load('workersInData');
            $mains = $mains->getDictionary();
            $this->assertTrue($mains[1]->workersInData->isEmpty());
            $this->assertSame(2, $mains[2]->workersInData->count());

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_main` where `json_main`.`id` in (?, ?)',
                'select * from `json_worker` where `json_worker`.`id` in (?, ?)',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }

    public function testManyHasManyInJson()
    {
        $this->runInCoroutine(function () {
            $mains = JsonMain::query()->find([1, 2]);
            foreach ($mains as $main) {
                $main->workerModels;
            }

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_main` where `json_main`.`id` in (?, ?)',
                'select * from `json_worker` where `json_worker`.`id` in (?, ?, ?) and `json_worker`.`id` is not null',
                'select * from `json_worker` where `json_worker`.`id` in (?, ?) and `json_worker`.`id` is not null',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }
}
