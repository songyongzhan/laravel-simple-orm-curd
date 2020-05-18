<?php

if (!function_exists('get_where_condition')) {
    /**
     * get_where_condition
     * @param $field
     * @param $val
     * @param string $operator
     * @param string $condition
     * @return array
     */
    function get_where_condition($field, $val, $operator = '=', $condition = 'AND')
    {
        return [
            'field' => trim($field),
            'val' => $val,
            'operator' => $operator,
            'condition' => $condition,
        ];
    }
}
