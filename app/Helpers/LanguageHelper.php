<?php

namespace App\Helpers;

class LanguageHelper
{
    /**
     * Get the current application language
     *
     * @return string
     */
    public static function getCurrentLanguage(): string
    {
        return app()->getLocale();
    }

    /**
     * Get all available languages
     *
     * @return array
     */
    public static function getAvailableLanguages(): array
    {
        return config('app.available_languages', [
            'en' => 'English',
        ]);
    }

    /**
     * Set the application language
     *
     * @param string $language
     * @return void
     */
    public static function setLanguage(string $language): void
    {
        app()->setLocale($language);
    }
}
