<?php

namespace Guysolamour\Administrable\Models;


class Comment extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'comment', 'approved', 'guest_name', 'guest_email', 'reply_notification'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'approved'           => 'boolean',
        'reply_notification' => 'boolean',
    ];

    /**
     * The user who posted the comment.
     */
    public function commenter()
    {
        return $this->morphTo();
    }

    /**
     * The user who posted the comment.
     */
    public function author()
    {
        return $this->commenter();
    }

    /**
     * The model that was commented upon.
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * Returns all comments that this comment is the parent of.
     */
    public function children()
    {
        return $this->hasMany(Comment::class, 'child_id');
    }

    /**
     * Returns the comment to which this comment belongs to.
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'child_id');
    }

    public function scopeNotes($query, bool $type = true)
    {
        $comparaison = $type ? '=' : '!=';

        return $query->where('commentable_type', $comparaison, Mailbox::class);
    }


    public function getCommenterName()
    {
        return $this->guest_name ?: $this->commenter->name;
    }

    public function getCommenterEmail()
    {
        return $this->guest_email ?: $this->commenter->email;
    }


    public function approved()
    {
        $this->update(['approved' => true]);
    }

    // add relation methods below

}
