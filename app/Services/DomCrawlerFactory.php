<?php

namespace App\Services;

use App\Contracts\CrawlerFactory;
use Symfony\Component\DomCrawler\Crawler;

class DomCrawlerFactory implements CrawlerFactory
{
    public function createFromContent(string $content): Crawler
    {
        return new Crawler($content);
    }
}
