<?php
namespace Schnafte\Kutjera;


/**
 * Class DataQuery
 * @package Kutjera
 */
interface DataQueryInterface
{
    /**
     * @return string
     */
    public function getQueryString();

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