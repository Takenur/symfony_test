<?php

namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;

class HtmlParse
{
    public function parseFromUrl(string $url)
    {
        $html=WebHelper::proxy($url);

        $crawler=new Crawler($html);

        $productName = $crawler->filter('h1[itemprop="name"]')->text("");
        $imageUrl = $crawler->filter('img[itemprop="thumbnail"]')->attr('src',"");
        $description = $crawler->filter('h2.title-1')->text("");
        return [
            'name' => $productName,
            'image' => $imageUrl,
            'description' => $description,
        ];
    }
}