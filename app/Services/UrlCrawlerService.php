<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class UrlCrawlerService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ],
        ]);
    }

    public function crawl($url)
    {
        $response = $this->client->get($url);
        $html = (string) $response->getBody();
        $crawler = new Crawler($html);

        return [
            'title' => $this->getTitle($crawler),
            'description' => $this->getDescription($crawler),
            'icon' => $this->getIcon($crawler, $url),
        ];
    }

    protected function getTitle(Crawler $crawler)
    {
        return $crawler->filter('title')->first()->text('');
    }

    protected function getDescription(Crawler $crawler)
    {
        return $crawler->filter('meta[name="description"]')->attr('content') ?? '';
    }

    protected function getIcon(Crawler $crawler, $url)
    {
        $favicon = $crawler->filter('link[rel="icon"], link[rel="shortcut icon"]')->attr('href');
        if ($favicon) {
            return $this->resolveUrl($url, $favicon);
        }
        return $this->resolveUrl($url, '/favicon.ico');
    }

    protected function resolveUrl($base, $relative)
    {
        if (parse_url($relative, PHP_URL_SCHEME) != '') {
            return $relative;
        }
        if ($relative[0] == '/') {
            $parts = parse_url($base);
            return $parts['scheme'] . '://' . $parts['host'] . $relative;
        }
        return dirname($base) . '/' . $relative;
    }
}
