<?php
namespace App\Contracts;

use Symfony\Component\DomCrawler\Crawler;

interface CrawlerFactory
{
    public function createFromContent(string $content): Crawler;
}
