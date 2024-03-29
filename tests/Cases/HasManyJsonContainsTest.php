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

use HyperfTest\Model\JsonWorker;
use HyperfTest\Model\SQLBag;

/**
 * @internal
 * @coversNothing
 */
class HasManyJsonContainsTest extends AbstractTestCase
{
    public function testHasManyContains()
    {
        $this->runInCoroutine(function () {
            $main = JsonWorker::query()->find(1);
            foreach ($main->mains as $main) {
                $this->assertSame(1, $main->id);
            }

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_worker` where `json_worker`.`id` = ? limit 1',
                'select * from `json_main` where json_contains(`json_main`.`workers`, ?) and `json_main`.`workers` is not null',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }

    public function testHasManyContainsInJsonData()
    {
        $this->runInCoroutine(function () {
            $model = JsonWorker::query()->find(1);
            foreach ($model->mainsInData as $main) {
                $this->assertSame(2, $main->id);
            }

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_worker` where `json_worker`.`id` = ? limit 1',
                "select * from `json_main` where json_contains(`json_main`.`data`, ?, '$.\"worker_ids\"') and json_unquote(json_extract(`json_main`.`data`, '$.\"worker_ids\"')) is not null",
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }

    public function testEagerLoadHasManyContains()
    {
        $this->runInCoroutine(function () {
            $models = JsonWorker::query()->find([1, 2]);
            $models->load('mains');
            $i = 1;
            foreach ($models as $model) {
                $this->assertSame($i++, $model->mains->count());
            }

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_worker` where `json_worker`.`id` in (?, ?)',
                'select * from `json_main` where (json_contains(`json_main`.`workers`, ?) or json_contains(`json_main`.`workers`, ?))',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }

    public function testEagerLoadHasManyContainsInJsonData()
    {
        $this->runInCoroutine(function () {
            $model = JsonWorker::query()->find(1);
            $model->load('mainsInData');

            foreach ($model->mainsInData as $main) {
                $this->assertSame(2, $main->id);
            }

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_worker` where `json_worker`.`id` = ? limit 1',
                "select * from `json_main` where (json_contains(`json_main`.`data`, ?, '$.\"worker_ids\"'))",
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }

    public function testManyHasManyContains()
    {
        $this->runInCoroutine(function () {
            $models = JsonWorker::query()->find([1, 2]);
            foreach ($models as $model) {
                $model->mains;
            }

            $bag = SQLBag::instance();
            $asserts = [
                'select * from `json_worker` where `json_worker`.`id` in (?, ?)',
                'select * from `json_main` where json_contains(`json_main`.`workers`, ?) and `json_main`.`workers` is not null',
                'select * from `json_main` where json_contains(`json_main`.`workers`, ?) and `json_main`.`workers` is not null',
            ];
            while ($event = $bag->shift()) {
                $this->assertSame(array_shift($asserts), $event->sql);
            }
        });
    }
}
