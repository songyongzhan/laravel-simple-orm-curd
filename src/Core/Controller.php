<?php

namespace Songyz\Simple\Orm\Core;

use Songyz\Simple\Orm\Traits\SnakeCamelChange;

/**
 * Class Controller
 * @package Songyz\Core
 * @author songyz <574482856@qq.com>
 * @date 2020/5/19 16:30
 */
class Controller extends \App\Http\Controllers\Controller
{

    use SnakeCamelChange;

    public function __construct()
    {

    }

    /**
     * 返回json串
     * showJson
     * @param $data
     * @param string $code
     * @param string $message
     * @param int $httpStatus
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function showJson(
        $data,
        string $code = '0',
        string $message = '成功',
        $httpStatus = 200,
        $headers = []
    ) {
        $result = get_return_json($data, $code, $message);
        return response()->json($result, $httpStatus, $headers, JSON_UNESCAPED_UNICODE);
    }

    protected function input($name = '', $default = '')
    {
        if (empty($name)) {
            return app('request')->all();
        }

        return app('request')->input($name, $default);
    }

    /**
     * 过滤空值
     * filterData
     * @param $data
     * @return array
     *
     */
    protected function filterData($data)
    {
        return array_filter($data, [$this, 'filter_empty_callback']);
    }

    /**
     * 根据规则获取对应的搜索条件
     * @param $rules
     * @param array $data
     * @return array|bool|mixed
     */
    public function where($rules, $data = [])
    {
        if (!is_array($rules) || !$data) {
            return [];
        }

        $where = [];

        foreach ($rules as $key => $val) {
            $condition_type = $val['condition'];
            switch ($condition_type) {
                case 'in':
                    $where = $this->in_condition($val, $where, 'in', $data);
                    break;
                case 'not_in':
                    $where = $this->in_condition($val, $where, 'not in', $data);
                    break;
                case 'like':
                    $where = $this->like_condition($val, $where, 'like', $data);
                    break;
                case 'after_like':
                    $where = $this->like_condition($val, $where, 'after_like', $data);
                    break;
                case 'before_like':
                    $where = $this->like_condition($val, $where, 'before_like', $data);
                    break;
                case 'between':
                    if (count($val['key_field']) < 2) {
                        break;
                    }
                    //就循环2次
                    for ($f_key = 0; $f_key < 2; $f_key++) {
                        $f_filed = $val['key_field'][$f_key];
                        if (!isset($data[$f_filed])) {
                            break;
                        }
                        $key_value = isset($data[$f_filed]) ? $data[$f_filed] : '';
                        if (isset($val['db_field'][$f_key])) {
                            $dbFields = $val['db_field'][$f_key];
                            $condition = $f_key == 0 ? '>=' : '<=';
                            $where[] = [
                                'field' => trim($dbFields),
                                'operator' => $condition,
                                'val' => trim($key_value)
                            ];
                        }
                    }
                    break;
                default :
                    foreach ($val['key_field'] as $f_key => $f_filed) {
                        if (!isset($data[$f_filed])) {
                            continue;
                        }
                        $where[] = [
                            'field' => trim($val['db_field'][$f_key]),
                            'operator' => $condition_type,
                            'val' => isset($data[$f_filed]) ? trim($data[$f_filed]) : ''
                        ];
                    }
                    break;
            }
        }
        return $where;
    }

    /**
     * 处理like 或 not like
     * like_condition
     * @param $val
     * @param $where 条件
     * @param $condition_type 处理类型
     * @param $data
     * @return array
     * @author songyz <574482856@qq.com>
     * @date 2020/1/14 16:21
     */
    private function like_condition($val, $where, $condition_type, $data)
    {
        foreach ($val['key_field'] as $f_key => $f_filed) {
            if (!isset($data[$f_filed])) {
                continue;
            }
            $key_value = isset($data[$f_filed]) ? $data[$f_filed] : '';
            if (isset($val['db_field'][$f_key])) {
                $dbFields = $val['db_field'][$f_key];
                switch ($condition_type) {
                    case 'like':
                        $where[] = [
                            'field' => $dbFields,
                            'operator' => 'like',
                            'val' => '%' . trim($key_value) . '%',
                            'condition' => 'AND'
                        ];
                        break;
                    case 'after_like':
                        $where[] = [
                            'field' => $dbFields,
                            'operator' => 'like',
                            'val' => trim($key_value) . '%',
                            'condition' => 'AND'
                        ];

                        break;
                    case 'before_like':
                        $where[] = [
                            'field' => $dbFields,
                            'operator' => 'like',
                            'val' => '%' . trim($key_value),
                            'condition' => 'AND'
                        ];
                        break;
                }
            }
        }
        return $where;
    }

    /**
     * 处理in 或not in
     * in_condition
     * @param $val
     * @param $where
     * @param $condition_type
     * @param $data
     * @return array
     * @author songyz <574482856@qq.com>
     * @date 2020/1/14 16:20
     */
    private function in_condition($val, $where, $condition_type, $data)
    {

        isset($val['db_field'][0]) && $dbFields = $val['db_field'][0];

        if (!$dbFields || !isset($data[$dbFields])) {
            return $where;
        }

        if (is_string($data[$dbFields])) {
            $data[$dbFields] = explode(',', $data[$dbFields]);
        }

        $where[] = [
            'field' => $dbFields,
            'operator' => $condition_type,
            'val' => $data[$dbFields],
            'condition' => 'AND'
        ];

        return $where;
    }

    private function filter_empty_callback($val)
    {
        if (is_array($val) && count($val) > 0) {
            return true;
        }

        if (is_array($val) && count($val) == 0) {
            return [];
        }

        if (strlen(trim($val)) > 0 && !is_null($val)) {
            return true;
        }
    }

}
