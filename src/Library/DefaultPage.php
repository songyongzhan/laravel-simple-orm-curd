<?php

namespace Songyz\Simple\Orm\Library;

use Songyz\Simple\Orm\Contracts\PageInterface;
use Songyz\Simple\Orm\Exceptions\PageValidaException;

/**
 * Class DefaultPage
 * @package Songyz\Library
 * @author songyz <574482856@qq.com>
 * @date 2020/5/19 16:30\
 */
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

    /**
     * @param int $pageNumber
     */
    public function setPageNumber(int $pageNumber): void
    {
        $this->pageNumber = $pageNumber;
    }

    /**
     * @param int $pageSize
     */
    public function setPageSize(int $pageSize): void
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @param false|float|int $totalPages
     */
    public function setTotalPages($totalPages): void
    {
        $this->totalPages = $totalPages;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount(int $totalCount): void
    {
        $this->totalCount = $totalCount;
    }

    /**
     * @param array $results
     */
    public function setResults(array $results): void
    {
        $this->results = $results;
    }

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
