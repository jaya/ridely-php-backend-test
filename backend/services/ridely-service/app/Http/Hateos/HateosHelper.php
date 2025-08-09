<?php

namespace App\Http\Hateos;

use App\Models\Ride;
use Illuminate\Support\Collection;

class HateosHelper
{

    public static function appendHateosLinks(array $responseData, string $path, string $id)
    {
        $self = sprintf("%s/%s", $path, $id);
        $responseData['_links'] = new HateosItemLinks($self);

        return $responseData;
    }

    public static function appendRideHateosLinks(array $responseData, string $path, string $id)
    {
        $self = sprintf("%s/%s", $path, $id);
        $responseData['_links'] = new RideHateosItemLinks($self);

        return $responseData;
    }

    /**
     * @param $data
     * @param $path
     * @return array
     */
    public static function addHateosLinksToItems($data, $path): array
    {
        if ($data instanceof Collection) {
            $items = $data->toArray();
        } else if (!is_array($data)) {
            $items = [$data];
        } else {
            $items = $data;
        }

        $modifiedItems = [];
        foreach ($items as $model) {
            $instanceType = "";
            if (is_object($model)) {
                $instanceType = get_class($model);
                $model = $model->toArray();
            }

            $self = sprintf("%s/%s", $path, $model['id']);
            if ($instanceType === Ride::class) {
                $links = new RideHateosItemLinks($self);
            } else {
                $links = new HateosItemLinks($self);
            }
            $metadata = [
                '_links' => $links
            ];
            $modifiedItems[] = array_merge($model, $metadata);
        }
        return $modifiedItems;

    }
}