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

use Hao\ORMJsonRelation\HasORMJsonRelations;

/**
 * @property int $id
 * @property array $workers
 * @property array $data
 */
class JsonMain extends Model
{
    use HasORMJsonRelations;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'json_main';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'workers', 'data'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'data' => 'json', 'workers' => 'json'];

    public function workerModels()
    {
        return $this->hasManyInJsonArray(JsonWorker::class, 'id', 'workers');
    }

    public function workersInData()
    {
        return $this->hasManyInJsonArray(JsonWorker::class, 'id', 'data->worker_ids');
    }

    public function workerInData()
    {
        return $this->hasOneInJsonObject(JsonWorker::class, 'id', 'data->worker_id');
    }

    public function worker()
    {
        return $this->hasOne(JsonWorker::class, 'id', 'worker_id');
    }
}
