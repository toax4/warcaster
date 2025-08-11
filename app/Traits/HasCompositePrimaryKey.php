<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasCompositePrimaryKey
{
    /**
     * Override getKeyName to return the array of primary keys.
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Override getKey to return an array of primary key values.
     */
    public function getKey()
    {
        return collect($this->getKeyName())
            ->mapWithKeys(fn ($key) => [$key => $this->getAttribute($key)])
            ->all();
    }

    /**
     * Override setKeysForSaveQuery to support composite keys.
     */
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $keyName) {
            $query->where($keyName, '=', $this->getAttribute($keyName));
        }

        return $query;
    }

    /**
     * Force Laravel to understand that it's not auto-incrementing.
     */
    public function getIncrementing()
    {
        return false;
    }
}
