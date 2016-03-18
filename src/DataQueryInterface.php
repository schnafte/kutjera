<?php
namespace Kutjera;


/**
 * Class DataQuery
 * @package Kutjera
 */
interface DataQueryInterface
{
    /**
     * @return array
     */
    public function getFilters();

    /**
     * @return array
     */
    public function getSorting();
}