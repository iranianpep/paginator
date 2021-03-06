<?php

namespace Paginator;

use PHPUnit\Framework\TestCase;

class AbstractPaginatorTest extends TestCase
{
    public function testHasPages()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);

        $this->assertTrue($paginator->hasPages());

        $paginator->setTotalItems(1);
        $this->assertTrue($paginator->hasPages());

        $paginator->setTotalItems(0);
        $this->assertFalse($paginator->hasPages());

        $paginator->setTotalItems(50);
        $paginator->setPerPage(0);
        $this->assertFalse($paginator->hasPages());

        $paginator->setPerPage(500);
        $paginator->setCurrentPage(1000);
        $this->assertTrue($paginator->hasPages());
    }

    public function testGetTotalItems()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);
        $this->assertEquals($totalItems, $paginator->getTotalItems());

        $totalItems = 0;
        $paginator = new Paginator($totalItems);
        $this->assertEquals($totalItems, $paginator->getTotalItems());

        $totalItems = 'invalid number';
        $paginator = new Paginator($totalItems);
        $this->assertEquals(0, $paginator->getTotalItems());

        $paginator->setTotalItems(25);
        $this->assertEquals(25, $paginator->getTotalItems());
    }

    public function testGetPerPage()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);
        $this->assertEquals(10, $paginator->getPerPage());

        $paginator = new Paginator($totalItems, 5);
        $this->assertEquals(5, $paginator->getPerPage());

        $paginator = new Paginator($totalItems, 0);
        $this->assertEquals(0, $paginator->getPerPage());

        $paginator->setPerPage(10);
        $this->assertEquals(10, $paginator->getPerPage());
    }

    public function testGetNumberOfPages()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);
        $this->assertEquals(5, $paginator->calculateNumberOfPages());

        $paginator = new Paginator($totalItems, 49);
        $this->assertEquals(2, $paginator->calculateNumberOfPages());

        $paginator = new Paginator($totalItems, 50);
        $this->assertEquals(1, $paginator->calculateNumberOfPages());

        $paginator = new Paginator($totalItems, 51);
        $this->assertEquals(1, $paginator->calculateNumberOfPages());

        $paginator = new Paginator(0);
        $this->assertEquals(0, $paginator->calculateNumberOfPages());

        $paginator = new Paginator($totalItems, 0);
        $this->assertEquals(0, $paginator->calculateNumberOfPages());

        $paginator->setPerPage(5);
        $this->assertEquals(10, $paginator->calculateNumberOfPages());

        $paginator->setTotalItems(500);
        $this->assertEquals(100, $paginator->calculateNumberOfPages());

        $paginator->setTotalItems(3);
        $paginator->setPerPage(3);
        $this->assertEquals(1, $paginator->calculateNumberOfPages());

        $paginator->setTotalItems(4);
        $this->assertEquals(2, $paginator->calculateNumberOfPages());

        $paginator->setTotalItems(5);
        $this->assertEquals(2, $paginator->calculateNumberOfPages());

        $paginator->setTotalItems(6);
        $this->assertEquals(2, $paginator->calculateNumberOfPages());

        $paginator->setTotalItems(7);
        $this->assertEquals(3, $paginator->calculateNumberOfPages());

        $paginator->setTotalItems(2);
        $this->assertEquals(1, $paginator->calculateNumberOfPages());

        $paginator->setTotalItems(1);
        $this->assertEquals(1, $paginator->calculateNumberOfPages());

        $paginator->setTotalItems(0);
        $this->assertEquals(0, $paginator->calculateNumberOfPages());

        $paginator->setTotalItems(24);
        $paginator->setPerPage(5);
        $this->assertEquals(5, $paginator->calculateNumberOfPages());

        $paginator->setPerPage(23);
        $this->assertEquals(2, $paginator->calculateNumberOfPages());

        $paginator->setPerPage(24);
        $this->assertEquals(1, $paginator->calculateNumberOfPages());

        $paginator = new Paginator(30, 49);
        $this->assertEquals(1, $paginator->calculateNumberOfPages());

        $paginator = new Paginator(48, 49);
        $this->assertEquals(1, $paginator->calculateNumberOfPages());

        $paginator = new Paginator(49, 49);
        $this->assertEquals(1, $paginator->calculateNumberOfPages());

        $paginator = new Paginator(50, 49);
        $this->assertEquals(2, $paginator->calculateNumberOfPages());
    }

    public function testGetCurrentPage()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);

        // if current page is not set, 1 is considered
        $this->assertEquals(1, $paginator->getCurrentPage()->getNumber());

        $paginator = new Paginator($totalItems, 10, 4);
        $this->assertEquals(4, $paginator->getCurrentPage()->getNumber());

        $paginator = new Paginator($totalItems, 10, 5);
        $this->assertEquals(5, $paginator->getCurrentPage()->getNumber());

        $paginator = new Paginator($totalItems, 10, 6);

        // because the maximum page is 5, and there is no page 6
        $this->assertEmpty($paginator->getCurrentPage());

        // because 0 is invalid page number
        $paginator = new Paginator($totalItems, 10, 0);
        $this->assertEmpty($paginator->getCurrentPage());

        $paginator->setCurrentPage(4);
        $this->assertEquals(4, $paginator->getCurrentPage()->getNumber());

        $paginator->setPerPage(50);

        // total items is 50, per page is 50, so there is only 1 page, so page number 4 does not exist anymore
        $this->assertEmpty($paginator->getCurrentPage());

        $paginator->setTotalItems(5);
        $paginator->setPerPage(1);
        $this->assertEquals(1, $paginator->getCurrentPage()->getNumber());

        $paginator->setCurrentPage(2);
        $paginator->setPerPage(2);

        // it still should be in second page
        $this->assertEquals(2, $paginator->getCurrentPage()->getNumber());

        $paginator->setPerPage(3);
        $this->assertEquals(2, $paginator->getCurrentPage()->getNumber());

        // with total items 5, and per page 5, there is no page 2
        $paginator->setPerPage(5);
        $this->assertEmpty($paginator->getCurrentPage());

        // move the current page to the last page
        $paginator->setPerPage(1);
        $paginator->setCurrentPage(5);

        // if per page is changed, the current page should be still the last page
        $paginator->setPerPage(2);

        // total items is 5, per page is 2, but page number 5 doesn't exist
        $this->assertEmpty($paginator->getCurrentPage());

        // total number of items is changed from 5 to 3, which reduces the number of pages to 2 (per page is 2)
        // So the current page is set to 1
        $paginator->setTotalItems(3);
        $this->assertEquals(1, $paginator->getCurrentPage()->getNumber());

        $paginator->setTotalItems(1);
        $this->assertEquals(1, $paginator->getCurrentPage()->getNumber());

        $paginator->setTotalItems(9);
        $this->assertEquals(1, $paginator->getCurrentPage()->getNumber());
    }

    public function testGetPages()
    {
        $totalItems = 7;
        $paginator = new Paginator($totalItems, 1, 1, 'https://www.example.com');

        $pages = $paginator->getPages();
        $this->assertEquals(7, count($pages));
        $this->assertEquals(true, $pages[1]->isFirst());
        $this->assertEquals(false, $pages[1]->isLast());
        $this->assertEquals('https://www.example.com/?page=1', $pages[1]->getUrl());
        $this->assertEquals(false, $pages[1]->isHidden());

        $this->assertEquals(false, $pages[4]->isFirst());
        $this->assertEquals(false, $pages[4]->isLast());
        $this->assertEquals('https://www.example.com/?page=4', $pages[4]->getUrl());
        $this->assertEquals(false, $pages[4]->isHidden());

        $this->assertEquals(false, $pages[7]->isFirst());
        $this->assertEquals(true, $pages[7]->isLast());
        $this->assertEquals('https://www.example.com/?page=7', $pages[7]->getUrl());
        $this->assertEquals(false, $pages[7]->isHidden());

        $totalItems = 1;
        $paginator = new Paginator($totalItems);

        $pages = $paginator->getPages();
        $this->assertEquals(1, count($pages));
        $this->assertEquals(true, $pages[1]->isFirst());
        $this->assertEquals(true, $pages[1]->isLast());
        $this->assertEmpty($pages[1]->getUrl());
        $this->assertEquals(false, $pages[1]->isHidden());

        $totalItems = 3;
        $paginator = new Paginator($totalItems);
        $paginator->setPerPage(1);
        $paginator->setOnEachSide(1);

        $pages = $paginator->getPages();
        $this->assertEquals(3, count($pages));
        $this->assertEquals(true, $pages[1]->isFirst());
        $this->assertEquals(false, $pages[1]->isLast());
        $this->assertEmpty($pages[1]->getUrl());
        $this->assertEquals(false, $pages[1]->isHidden());

        $this->assertEquals(false, $pages[2]->isFirst());
        $this->assertEquals(false, $pages[2]->isLast());
        $this->assertEmpty($pages[2]->getUrl());
        $this->assertEquals(false, $pages[2]->isHidden());

        $this->assertEquals(false, $pages[3]->isFirst());
        $this->assertEquals(true, $pages[3]->isLast());
        $this->assertEmpty($pages[3]->getUrl());
        $this->assertEquals(false, $pages[3]->isHidden());
    }
}
