<?php

namespace Guysolamour\Administrable\Traits;


trait DraftableTrait
{

    /**
     * Get online elements
     *
     * @param  $query
     * @return void
     */
    public function scopeOnline($query)
    {
        return $query->where('online', true);
    }

    /**
     * Get draft elements
     *
     * @param  $query
     * @return void
     */
    public function scopeDraft($query)
    {
        return !$this->online();
    }


    /**
     * Determine if the model is online.
     *
     * @return bool
     */
    public function isOnline(): bool
    {
        return $this->online;
    }


    /**
     * Determine if the model is draft.
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return !$this->isOnline();
    }
}
