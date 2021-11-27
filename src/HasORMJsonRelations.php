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
namespace Hao\ORMJsonRelation;

use Hao\ORMJsonRelation\Relation\HasManyInJsonArray;
use Hao\ORMJsonRelation\Relation\HasManyJsonContains;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\HasMany;

/**
 * @mixin Model
 */
trait HasORMJsonRelations
{
    /**
     * 查询本体在 related 中某个 json 结构中存在的 related.
     */
    public function hasManyJsonContains(string $related, string $foreignKey, ?string $localKey = null, ?string $path = null): HasMany
    {
        $instance = $this->newRelatedInstance($related);

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasManyJsonContains(
            $instance->newQuery(),
            $this,
            $instance->getTable() . '.' . $foreignKey,
            $localKey,
            $path ?? '$'
        );
    }

    /**
     * 查询本体某个 json 数组中所有的 related.
     */
    public function hasManyInJsonArray(string $related, string $foreignKey, ?string $localKey = null, ?string $path = null): HasMany
    {
        $instance = $this->newRelatedInstance($related);

        $localKey = $localKey ?: $this->getKeyName();

        return $this->newHasManyInJsonArray(
            $instance->newQuery(),
            $this,
            $instance->getTable() . '.' . $foreignKey,
            $localKey,
            $path ?? '$'
        );
    }

    protected function newHasManyInJsonArray(Builder $query, Model $parent, string $foreignKey, string $localKey, string $path = '$')
    {
        return new HasManyInJsonArray($query, $parent, $foreignKey, $localKey, $path);
    }

    protected function newHasManyJsonContains(Builder $query, Model $parent, string $foreignKey, string $localKey, string $path = '$')
    {
        return new HasManyJsonContains($query, $parent, $foreignKey, $localKey, $path);
    }
}
