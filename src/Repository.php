<?php
namespace Kutjera;

use Kutjera\DataProvider\DataProviderInterface;

/**
 *
 */
class Repository
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;


    /**
     * Repository constructor.
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(DataProviderInterface $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }


    /**
     * @param string $resource
     * @param DataQueryInterface $query
     * @return array
     */
    public function query($resource, DataQueryInterface $query)
    {
        $rs = $this->dataProvider->getCollection($resource);

        return $this->filterAndSort($rs, $query);
    }


    /**
     * @param $resource
     * @param $id
     * @return array
     */
    public function byId($resource, $id)
    {
        $rs = $this->dataProvider->getResource($resource, $id);

        return $rs;
    }


    /**
     * @param array $rs
     * @param DataQueryInterface $query
     * @return array
     */
    protected function filterAndSort(array $rs, DataQueryInterface $query)
    {
        $processed = $rs;

        return $processed;
    }
}