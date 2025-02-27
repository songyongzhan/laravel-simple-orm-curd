<?php

namespace Songyz\Simple\Orm\Core;

use Songyz\Simple\Orm\Library\DefaultPage;
use Songyz\Simple\Orm\Traits\SnakeCamelChange;

/**
 * Class DatabaseService
 * @package Songyz\Core
 * @author songyz <574482856@qq.com>
 * @date 2020/5/19 17:08
 */
abstract class DatabaseService
{
    use SnakeCamelChange;

    protected $primaryId = 'id';

    public function __construct()
    {

    }

    /**
     * 新增
     * add
     * @param array $data
     * @return string
     *
     * @author songyongzhan <574482856@qq.com>
     * @date 2021/1/18 09:32
     */
    public function add(array $data)
    {
        $data = $this->filterField($this->snake($data));
        $result = $this->getModel()->newQuery()->create($data);

        return $result ? $result->{$this->primaryId} : '';
    }

    /**
     * 更新模型
     * 如果 $attributes 不存在，就联合$attributes和$values 创建一个
     * 如果存在 就将符合$attributes的数据更新成$values
     * updateOrCreate
     * @param array $attributes
     * @param array $values
     *
     * @return
     * @author songyongzhan <574482856@qq.com>
     * @date 2021/1/19 08:20
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->getModel()->newQuery()->updateOrCreate($this->snake($attributes), $this->snake($values));
    }

    /**
     *
     * firstOrCreate
     * @param array $attributes
     * @param array $values
     * @return mixed
     * @url https://learnku.com/docs/laravel/6.x/eloquent/5176#b438f8
     * @author songyongzhan <574482856@qq.com>
     * @date 2021/1/19 08:27
     */
    public function firstOrCreate(array $attributes, array $values = [])
    {
        return $this->getModel()->newQuery()->firstOrCreate($this->snake($attributes), $this->snake($values));
    }

    /**
     * 获取单条记录
     * getOne
     * @param $where
     * @param array $fields
     * @param array $with
     * @param string $callback
     * @return array
     *
     * @author songyongzhan <574482856@qq.com>
     * @date 2021/1/18 09:32
     */
    public function getOne($where, array $fields = [], array $with = [], $callback = 'function')
    {
        $fields = $this->filterField($this->valueSnake($fields), '', false);
        $fields || $fields = ['*'];
        if (!is_array($where)) {
            $where = [get_where_condition($this->primaryId, $where)];
        }

        $data = $this->getModel()::getQuery($where)->with($with)->select($fields)->firstOrNew([])->toArray();

        if (is_callable($callback)) {
            $data = $callback($data);
        }

        return $data ?? [];
    }

    /**
     * 更新
     * update
     * @param $where
     * @param array $data
     * @return string
     *
     * @author songyongzhan <574482856@qq.com>
     * @date 2021/1/18 09:31
     */
    public function update($where, array $data)
    {
        if (!is_array($where)) {
            $where = [get_where_condition($this->primaryId, $where)];
        }

        $data = $this->filterField($this->snake($data));
        $records = $this->getModel()::getQuery($where)->get();

        foreach ($records as $key => $value) {
            $value->fill($data);
            $value->save();
        }

        return empty($records->toArray()) ? '0' : count($records->toArray());

    }

    /**
     * 删除
     * del
     * @param $where
     * @return string
     *
     * @author songyongzhan <574482856@qq.com>
     * @date 2021/1/18 09:31
     */
    public function del($where)
    {
        if (!is_array($where)) {
            $where = [get_where_condition($this->primaryId, $where)];
        }

        $affectedNum = $this->getModel()::getQuery($where)->delete();

        return $affectedNum ?? '0';
    }

    protected function filterField(array $data, $table = '', $keyMap = true)
    {
        return $this->getModel()->filterFiled($data, $table, $keyMap);
    }

    /**
     * 获取列表 带有分页
     * getList
     * @param array $where
     * @param array $fields
     * @param int $pageSize
     * @param int $pageNumber
     * @param string $order
     * @param array $with
     * @param string $callback 回调方法
     * @return DefaultPage
     * @throws \Songyz\Exceptions\PageValidaException
     * @author songyongzhan <574482856@qq.com>
     * @date 2020/05/18 09:27
     */
    public function getList(
        array $where,
        array $fields = [],
        int $pageSize = 10,
        int $pageNumber = 1,
        string $order = '',
        array $with = [],
        $callback = 'function'
    ) {

        $fields = $this->filterField($this->valueSnake($fields), '', false);
        $fields || $fields = ['*'];

        $model = $this->getModel();

        $query = $model::getQuery($where)->with($with);

        empty($order) && $order = $this->primaryId . ' desc';

        if (stripos($order, ',')) {
            $orderArr = explode(',', $order);
            foreach ($orderArr as $orderK => $orderVal) {
                if (stripos(trim($orderVal), ' ')) {
                    list($orderField, $orderType) = explode(' ', $orderVal);
                    $query->orderBy($orderField, $orderType);
                }
            }
        } else {
            list($orderField, $orderType) = explode(' ', $order);
            $query->orderBy($orderField, $orderType);
        }

        $result = $query->paginate($pageSize,
            $fields, 'pageNumber', $pageNumber);

        $listData = collect($result->items())->toArray();

        if (is_callable($callback)) {
            $listData = $callback($listData);
        }
        $defaultPage = config('songyz_scaffold.default_page');

        if (empty($defaultPage)) {
            $defaultPage = DefaultPage::class;
        }
        return new $defaultPage($listData, $result->total(), $pageNumber, $pageSize);
    }

    /**
     * 获取所有的记录
     * getListAll
     * @param array $where
     * @param array $fields
     * @param string $order
     * @param array $with
     * @param string $callback
     * @return mixed
     *
     * @author songyongzhan <574482856@qq.com>
     * @date 2020/05/18 09:27
     */
    public function getListAll(
        array $where,
        array $fields = [],
        string $order = '',
        array $with = [],
        $callback = 'function'
    ) {
        empty($order) && $order = $this->primaryId . ' desc';
        list($orderField, $orderType) = explode(' ', $order);

        $fields = $this->filterField($this->valueSnake($fields), '', false);
        $fields || $fields = ['*'];

        $model = $this->getModel();
        $listData = $model::getQuery($where)->with($with)->select($fields)->orderBy($orderField,
            $orderType)->get()->toArray();

        if (is_callable($callback)) {
            $listData = $callback($listData);
        }

        return $listData;
    }

    /**
     *
     * getModel
     * @return Model
     *
     */
    public abstract function getModel(): Model;

}
