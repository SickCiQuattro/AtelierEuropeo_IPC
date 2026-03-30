<?php

if (!function_exists('getCurrentLocale')) {
    /**
     * Ottieni la lingua corrente
     */
    function getCurrentLocale()
    {
        return app()->getLocale();
    }
}

if (!function_exists('getAvailableLocales')) {
    /**
     * Ottieni tutte le lingue disponibili
     */
    function getAvailableLocales()
    {
        return [
            'it' => __('common.italian'),
            'en' => __('common.english'),
            'es' => 'Español',
            'fr' => 'Français',
        ];
    }
}

if (!function_exists('isCurrentLocale')) {
    /**
     * Verifica se la lingua data è quella corrente
     */
    function isCurrentLocale($locale)
    {
        return getCurrentLocale() === $locale;
    }
}

if (!function_exists('getOtherLocale')) {
    /**
     * Ottieni l'altra lingua disponibile
     */
    function getOtherLocale()
    {
        return getCurrentLocale() === 'it' ? 'en' : 'it';
    }
}

if (!function_exists('formatDate')) {
    /**
     * Formatta una data secondo la lingua corrente
     */
    function formatDate($date, $format = null)
    {
        if (!$date) return '';
        
        if (!$date instanceof \Carbon\Carbon) {
            $date = \Carbon\Carbon::parse($date);
        }
        
        $locale = getCurrentLocale();
        
        if ($format) {
            return $date->locale($locale)->format($format);
        }
        
        // Formato predefinito per ogni lingua
        $formats = [
            'it' => 'd/m/Y',
            'en' => 'm/d/Y',
        ];
        
        return $date->locale($locale)->format($formats[$locale] ?? 'd/m/Y');
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Formatta una data e ora secondo la lingua corrente
     */
    function formatDateTime($datetime, $format = null)
    {
        if (!$datetime) return '';
        
        if (!$datetime instanceof \Carbon\Carbon) {
            $datetime = \Carbon\Carbon::parse($datetime);
        }
        
        $locale = getCurrentLocale();
        
        if ($format) {
            return $datetime->locale($locale)->format($format);
        }
        
        // Formato predefinito per ogni lingua
        $formats = [
            'it' => 'd/m/Y H:i',
            'en' => 'm/d/Y h:i A',
        ];
        
        return $datetime->locale($locale)->format($formats[$locale] ?? 'd/m/Y H:i');
    }
}

if (!function_exists('translateStatus')) {
    /**
     * Traduci uno stato comune
     */
    function translateStatus($status)
    {
        $statusTranslations = [
            'active' => __('common.active'),
            'inactive' => __('common.inactive'),
            'pending' => __('common.pending'),
            'approved' => __('common.approved'),
            'rejected' => __('common.rejected'),
            'completed' => __('common.completed'),
            'draft' => __('common.draft'),
        ];
        
        return $statusTranslations[$status] ?? $status;
    }
}

if (!function_exists('formatDateForInput')) {
    /**
     * Formatta una data per i campi input di tipo date
     */
    function formatDateForInput($date)
    {
        if (!$date) return '';
        
        if (!$date instanceof \Carbon\Carbon) {
            $date = \Carbon\Carbon::parse($date);
        }
        
        return $date->format('Y-m-d');
    }
}
