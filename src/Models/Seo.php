<?php

namespace Guysolamour\Administrable\Models;

use Spatie\MediaLibrary\HasMedia;
use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Database\Eloquent\Model;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Guysolamour\Administrable\Traits\MediaableTrait;

class Seo extends Model implements HasMedia
{
    use MediaableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seo_meta_tags';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'page:title', 'og:locale', 'og:type', 'og:title', 'og:description', 'og:url', 'og:image',
        'twitter:type', 'twitter:title', 'twitter:image', 'twitter:description',
        'robots:index', 'robots:follow', 'page:canonical:url', 'page:author', 'page:meta:description',
        'page:meta:keywords', 'html'
    ];

    /**
     * The model default values.
     *
     * @var array
     */
    protected $attributes = [
        'page:title'            => '',
        'og:title'              => '',
        'og:url'                => '',
        'og:description'        => '',
        'twitter:title'         => '',
        'twitter:description'   => '',
        'page:canonical:url'    => '',
        'page:meta:description' => '',
        'og:type'               => 'article',
        'twitter:type'          => 'summary',
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function seoable()
    {
        return $this->morphTo();
    }

    public function getHtmlTags(bool $force = false): ?string
    {
        return $force ? $this->generateTags() : $this->html;
    }

    

    public function generateTags(?Model $model = null): string
    {
        if ($this['page:title']) {
            SEOMeta::setTitle($this['page:title']);
        }

        if ($this['robots:index'] && $this['robots:follow']) {
            SEOMeta::setRobots('robots', "{$this['robots:index']},{$this['robots:follow']}");
        }

        if ($this['page:author']) {
            SEOMeta::addMeta('author', $this['page:author']);
        }

        if ($this['page:meta:keywords']) {
            SEOMeta::setKeywords($this['page:meta:keywords']);
        }

        if ($this['page:meta:description']) {
            SEOMeta::setDescription($this['page:meta:description']);
        }

        if ($this['page:canonical:url']) {
            SEOMeta::setDescription($this['page:canonical:url']);
        }

        // OpenGraph
        if ($this['og:title']) {
            OpenGraph::setTitle($this['og:title']);
        } else if (SEOMeta::getTitle()) {
            OpenGraph::setTitle(SEOMeta::getTitle());
        }

        if ($this['og:description']) {
            OpenGraph::setDescription($this['og:description']);
        } else if (SEOMeta::getDescription()) {
            OpenGraph::setDescription(SEOMeta::getDescription());
        }


        if ($this['og:url']) {
            OpenGraph::setUrl($this['og:url']);
        } else if (is_object($model) && (method_exists($model, 'getFrontRoute'))) {
            OpenGraph::setUrl($model->getFrontRoute());
        }

        if ($this['og:url']) {
            OpenGraph::addProperty('type', $this['og:type']);
        }

        OpenGraph::addProperty('locale', $this['og:locale'] ?? config('app.locale'));

        if ($this['og:image']) {
            OpenGraph::addImage($this['og:image']);
        } else if (is_object($model) && method_exists($model, 'getFrontImageUrl')) {
            OpenGraph::setUrl($model->getFrontImageUrl());
        }

        // Twitter
        if ($this['twitter:title']) {
            TwitterCard::setTitle($this['twitter:title']);
        } else if (SEOMeta::getTitle()) {
            TwitterCard::setTitle(SEOMeta::getTitle());
        }

        if ($this['twitter:type']) {
            TwitterCard::setType($this['twitter:type']);
        }

        if ($this['twitter:image']) {
            TwitterCard::setImage($this['twitter:image']);
        } else if ($this['og:image']) {
            TwitterCard::setImage($this['og:image']);
        } else if (is_object($model) && method_exists($model, 'getFrontImageUrl')) {
            TwitterCard::setImage($model->getFrontImageUrl());
        }

        if ($this['twitter:description']) {
            TwitterCard::setDescription($this['twitter:description']);
        } else if (SEOMeta::getDescription()) {
            TwitterCard::setDescription(SEOMeta::getDescription());
        }

        // JsonLd
        if (SEOMeta::getTitle()) {
            JsonLd::setTitle(SEOMeta::getTitle());
        }

        if (SEOMeta::getDescription()) {
            JsonLd::setDescription(SEOMeta::getDescription());
        }


        if ($this['og:image']) {
            JsonLd::addImage($this['og:image']);
        } else if ($this['twitter:image']) {
            JsonLd::addImage($this['twitter:image']);
        } else if (is_object($model) && method_exists($model, 'getFrontImageUrl')) {
            JsonLd::addImage($model->getFrontImageUrl());
        }

        return SEOTools::generate();
    }

    public static function booted()
    {
        parent::booted();

        static::saving(function ($model) {
            /**
             * @var Seo $model
             */
            if (request('seo')) {
                $model->addImage('og:image');
                $model->addImage('twitter:image');
            }
        });
    }

    public function addImage(string $key): void
    {
        $key_with_prefix = "seo.{$key}";

        if (!request()->has($key_with_prefix)) {
            return;
        }

        $collection = config('administrable.media.collections.seo.label');

        if (!$collection) {
            return;
        }

        $this->getMedia($collection, ['field' => $key_with_prefix])->each->delete();

        $media =  $this->addMediaFromRequest($key_with_prefix)
            ->withCustomProperties([
                'field' => $key_with_prefix,
            ])
            ->toMediaCollection($collection);

        $this->setAttribute($key, $media->getFullUrl());
    }
}
