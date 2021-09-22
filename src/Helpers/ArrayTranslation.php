<?php

namespace Takachi67\LaravelVueTranslations\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class ArrayTranslation 
{
    /**
     * Fill translations collection from a directory files
     */
    public static function fillTranslations(Collection $translations, string $directory)
    {
        collect(File::files($directory))->flatMap(function ($file) use ($directory, $translations) {
            $path = str_replace(resource_path('lang/' . App::getLocale()), '', $directory) . '/' . $file->getBasename('.php');
            $translations[substr(str_replace('/', '.', $path), 1)] = trans($path);
        });

        return $translations;
    }
}