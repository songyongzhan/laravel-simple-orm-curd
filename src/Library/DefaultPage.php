<?php

namespace Songyz\Library;

use Songyz\Contracts\PageInterface;
use Songyz\Exceptions\PageValidaException;

class DefaultPage implements PageInterface
{
    /**
     * 第几页
     */
    public $pageNumber;
    /**
     * 每页多少条记录数
     */
    public $pageSize;
    /**
     * 总页
     */
    public $totalPages;
    /**
     * 总记录数
     */
    public $totalCount;

    /**
     * 结果集合
     */
    public $results;

    function __construct(array $results, int $totalCount, int $pageNumber = 1, int $pageSize = 10)
    {
        if ($pageNumber < 1) {
            throw new PageValidaException("当前页不能小于1");
        }
        if ($pageSize < 1) {
            throw new PageValidaException("单页记录不能小于1");
        }

        $this->pageNumber = $pageNumber;
        $this->pageSize = $pageSize;
        $this->totalCount = $totalCount;
        $this->totalPages = $pageSize != 0 && $totalCount != 0 ?
            ceil($totalCount / $pageSize) : 1;
        $this->results = $results ?? [];
    }

    /**
     * 获取页号
     * @return int 页号
     */
    function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    /**
     * 获取每页可显示的记录数
     * @return int 每页可显示的记录数
     */
    function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * 获取总记录数
     * @return int 总记录数
     */
    function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * 获取数据列表
     * @return array 数据列表
     */
    function getResults(): array
    {
        return $this->results;
    }

    /**
     * 获取总页数
     * @return int 总页数
     */
    function getTotalPages(): int
    {
        return $this->totalPages;
    }


}
