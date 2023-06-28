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
namespace HyperfTest\Model;

use Hyperf\Database\Events\QueryExecuted;

class SQLBag
{
    public array $sqls = [];

    private static $instance;

    public static function instance()
    {
        if (static::$instance) {
            return static::$instance;
        }

        return static::$instance = new self();
    }

    public function insert(QueryExecuted $executed): void
    {
        $this->sqls[] = $executed;
    }

    public function shift(): ?QueryExecuted
    {
        return array_shift($this->sqls);
    }
}
