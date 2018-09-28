# Paginator
A powerful PHP pagination engine to take care of pagination hassles

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
The simplest way to use Paginator is as follow:
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

If you are using any MVC framework like Laravel instantiate an object from Paginator class in the controller and pass it to the view:

#### Controller
```
public function index(Request $request)
{
    $totalProductsNumber = 10;
    $perPage = 10;
    $currentPageNumber = (int) $request->get('page') > 0 ? $request->get('page') : 1;
    $url = $request->fullUrl();
    
    $paginator = new Paginator($totalProductsNumber, $perPage, $currentPageNumber, $url);

    // you should use the offset in your database query to get a slice of data
    $offset = $paginator->calculateDatabaseOffset($currentPageNumber);

    return view('view.index', [
        'paginator' => $paginator
    ]);
}
```

#### View (Laravel Blade)
```
@if ($paginator->hasPages())
    <ul class="pagination">
        @if ($paginator->isOnFirstPage() === true)
            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
        @else
            <li class="page-item"><a class="page-link" href="{{ $paginator->getPreviousPageUrl() }}" rel="prev" title="Previous">&laquo;</a></li>
        @endif

        @php
            $hiddenRanges = $paginator->getHiddenRanges();
        @endphp

        @foreach ($paginator->getPages() as $page)
            {{-- "Three Dots" Separator --}}
            @if ((isset($hiddenRanges[0]) && $page->getNumber() === $hiddenRanges[0]['start']) ||
            (isset($hiddenRanges[1]) && $page->getNumber() === $hiddenRanges[1]['start']))
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @elseif((isset($hiddenRanges[0]) && $page->getNumber() > $hiddenRanges[0]['start'] && $page->getNumber() <= $hiddenRanges[0]['finish']) ||
            (isset($hiddenRanges[1]) && $page->getNumber() > $hiddenRanges[1]['start'] && $page->getNumber() <= $hiddenRanges[1]['finish']))
                @continue
            @else
                @if ($page->isCurrent())
                    <li class="page-item active"><span class="page-link">{{ $page->getNumber() }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $page->getUrl() }}" title="Page">{{ $page->getNumber() }}</a></li>
                @endif
            @endif
        @endforeach

        @if ($paginator->isOnLastPage() === false)
            <li class="page-item"><a class="page-link" href="{{ $paginator->getNextPageUrl() }}" rel="next" title="Next">&raquo;</a></li>
        @else
            <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
        @endif
    </ul>
@endif
```