<?php

namespace Guysolamour\Administrable\Traits;

use Cviebrock\EloquentSluggable\Sluggable;

trait SluggableTrait
{
    use Sluggable;

    public function getRouteKeyName(): string
    {
        return  config('administrable.modules.sluggable.key', 'slug');
    }


    private function getSluggableSource(): string
    {
        if (property_exists($this, 'sluggablefield')) {
            return $this->sluggablefield;
        }

        return config('administrable.modules.sluggable.field', 'title');
    }


    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => ['source' => $this->getSluggableSource()]
        ];
    }

    /**
     * Get elemet by Slug
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }


    /**
     * Get elements by slug
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $slugs
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindAllBySlug($query, array $slugs)
    {
        return $query->whereIn('slug', $slugs);
    }
}
