<?php

namespace Paginator;

use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
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
    }

    public function testGetCurrentPage()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);
        $this->assertEquals(1, $paginator->getCurrentPage());

        $paginator = new Paginator($totalItems, 10, 5);
        $this->assertEquals(5, $paginator->getCurrentPage());

        $paginator = new Paginator($totalItems, 10, 0);
        $this->assertEquals(1, $paginator->getCurrentPage());
    }

    public function testGetNumberOfPages()
    {
        $totalItems = 50;
        $paginator = new Paginator($totalItems);
        $this->assertEquals(5, $paginator->getNumberOfPages());

        $paginator = new Paginator($totalItems, 49);
        $this->assertEquals(2, $paginator->getNumberOfPages());

        $paginator = new Paginator($totalItems, 50);
        $this->assertEquals(1, $paginator->getNumberOfPages());

        $paginator = new Paginator($totalItems, 51);
        $this->assertEquals(1, $paginator->getNumberOfPages());
    }
}
