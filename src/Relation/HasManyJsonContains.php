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

use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\Constraint;

class HasManyJsonContains extends HasMany
{
    public function __construct(Builder $query, Model $parent, string $foreignKey, string $localKey, protected string $path = '$')
    {
        parent::__construct($query, $parent, $foreignKey, $localKey);
    }

    /**
     * Set the base constraints on the relation query.
     */
    public function addConstraints()
    {
        if (Constraint::isConstraint()) {
            $this->query->whereRaw("JSON_CONTAINS({$this->foreignKey}, ?, ?)", [$this->getParentKey(), $this->getPath()]);

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
        $path = $this->getPath();
        $this->query->where(static function (Builder $query) use ($keys, $foreignKey, $path) {
            foreach ($keys as $key) {
                $query->orWhereRaw("JSON_CONTAINS({$foreignKey}, ?, ?)", [$key, $path]);
            }
        });
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @return array
     */
    protected function buildDictionary(Collection $results)
    {
        $foreign = $this->getForeignKeyName();

        return $results->mapToDictionary(function ($result) use ($foreign) {
            $path = $this->getPath();
            $path = match ($path) {
                '$' => null,
                default => str_replace('$.', '', $path)
            };

            $array = data_get($result->{$foreign}, $path);
            $ret = [];
            foreach ($array as $key) {
                $ret[$key] = $result;
            }
            return $ret;
        })->all();
    }
}
