<?php

namespace Takachi67\LaravelVueTranslations\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Takachi67\LaravelVueTranslations\Helpers\ArrayTranslation;

class LocalizationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishConfigs();

        Cache::rememberForever(config('translations.key_name'), function () {
            $files = File::files(resource_path('lang/'. App::getLocale()));

            $translations = collect($files)->flatMap(function ($file) {
                return [
                    $translation = $file->getBasename('.php') => trans($translation)
                ];
            });

            $directories = File::directories(resource_path('lang/'. App::getLocale()));

            foreach ($directories as $directory) {
                ArrayTranslation::fillTranslations($translations, $directory);

                $subDirectories = File::directories($directory);

                foreach ($subDirectories as $subDirectory) {
                    ArrayTranslation::fillTranslations($translations, $subDirectory);
                }

                $translations = $translations->toArray();

                foreach ($translations as $key => $translation) {
                    Arr::set($translations, $key, $translation);
                }
            }

            return collect($translations)->toJson();
        });
    }

    /**
     * Publish the config file
     */
    protected function publishConfigs()
    {
        $this->publishes([
            __DIR__ . '/../config/translations.php' => config_path('translations.php')
        ]);
    }
}
