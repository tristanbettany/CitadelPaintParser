<?php

namespace App;

final class Parser
{
    private array $content;
    private array $records = [];
    private array $products = [];

    public function __construct()
    {
        $json = json_decode(file_get_contents(__DIR__ . '/../data/searchResponse.json'));
        $contents = reset($json->contents);
        $this->content = $contents->mainContent;
    }

    public function loadRecords(): self
    {
        foreach($this->content as $contentItem) {
            if ($contentItem->name === 'Shared Results List') {
                $contentItemContents = reset($contentItem->contents);
                $this->records = $contentItemContents->records;
            }
        }

        return $this;
    }

    public function loadProducts(): self
    {
        if (empty($this->records) === false) {
            foreach($this->records as $record) {
                $nameKey = 'product.displayName';
                $skuKey = 'sku.repositoryId';
                $fieldObjKey = 'analytics.productFieldObject';

                $fieldObj = reset($record->attributes->$fieldObjKey);
                $price = (float) $fieldObj->price;

                $nameVal = reset($record->attributes->$nameKey);

                if (str_contains($nameVal, 'Collection') === true) {
                    continue;
                }

                preg_match_all(
                    $this->getRegexForString($nameVal),
                    $nameVal,
                    $matches
                );

                if (empty($matches[2]) === false) {
                    if (is_array($matches[2]) === true) {
                        $nameVal = $matches[2][0];
                    } else {
                        $nameVal = $matches[2];
                    }

                    $nameVal = str_replace(':', '', $nameVal);
                }

                $type = '';
                if (empty($matches[1]) === false) {
                    if (is_array($matches[1]) === true) {
                        $type = $matches[1][0];
                    } else {
                        $type = $matches[1];
                    }

                    $nameVal = str_replace($type, '', $nameVal);
                }

                $nameVal = trim($nameVal);

                //Image
                $imageKey = 'product.imageName';
                $imageBaseUrl = 'https://www.games-workshop.com/resources/catalog/product/600x620/';
                $imageUrl = $imageBaseUrl . reset($record->attributes->$imageKey);

                $color = '';
                if (str_contains($imageUrl, '.svg') === true) {
                    $color = $this->processSVG($imageUrl);
                }

                $this->products[] = [
                    'name' => $nameVal,
                    'sku' => reset($record->attributes->$skuKey),
                    'price' => $price,
                    'type' => $type,
                    'color' => $color,
                ];
            }
        }

        return $this;
    }

    private function processSVG(string $imageUrl): string
    {
        $imageContents = file_get_contents($imageUrl);

        $pattern = "/fill=\"(#.{6})\"/m";
        preg_match($pattern, $imageContents, $matches);

        if (empty($matches) === false) {
            return $matches[1];
        }

        return '';
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    private function getRegexForString(string $str): string
    {
        if (
            str_contains($str, 'UK/ROW') === true
            || str_contains($str, 'ml)') === true
        ) {
            return "/(Spray|Layer|Base|Dry|Technical|Shade|Contrast|Air).\s(.+)(\(\d+ml\)|UK\/ROW)\s+(\(\d+\))/m";
        }

        return "/(Spray|Layer|Base|Dry|Technical|Shade|Contrast|Air).\s(.+)\s+(\(\d+\))/m";
    }
}