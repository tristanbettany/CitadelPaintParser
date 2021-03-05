<?php

require __DIR__ . '/../vendor/autoload.php';

$json = json_decode(file_get_contents(__DIR__ . '/response.json'));

$contents = reset($json->contents);
$mainContent = $contents->mainContent;

$records = [];

foreach($mainContent as $contentItem) {
    if ($contentItem->name === 'Shared Results List') {
        $contentItemContents = reset($contentItem->contents);
        $records = $contentItemContents->records;
    }
}

if (empty($records) === false) {
    foreach($records as $record) {
        $wut = 'product.displayName';
        echo reset($record->attributes->$wut) . "\n";
    }
}