<?php

namespace Songyz\Core;

use Songyz\Library\DefaultPage;
use Songyz\Traits\SnakeCamelChange;

abstract class DatabaseService
{
    use SnakeCamelChange;

    protected $primaryId = 'id';

    public function __construct()
    {

    }

    public function add(array $data)
    {
        $data = $this->filterField($this->snake($data));
        $result = $this->getModel()->newQuery()->create($data);

        return $result ? $result->{$this->primaryId} : '';
    }

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

    public function update($where, array $data)
    {
        if (!is_array($where)) {
            $where = [get_where_condition($this->primaryId, $where)];
        }

        $data = $this->filterField($this->snake($data));
        $result = $this->getModel()::getQuery($where)->update($data);

        return $result ?? '0';
    }

    public function del(int $id)
    {
        if ($this->getModel()->isRealDelete) {
            $affectedNum = $this->getModel()->newQuery()->where($this->primaryId, '=', $id)->delete();
        } else {
            $affectedNum = $this->getModel()->newQuery()->where($this->primaryId, '=',
                $id)->update([$this->getModel()->isDeleted => '1']);
        }

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
        empty($order) && $order = 'gmt_created desc';
        list($orderField, $orderType) = explode(' ', $order);

        $fields = $this->filterField($this->valueSnake($fields), '', false);
        $fields || $fields = ['*'];

        $model = $this->getModel();
        $result = $model::getQuery($where)->with($with)->orderBy($orderField, $orderType)->paginate($pageSize,
            $fields, 'pageNumber', $pageNumber);

        $listData = collect($result->items())->toArray();

        if (is_callable($callback)) {
            $listData = $callback($listData);
        }

        return new DefaultPage($listData, $result->total(), $pageNumber, $pageSize);
    }

    /**
     *
     * getListAll
     * @param array $where
     * @param array $fields
     * @param string $order
     * @param array $with
     * @param string $callback 回调方法
     * @return array
     */
    public function getListAll(
        array $where,
        array $fields = [],
        string $order = '',
        array $with = [],
        $callback = 'function'
    ) {
        empty($order) && $order = 'gmt_created desc';
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
