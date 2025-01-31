<?php

namespace Guysolamour\Administrable\Traits;

use Guysolamour\Administrable\Models\Comment;

/**
 * Add this trait to any model that you want to be able to
 * comment upon or get comments for.
 */
trait CommentableTrait
{
    /**
     * This static method does voodoo magic to
     * delete leftover comments once the commentable
     * model is deleted.
     */
    protected static function bootCommentableTrait()
    {
        static::deleted(function ($commentable) {
            $commentable->comments->each->delete();
        });
    }

    /**
     * Returns all comments for this model.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Returns only approved comments for this model.
     */
    public function approvedComments()
    {
        return $this->morphMany(Comment::class, 'commentable')->where('approved', true);
    }
}
