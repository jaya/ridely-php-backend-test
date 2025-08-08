<?php

namespace App\Http\Hateos;

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
        foreach ($items as $driver) {

            if (is_object($driver)) {
                $driver = $driver->toArray();
            }

            $self = sprintf("%s/%s", $path, $driver['id']);
            $metadata = [
                '_links' => new HateosItemLinks($self)
            ];
            $modifiedItems[] = array_merge($driver, $metadata);
        }
        return $modifiedItems;

    }
}