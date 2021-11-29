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
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\Constraint;
use Hyperf\Database\Model\Relations\HasMany;

class HasManyInJsonArray extends HasMany
{
    use HasJson;

    /**
     * Set the base constraints on the relation query.
     */
    public function addConstraints()
    {
        if (Constraint::isConstraint()) {
            [$key, $path] = $this->getPath($this->localKey);
            $json = $this->getJsonArrayFromModel($this->parent, $key);

            $this->query->whereIn($this->foreignKey, data_get($json, $path));

            $this->query->whereNotNull($this->foreignKey);
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     */
    public function addEagerConstraints(array $models)
    {
        $keys = [];
        foreach ($models as $model) {
            [$key, $path] = $this->getPath($this->localKey);

            $json = $this->getJsonArrayFromModel($model, $key);

            $keys = array_merge($keys, data_get($json, $path));
        }

        $this->query->whereIn($this->foreignKey, array_values(array_unique($keys)));
    }

    protected function matchOneOrMany(array $models, Collection $results, $relation, $type)
    {
        $dictionary = $this->buildDictionary($results);

        [$modelKey, $path] = $this->getPath($this->localKey);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            $json = $this->getJsonArrayFromModel($model, $modelKey);
            $json = data_get($json, $path);
            $value = [];
            foreach ($json as $key) {
                if (isset($dictionary[$key])) {
                    $value = array_merge($value, $dictionary[$key]);
                }
            }

            $model->setRelation(
                $relation,
                $type === 'one' ? reset($value) : $this->related->newCollection($value)
            );
        }

        return $models;
    }
}
