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
namespace Hao\ORMJsonRelation\Relation;

use Hao\ORMJsonRelation\HasJson;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\Constraint;
use Hyperf\Database\Model\Relations\HasMany;

use function Hyperf\Collection\data_get;
use function Hyperf\Support\value;

class HasManyJsonContains extends HasMany
{
    use HasJson;

    /**
     * Set the base constraints on the relation query.
     */
    public function addConstraints()
    {
        if (Constraint::isConstraint()) {
            $this->query->whereJsonContains($this->foreignKey, $this->getParentKey());

            $this->query->whereNotNull($this->foreignKey);
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     */
    public function addEagerConstraints(array $models)
    {
        $keys = $this->getKeys($models, $this->localKey);
        $foreignKey = $this->foreignKey;
        $this->query->where(static function (Builder $query) use ($keys, $foreignKey) {
            foreach ($keys as $key) {
                $query->orWhereJsonContains($foreignKey, $key);
            }
        });
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @return array
     */
    protected function buildDictionary(Collection $results)
    {
        $foreign = $this->getForeignKeyName();

        $dictionary = [];
        foreach ($results as $result) {
            $pairs = value(function () use ($result, $foreign) {
                [$modelKey, $path] = $this->getPath($foreign);
                $array = data_get($result->{$modelKey}, $path, []);
                $ret = [];
                foreach ($array as $key) {
                    $ret[$key] = $result;
                }
                return $ret;
            });

            foreach ($pairs as $key => $value) {
                if (! isset($dictionary[$key])) {
                    $dictionary[$key] = [];
                }
                $dictionary[$key][] = $value;
            }
        }

        return $dictionary;
    }
}
