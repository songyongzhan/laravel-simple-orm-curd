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
     *
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
     *
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
        $result = $this->getModel()::getQuery($where)->update($data);

        return $result ?? '0';
    }

    /**
     * 删除
     * del
     * @param int $id
     * @return string
     *
     * @author songyongzhan <574482856@qq.com>
     * @date 2021/1/18 09:31
     */
    public function del(int $id)
    {
        $affectedNum = $this->getModel()->newQuery()->where($this->primaryId, '=', $id)->delete();

        return $affectedNum ?? '0';
    }

    protected function filterField(array $data, $table = '', $keyMap = true)
    {
        return $this->getModel()->filterFiled($data, $table, $keyMap);
    }

    /**
     *
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
                    list($orderField, $orderType) = explode(' ', $order);
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
     *
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
