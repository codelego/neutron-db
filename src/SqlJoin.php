<?php

namespace Phpfox\Db;

/**
 * Class SqlJoin
 *
 * @package Phpfox\Db
 */
class SqlJoin
{

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var array
     */
    protected $elements = [];

    /**
     * SqlJoin constructor.
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param $type
     * @param $table
     * @param $alias
     * @param $expression
     * @param $value
     *
     * @return $this
     */
    public function join($type, $table, $alias, $expression, $value)
    {

        if (is_string($table) && substr($table, 0, 1) == ':') {
            $table = \app()->db()->getPrefix() . substr($table, 1);
        }

        $condition = null;

        if (is_null($value)) {
            $condition = $expression;
        } else {
            if (is_array($value)) {
                $condition = strtr($expression, $this->quoteArray($value));
            } else {
                $condition = str_replace('?', $this->adapter->quoteValue($value),
                    $expression);
            }
        }

        $this->elements[] = $type . ' ' . $table . ' AS ' . $alias . ' ON ('
            . $condition . ') ';

        return $this;
    }

    /**
     * @param array $values
     *
     * @return array
     */
    protected function quoteArray(array $values)
    {
        $result = [];

        foreach ($values as $k => $v) {
            $result[$k] = $this->adapter->quoteValue($v);
        }

        return $result;

    }

    /**
     * @return string
     */
    public function prepare()
    {
        return implode(' ', $this->elements);
    }
}