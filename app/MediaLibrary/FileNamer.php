<?php

namespace App\MediaLibrary;

use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer as DefaultFileNamer;

class FileNamer extends DefaultFileNamer
{
    public function originalFileName(string $fileName): string
    {
        $id = uniqid();

        return "IMG_{$id}";
    }

    public function conversionFileName(string $fileName, Conversion $conversion): string
    {
        $id = uniqid();

        return "IMG_{$id}-{$conversion->getName()}";
    }

    public function responsiveFileName(string $fileName): string
    {
        $id = uniqid();

        return "IMG_{$id}";
    }
}
