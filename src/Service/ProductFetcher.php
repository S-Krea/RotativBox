<?php

namespace App\Service;

use Graviton\LinkHeaderParser\LinkHeader;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductFetcher
{
    private HttpClientInterface $woocommerceApi;

    public function __construct(
        HttpClientInterface $woocommerceApi
    )
    {
        $this->woocommerceApi = $woocommerceApi;
    }

    public function fetchProducts($url = 'products', $options = [])
    {

        $options = array_merge_recursive(['query' => [
            'per_page' => '50',
            'tag' => '93',
        ]], $options);
        $response = $this->woocommerceApi->request('GET', $url, $options);
        $nextLink = $this->findNext($response->getHeaders());

        return [
            'products' => $response->getContent(),
            'next' => $nextLink
        ];
    }

    private function findNext(array $headers): ?string
    {
        $links = $headers['link'];
        $link = LinkHeader::fromString($links[0]);

        return $link->getRel('next')?->getUri();
    }
}