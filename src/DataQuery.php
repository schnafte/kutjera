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

    /**
     * @var array
     */
    private $fields;

    /**
     * @var LimitRule
     */
    private $limit;



    public function __construct(array $filters, array $sorting, array $fields, LimitRule $limit = null)
    {
        $this->filters = $filters;
        $this->sorting = $sorting;
        $this->fields = $fields;
        $this->limit = $limit;
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
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return LimitRule
     */
    public function getLimit()
    {
        return $this->limit;
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


    /**
     * @return array
     */
    public function getFieldsAsArray()
    {
        return $this->fields;
    }


    /**
     * @return array
     */
    public function getLimitAsArray()
    {
        return [
            "count" => $this->limit->getCount(),
            "offset" => $this->limit->getOffset()
        ];
    }
}