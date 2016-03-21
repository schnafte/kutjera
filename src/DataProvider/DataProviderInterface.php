<?php
namespace Schnafte\Kutjera\DataProvider;

interface DataProviderInterface
{
    /**
     * @param string $resource
     * @return array
     */
    public function getCollection($resource);

    /**
     * @param string $resource
     * @param $id
     * @return array
     */
    public function getResource($resource, $id);

}