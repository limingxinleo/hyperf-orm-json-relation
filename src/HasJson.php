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

use Hyperf\Utils\Codec\Json;

trait HasJson
{
    public function getJsonData(array $json, string $path)
    {
        return data_get($json, $path);
    }

    public function getPath(string $foreign): array
    {
        $data = explode('->', $foreign);

        $attribute = array_shift($data);

        if (empty($data)) {
            return [$attribute, null];
        }

        return [$attribute, implode('.', $data)];
    }

    public function getJsonArrayFromModel($model, $key): array
    {
        $json = $model->getAttribute($key);
        if (! is_array($json)) {
            $json = Json::decode((string) $json);
        }

        return $json;
    }
}
