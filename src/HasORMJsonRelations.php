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

use Hao\ORMJsonRelation\Relation\HasManyJsonContains;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;

/**
 * @mixin Model
 */
trait HasORMJsonRelations
{
    /**
     * Define a one-to-many relationship.
     *
     * @return \Hyperf\Database\Model\Relations\HasMany
     */
    public function hasManyJsonContains(string $related, string $foreignKey, ?string $localKey = null, ?string $path = null)
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

    protected function newHasManyJsonContains(Builder $query, Model $parent, string $foreignKey, string $localKey, string $path = '$')
    {
        return new HasManyJsonContains($query, $parent, $foreignKey, $localKey, $path);
    }
}
