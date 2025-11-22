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
            'field'     => trim($field),
            'val'       => $val,
            'operator'  => $operator,
            'condition' => $condition,
        ];
    }
}


if (!function_exists('get_where_in_condition')) {
    /**
     *
     * get_where_in_condition
     * @param $field
     * @param $val
     * @param string $condition
     * @return array
     *
     * @date 2021/12/28 15:35
     */
    function get_where_in_condition($field, $val, $condition = 'AND')
    {
        return [
            'field'     => trim($field),
            'val'       => $val,
            'operator'  => '=',
            'condition' => $condition,
            'command'   => 'whereIn',
        ];
    }
}


if (!function_exists('get_where_between_condition')) {
    /**
     *
     * get_where_between_condition
     * @param $field
     * @param array $val
     * @return array
     *
     * @date 2021/12/28 15:36
     */
    function get_where_between_condition($field, array $val)
    {
        return [
            'field'   => trim($field),
            'val'     => $val,
            'command' => 'whereBetween',
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
            'code'    => $code,
            'message' => $message,
            'data'    => $data
        ];
    }
}


if (!function_exists('is_date')) {
    /**
     *
     * is_date
     * @param $str
     *
     * @return bool
     * @author songyongzhan <574482856@qq.com>
     * @date 2025/11/22 14:45
     */
    function is_date($str)
    {
        // Check if the string matches YYYY-MM-DD HH:MM:SS format
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $str)) {
            $timestamp = strtotime($str);
            return $timestamp !== false && date('Y-m-d H:i:s', $timestamp) === $str;
        }

        // Check if the string matches YYYY-MM-DD format
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $str)) {
            $timestamp = strtotime($str);
            return $timestamp !== false && date('Y-m-d', $timestamp) === $str;
        }

        // If none of the formats match, return false
        return false;
    }

}


