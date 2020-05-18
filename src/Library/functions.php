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

if (!function_exists('get_return_json')) {
    /**
     * get_return_json
     * @param $data
     * @param string $code
     * @param string $message
     * @return array
     */
    function get_return_json($data, $code = '0', string $message = '成功')
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }
}


