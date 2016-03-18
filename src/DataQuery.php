<?php
namespace Kutjera;


/**
 * Class DataQuery
 * @package Kutjera
 */
class DataQuery implements DataQueryInterface
{
    /**
     * @var FilterRule[]
     */
    private $filters;

    /**
     * @var SortingRule[]
     */
    private $sorting;



    public function __construct(array $filters, array $sorting)
    {
        $this->filters = $filters;
        $this->sorting = $sorting;
    }


    /*******************************************************************************************************************
     * PUBLIC
     */


    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return array
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * @return array
     */
    public function getFiltersAsArray()
    {
        $filters = [];

        foreach ($this->filters as $row) {
            $filters[]= [
                "field" => $row->getField(),
                "operator" => $row->getOperator(),
                "value" => $row->getValue()
            ];
        }

        return $filters;
    }

    /**
     * @return array
     */
    public function getSortingAsArray()
    {
        $sorting = [];

        foreach ($this->sorting as $row) {
            $sorting[]= [
                "field" => $row->getField(),
                "order" => $row->getOrder()
            ];
        }

        return $sorting;
    }
}