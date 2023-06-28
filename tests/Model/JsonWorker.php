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
 * @property string $name
 */
class JsonWorker extends Model
{
    use HasORMJsonRelations;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'json_worker';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['id', 'name'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['id' => 'integer'];

    public function mains()
    {
        return $this->hasManyJsonContains(JsonMain::class, 'workers', 'id');
    }

    public function mainsInData()
    {
        return $this->hasManyJsonContains(JsonMain::class, 'data->worker_ids', 'id');
    }
}
