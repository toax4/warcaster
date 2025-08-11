<?php

namespace App\Traits;

trait HasTranslations
{
    public function translation($langId = 1)
    {
        $translations = collect($this->translations);
        
        $desired = $translations->firstWhere('lang_id', $langId);
        $fallback = $translations->firstWhere('lang_id', 1); // anglais

        if (!$fallback) {
            return null;
        }

        if (!$desired) {
            return $fallback;
        }


        // On merge les deux, priorité à la langue demandée
        foreach ($desired->getAttributes() as $key => $value) {
            if (empty($desired->$key)) {
                $desired->$key = $fallback->$key;
            }
        }

        return $desired;
    }

    public function withTranslation($langId = 1)
    {
        $translations = collect($this->translations);
        $desired = $translations->firstWhere('lang_id', $langId);
        $fallback = $translations->firstWhere('lang_id', 1); // anglais

        if (!$fallback) {
            return $this;
        }

        // On merge les deux, priorité à la langue demandée
        if ($fallback->getTranslatableFields()) {
            $keys = $fallback->getTranslatableFields();
        } else {
            $keys = array_keys($fallback->getAttributes());
        }

        foreach ($keys as $key) {
            if ($desired && array_key_exists($key, $desired->getAttributes()) && !empty($desired->$key)) {
                $this->setAttribute($key, $desired->$key);
            } else {
                $this->setAttribute($key, $fallback->$key);
            }
        }

        return $this;
    }
}