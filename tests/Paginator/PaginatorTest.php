<?php

namespace Paginator;

use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    public function testIsFirstPage()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);

        $this->assertTrue($paginator->isOnFirstPage());

        $paginator->setCurrentPage(2);
        $this->assertFalse($paginator->isOnFirstPage());

        $paginator->setCurrentPage(234242525256);
        $this->assertFalse($paginator->isOnFirstPage());
    }

    public function testIsLastPage()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);

        $this->assertFalse($paginator->isOnLastPage());

        // at the moment per page is 10
        $paginator->setTotalItems(9);
        $this->assertTrue($paginator->isOnFirstPage());
        $this->assertTrue($paginator->isOnLastPage());

        // current page number 2, doesn't exist
        $paginator->setCurrentPage(2);
        $this->assertFalse($paginator->isOnFirstPage());
        $this->assertFalse($paginator->isOnLastPage());

        $paginator->setTotalItems(19);
        $paginator->setCurrentPage(2);
        $this->assertFalse($paginator->isOnFirstPage());
        $this->assertTrue($paginator->isOnLastPage());

        $paginator->setPerPage(5);
        $this->assertFalse($paginator->isOnFirstPage());
        $this->assertFalse($paginator->isOnLastPage());

        // still should be on the second page
        $this->assertEquals(2, $paginator->getCurrentPage()->getNumber());
    }

    public function testGetNextPage()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems);

        $this->assertFalse($paginator->getNextPage());

        $paginator->setPerPage(1);
        $this->assertEquals(2, $paginator->getNextPage()->getNumber());

        $paginator->setCurrentPage(2);
        $this->assertEquals(3, $paginator->getNextPage()->getNumber());

        $paginator->setCurrentPage(3);
        $this->assertEquals(false, $paginator->getNextPage());

        $paginator->setTotalItems(4);
        $this->assertEquals(4, $paginator->getNextPage()->getNumber());

        $paginator->setCurrentPage(5);
        $this->assertFalse($paginator->getPreviousPage());
    }

    public function testGetPreviousPage()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems);

        $this->assertFalse($paginator->getPreviousPage());

        $paginator->setPerPage(1);
        $this->assertFalse($paginator->getPreviousPage());

        $paginator->setCurrentPage(2);
        $this->assertEquals(1, $paginator->getPreviousPage()->getNumber());

        $paginator->setCurrentPage(5);
        $this->assertFalse($paginator->getPreviousPage());
    }

    public function testGetNextPageUrl()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems, 1);
        $paginator->setUrl('/product/category');

        $this->assertEquals('/product/category?page=2', $paginator->getNextPageUrl());

        $paginator->setUrl('');
        $this->assertEquals('/?page=2', $paginator->getNextPageUrl());

        $paginator->setCurrentPage(2);
        $paginator->setUrl('/product/category');

        $this->assertEquals('/product/category?page=3', $paginator->getNextPageUrl());

        // the old query string should be updated with the new one
        $paginator->setUrl('/product/category?page=1');
        $this->assertEquals('/product/category?page=3', $paginator->getNextPageUrl());

        $paginator->setUrl('/product/category?page=1&sortby=date&sortdir=asc');
        $this->assertEquals(
            '/product/category?page=3&sortby=date&sortdir=asc',
            $paginator->getNextPageUrl()
        );

        $paginator->setUrl('/product/category?sortby=date&sortdir=asc&page=1');
        $this->assertEquals(
            '/product/category?sortby=date&sortdir=asc&page=3',
            $paginator->getNextPageUrl()
        );

        $paginator->setUrl('https://example.com/product/category?page=1&sortby=date&sortdir=asc');
        $this->assertEquals(
            'https://example.com/product/category?page=3&sortby=date&sortdir=asc',
            $paginator->getNextPageUrl()
        );

        $paginator->setPageName('p');
        $paginator->setUrl('/product/category');
        $this->assertEquals('/product/category?p=3', $paginator->getNextPageUrl());

        $paginator->setCurrentPage(3);
        $paginator->setUrl('/product/category');
        $this->assertEquals(false, $paginator->getNextPageUrl());
    }

    public function testGetPreviousPageUrl()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems, 1);

        $paginator->setUrl('/product/category');
        $this->assertEquals(false, $paginator->getPreviousPageUrl());

        $paginator->setCurrentPage(2);
        $paginator->setUrl('/product/category');
        $this->assertEquals('/product/category?page=1', $paginator->getPreviousPageUrl());

        // the old query string should be updated with the new one
        $paginator->setUrl('/product/category?page=2');
        $this->assertEquals('/product/category?page=1', $paginator->getPreviousPageUrl());

        $paginator->setUrl('/product/category?page=3&sortby=date&sortdir=asc');
        $this->assertEquals(
            '/product/category?page=1&sortby=date&sortdir=asc',
            $paginator->getPreviousPageUrl()
        );

        $paginator->setUrl('/product/category?sortby=date&sortdir=asc&page=3');
        $this->assertEquals(
            '/product/category?sortby=date&sortdir=asc&page=1',
            $paginator->getPreviousPageUrl()
        );

        $paginator->setUrl('https://example.com/product/category?page=3&sortby=date&sortdir=asc');
        $this->assertEquals(
            'https://example.com/product/category?page=1&sortby=date&sortdir=asc',
            $paginator->getPreviousPageUrl()
        );

        $paginator->setUrl('/product/category');
        $paginator->setPageName('p');
        $this->assertEquals('/product/category?p=1', $paginator->getPreviousPageUrl());

        $paginator->setUrl('/product/category');
        $paginator->setCurrentPage(1);
        $this->assertEquals(false, $paginator->getPreviousPageUrl());
    }

    public function testGetPageName()
    {
        $totalItems = 3;
        $paginator = new Paginator($totalItems);

        // page name is not set, so default gets returned
        $this->assertEquals(Paginator::DEFAULT_PAGE_NAME, $paginator->getPageName());

        $paginator->setPageName('p');
        $this->assertEquals('p', $paginator->getPageName());

        $paginator->setPageName('');

        // page name is set to none, default gets returned
        $this->assertEquals(Paginator::DEFAULT_PAGE_NAME, $paginator->getPageName());
    }

    public function testCalculateDatabaseOffset()
    {
        $totalItems = 10;
        $paginator = new Paginator($totalItems, 10);

        $this->assertEquals(0, $paginator->calculateDatabaseOffset(1));
        $this->assertEquals(10, $paginator->calculateDatabaseOffset(2));

        $paginator->setPerPage(5);
        $this->assertEquals(0, $paginator->calculateDatabaseOffset(0));
        $this->assertEquals(0, $paginator->calculateDatabaseOffset(1));
        $this->assertEquals(5, $paginator->calculateDatabaseOffset(2));
    }

    public function testGetHiddenRanges()
    {
        $totalItems = 10;
        $paginator = new Paginator($totalItems);

        // current page is 1 - number of page is 1
        $ranges = $paginator->getHiddenRanges();
        $this->assertEmpty($ranges);

        $paginator->setTotalItems(20);
        $ranges = $paginator->getHiddenRanges();
        $this->assertEmpty($ranges);

        $paginator->setTotalItems(100);
        $ranges = $paginator->getHiddenRanges();
        $this->assertEmpty($ranges);

        $paginator->setPerPage(5);

        // number of page is 20
        $this->assertEquals([
            [
                'start' => 9,
                'finish' => 17
            ]
        ], $paginator->getHiddenRanges());

        $paginator->setCurrentPage(6);

        // still the hidden range should be the same
        $this->assertEquals([
            [
                'start' => 9,
                'finish' => 17
            ]
        ], $paginator->getHiddenRanges());

        $paginator->setCurrentPage(7);
        $this->assertEquals([
            [
                'start' => 3,
                'finish' => 4
            ],
            [
                'start' => 10,
                'finish' => 17
            ]
        ], $paginator->getHiddenRanges());

        $paginator->setCurrentPage(8);
        $this->assertEquals([
            [
                'start' => 3,
                'finish' => 5
            ],
            [
                'start' => 11,
                'finish' => 17
            ]
        ], $paginator->getHiddenRanges());

        $paginator->setCurrentPage(14);
        $this->assertEquals([
            [
                'start' => 3,
                'finish' => 11
            ],
            [
                'start' => 17,
                'finish' => 17
            ]
        ], $paginator->getHiddenRanges());

        $paginator->setCurrentPage(15);
        $this->assertEquals([
            [
                'start' => 3,
                'finish' => 11
            ],
        ], $paginator->getHiddenRanges());

        $paginator->setCurrentPage(16);
        $this->assertEquals([
            [
                'start' => 3,
                'finish' => 11
            ],
        ], $paginator->getHiddenRanges());

        $paginator->setCurrentPage(17);
        $this->assertEquals([
            [
                'start' => 3,
                'finish' => 11
            ],
        ], $paginator->getHiddenRanges());

        $paginator->setCurrentPage(18);
        $this->assertEquals([
            [
                'start' => 3,
                'finish' => 11
            ],
        ], $paginator->getHiddenRanges());

        $paginator->setCurrentPage(19);
        $this->assertEquals([
            [
                'start' => 3,
                'finish' => 11
            ],
        ], $paginator->getHiddenRanges());

        $paginator->setCurrentPage(20);
        $this->assertEquals([
            [
                'start' => 3,
                'finish' => 11
            ],
        ], $paginator->getHiddenRanges());
    }
}
