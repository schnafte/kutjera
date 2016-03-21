<?php
namespace Kutjera;


/**
 * Class DataQuery
 * @package Kutjera
 */
interface DataQueryInterface
{
    /**
     * @return FilterRule[]
     */
    public function getFilters();

    /**
     * @return SortingRule[]
     */
    public function getSorting();

    /**
     * @return array
     */
    public function getFields();

    /**
     * @return LimitRule
     */
    public function getLimit();
}