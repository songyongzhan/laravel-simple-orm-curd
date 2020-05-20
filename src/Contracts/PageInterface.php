<?php

namespace Songyz\Simple\Orm\Contracts;

/**
 * Interface PageInterface
 * @package Songyz\Contracts
 * @author songyz <574482856@qq.com>
 * @date 2020/05/2020/5/19 16:28
 */
interface PageInterface
{
    function __construct(array $results, int $totalCount, int $pageNumber = 1, int $pageSize = 10);

    /**
     * 获取页号
     * @return int 页号
     */
    function getPageNumber(): int;

    /**
     * 获取每页可显示的记录数
     * @return int 每页可显示的记录数
     */
    function getPageSize(): int;

    /**
     * 获取总记录数
     * @return int 总记录数
     */
    function getTotalCount(): int;

    /**
     * 获取数据列表
     * @return array 数据列表
     */
    function getResults(): array;

    /**
     * 获取总页数
     * @return int 总页数
     */
    function getTotalPages(): int;

}
