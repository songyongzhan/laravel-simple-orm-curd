<?php

namespace Songyz\Simple\Orm\Core;

/**
 * 本地业务层
 * Class Manager
 * @package App\Core
 * @author songyz <574482856@qq.com>
 *
 */
abstract class DatabaseManager
{
    public function __construct()
    {

    }

    protected function setService(DatabaseService $service): DatabaseService
    {
        $this->service = $service;
    }

    public function del($id)
    {
        $affectedNum = $this->getService()->del($id);
        return $affectedNum ? ['id' => $id] : [];
    }

    public function add(array $data)
    {
        $id = $this->getService()->add($data);

        return ['id' => $id];
    }

    public function update($id, array $data)
    {
        $result = $this->getService()->update($id, $data);

        return $result ? ['id' => $id] : [];
    }

    public function getList(array $where, array $fields = [], $pageSize = 10, $pageNumber = 1, $order = '')
    {
        return $this->getService()->getList($where, $fields, $pageSize, $pageNumber, $order);
    }

    public function getOne($where, array $fields = [])
    {
        return $this->getService()->getOne($where, $fields);
    }

    public abstract function getService(): DatabaseService;

}
