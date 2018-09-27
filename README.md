# Paginator
A simple yet powerful PHP pagination engine

[![Build Status](https://travis-ci.org/iranianpep/paginator.svg?branch=master)](https://travis-ci.org/iranianpep/paginator)
[![Maintainability](https://api.codeclimate.com/v1/badges/9f8b2dd15bf3a8f48103/maintainability)](https://codeclimate.com/github/iranianpep/paginator/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/9f8b2dd15bf3a8f48103/test_coverage)](https://codeclimate.com/github/iranianpep/paginator/test_coverage)
[![StyleCI](https://styleci.io/repos/149567054/shield?branch=master)](https://styleci.io/repos/149567054)
[![Issue Count](https://codeclimate.com/github/iranianpep/paginator/badges/issue_count.svg)](https://codeclimate.com/github/iranianpep/paginator)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/1438614849564c76ac97e4eefccee63d)](https://www.codacy.com/app/iranianpep/paginator?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=iranianpep/paginator&amp;utm_campaign=Badge_Grade)

## Server Requirements
- PHP >= 7.1

## Installation
- Using Composer get the latest version:
```
composer require paginator/paginator
```

## Example
```
$totalItems = 3;
$perPage = 1;
$currentPage = 1;
$url = 'https://example.com';

$paginator = new Paginator($totalItems, $perPage, $currentPage, $url);

if ($paginator->hasPages() === true) {
    if ($paginator->getPreviousPage()) {
        $previousPageUrl = $paginator->getPreviousPageUrl();

        echo "<li><a href='{$previousPageUrl}'>Previous</a></li>";
    }

    foreach ($paginator->getPages() as $page) {
        if (!$page instanceof Page) {
            continue;
        }

        $pageNumber = $page->getNumber();
        $pageUrl = $page->getUrl();
        $cssClass = $page->isCurrent() === true ? 'active' : '';

        echo "<li class='{$cssClass}'><a href='{$pageUrl}'>{$pageNumber}</a></li>";
    }

    if ($paginator->getNextPage()) {
        $nextPageUrl = $paginator->getNextPageUrl();

        echo "<li><a href='{$nextPageUrl}'>Next</a></li>";
    }
}
```
