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
use Hyperf\Utils\Traits\StaticInstance;

class SQLBag
{
    use StaticInstance;

    public array $sqls = [];

    public function insert(QueryExecuted $executed): void
    {
        array_push($this->sqls, $executed);
    }

    public function shift(): ?QueryExecuted
    {
        return array_shift($this->sqls);
    }
}
