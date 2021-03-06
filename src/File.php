<?php

namespace App;

final class File
{
    public static function saveAsJson(
        array $data,
        string $filename
    ): void {
        $json = json_encode($data);
        file_put_contents(
            __DIR__ . '/../storage/' . $filename . '.json',
            $json
        );
    }
}