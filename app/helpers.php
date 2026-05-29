<?php

if (! function_exists('site_setting')) {
    /**
     * Get a key from the DB-backed site_settings table.
     * Falls back to the supplied default (often a config() lookup).
     */
    function site_setting(string $key, mixed $default = null): mixed
    {
        return \App\SiteSetting::get($key, $default);
    }
}

if (! function_exists('site_whatsapp_number')) {
    /** Canonical: digits-only WhatsApp number (no plus). */
    function site_whatsapp_number(): string
    {
        return (string) site_setting('whatsapp_number', config('site.whatsapp.number'));
    }
}
