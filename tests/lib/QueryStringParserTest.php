<?php
namespace Kutjera\Tests;

use InvalidArgumentException;
use Kutjera\DataQueryInterface;
use Kutjera\Entity\ParsedQuery;
use Kutjera\QueryStringParser;
use PHPUnit_Framework_TestCase;

class QueryStringParserTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $parser = new QueryStringParser();

        $this->assertEquals('', $parser->getQueryString());
        $this->assertEquals('filter', $parser->getFiltersKey());
        $this->assertEquals('sort', $parser->getSortingKey());
    }


    public function testParseReturnsDataQueryInstance()
    {
        $parser = new QueryStringParser();
        $dataQuery = $parser->parse();

        $this->assertTrue($dataQuery instanceof DataQueryInterface);
    }


    /**
     * @dataProvider testParseProvider
     */
    public function testParse($queryString, $filtersKey, $sortingKey, $expected)
    {
        $parser = new QueryStringParser($queryString, $filtersKey, $sortingKey);

        if ($expected['type'] == 'exception') {
            $this->expectException($expected['exception']);
            $parser->parse();

        } else {
            $dataQuery = $parser->parse();

            $resultFilters = $dataQuery->getFilters();
            $resultSorting = $dataQuery->getSorting();

            $this->assertEquals(sizeof($resultFilters), sizeof($expected['filters']), 'numberOfFilterRules');
            $this->assertEquals(sizeof($resultSorting), sizeof($expected['sorting']), 'numberOfSortingRules');

            for($i=0; $i<sizeof($resultFilters); $i++) {
                $this->assertEquals($resultFilters[$i]->getField(), $expected['filters'][$i][0]);
                $this->assertEquals($resultFilters[$i]->getOperator(), $expected['filters'][$i][1]);
                $this->assertEquals($resultFilters[$i]->getValue(), $expected['filters'][$i][2]);
            }

            for($i=0; $i<sizeof($resultSorting); $i++) {
                $this->assertEquals($resultSorting[$i]->getField(), $expected['sorting'][$i][0]);
                $this->assertEquals($resultSorting[$i]->getOrder(), $expected['sorting'][$i][1]);
            }

        }
    }


    /**
     * @dataProvider invalidQueryStringTypeProvider
     * @expectedException InvalidArgumentException
     */
    public function testInvalidQueryStringType($queryString)
    {
        $parser = new QueryStringParser($queryString);
    }


    /**
     * @dataProvider invalidFilterKeysProvider
     * @expectedException InvalidArgumentException
     */
    public function testInvalidFiltersKeyType($filtersKey)
    {
        $parser = new QueryStringParser('', $filtersKey);
    }

    /**
     * @dataProvider invalidSortingKeysProvider
     * @expectedException InvalidArgumentException
     */
    public function testInvalidSortingKeyType($sortingKey)
    {
        $parser = new QueryStringParser('', 'filter', $sortingKey);
    }


    /*******************************************************************************************************************
     * DATA PROVIDERS
     */


    public function invalidQueryStringTypeProvider()
    {
        return array(
            'is_integer'    => [1],
            'is_null'       => [null],
            'is_array'      => [[]],
        );
    }

    public function invalidFilterKeysProvider()
    {
        return array(
            'is_integer'    => [1],
            'is_null'       => [null],
            'is_array'      => [[]],
            'is_empty'      => ['']
        );
    }


    public function invalidSortingKeysProvider()
    {
        return array(
            'is_integer'    => [1],
            'is_null'       => [null],
            'is_array'      => [[]],
            'is_empty'      => ['']
        );
    }


    public function testParseProvider()
    {
        return array(
            'empty_query_string' => [
                '',
                'filter',
                'sort',
                ['type'=>'result','filters'=>[], 'sorting'=>[]]
            ],
            'non_matching_filter_key_1' => [
                'foo=bar',
                'filter',
                'sort',
                ['type'=>'result','filters'=>[], 'sorting'=>[]]
            ],
            'non_matching_filter_key_2' => [
                'filter=bar',
                'foo',
                'sort',
                ['type'=>'result','filters'=>[], 'sorting'=>[]]
            ],
            'invalid_filter_query_string' => [
                'filter=foobar',
                'filter',
                'sort',
                ['type'=>'exception','exception'=>\InvalidArgumentException::class]
            ],
            'valid_1' => [
                'filter=foo=bar',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'eq', 'bar']],
                    'sorting'=>[]
                ]
            ],
            'valid_2' => [
                'filter=foo=bar,lorem=ipsum',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'eq', 'bar'],['lorem', 'eq', 'ipsum']],
                    'sorting'=>[]
                ]
            ],
            'valid_3' => [
                'filter=foo=bar,lorem=ipsum&sort=title',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'eq', 'bar'],['lorem', 'eq', 'ipsum']],
                    'sorting'=>[['title','ASC']]
                ]
            ],
            'valid_4' => [
                'filter=foo=bar,lorem=ipsum&sort=-title',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'eq', 'bar'],['lorem', 'eq', 'ipsum']],
                    'sorting'=>[['title','DESC']]
                ]
            ],
            'valid_5' => [
                'filter=a.nested.field=gte:1,another.d.e.e.p.l.y.nested.field=lke:ipsum&sort=-title,another_sort_field',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['a.nested.field', 'gte', '1'], ['another.d.e.e.p.l.y.nested.field', 'lke', 'ipsum']],
                    'sorting'=>[['title','DESC'], ['another_sort_field','ASC']]
                ]
            ],
            'operator_default' => [
                'filter=foo=bar',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'eq', 'bar']],
                    'sorting'=>[]
                ]
            ],
            'operator_eq' => [
                'filter=foo=eq:bar',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'eq', 'bar']],
                    'sorting'=>[]
                ]
            ],
            'operator_neq' => [
                'filter=foo=neq:bar',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'neq', 'bar']],
                    'sorting'=>[]
                ]
            ],
            'operator_gt' => [
                'filter=foo=gt:bar',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'gt', 'bar']],
                    'sorting'=>[]
                ]
            ],
            'operator_gte' => [
                'filter=foo=gte:bar',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'gte', 'bar']],
                    'sorting'=>[]
                ]
            ],
            'operator_lt' => [
                'filter=foo=lt:bar',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'lt', 'bar']],
                    'sorting'=>[]
                ]
            ],
            'operator_lte' => [
                'filter=foo=lte:bar',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'lte', 'bar']],
                    'sorting'=>[]
                ]
            ],
            'operator_cts' => [
                'filter=foo=cts:bar',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'cts', 'bar']],
                    'sorting'=>[]
                ]
            ],
            'operator_any' => [
                'filter=foo=any:bar',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'any', 'bar']],
                    'sorting'=>[]
                ]
            ],
            'operator_lke' => [
                'filter=foo=lke:bar',
                'filter',
                'sort',
                [
                    'type'=>'result',
                    'filters'=>[['foo', 'lke', 'bar']],
                    'sorting'=>[]
                ]
            ],
            'invalid_operator' => [
                'filter=foo=invalid_op:bar',
                'filter',
                'sort',
                ['type'=>'exception','exception'=>\InvalidArgumentException::class]
            ],
            'missing_value' => [
                'filter=foo=eq:',
                'filter',
                'sort',
                ['type'=>'exception','exception'=>\InvalidArgumentException::class]
            ],
            'missing_operator' => [
                'filter=foo=:bar',
                'filter',
                'sort',
                ['type'=>'exception','exception'=>\InvalidArgumentException::class]
            ],
            'missing_value_and_operator' => [
                'filter=foo=:',
                'filter',
                'sort',
                ['type'=>'exception','exception'=>\InvalidArgumentException::class]
            ]
        );
    }
}