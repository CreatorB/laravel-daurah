<?php

namespace App\Models;

use App\Enums\QuestionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Question extends Model
{
    protected $fillable = [
        'name',
        'question_body',
        'is_anonymous',
        'locale',
        'status',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'published_at',
        'lesson_code',
        'public_ref',
        'admin_note',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'status' => QuestionStatus::class,
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Question $question): void {
            if (empty($question->public_ref)) {
                $question->public_ref = static::generatePublicRef();
            }
        });
    }

    public static function generatePublicRef(): string
    {
        do {
            $ref = 'Q-'.strtoupper(Str::random(8));
        } while (static::where('public_ref', $ref)->exists());

        return $ref;
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', QuestionStatus::Approved->value);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', QuestionStatus::Pending->value);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', QuestionStatus::Rejected->value);
    }

    public function scopeForLocale(Builder $query, ?string $locale): Builder
    {
        return $locale ? $query->where('locale', $locale) : $query;
    }

    public function scopeLatestPublished(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('approved_at')->orderByDesc('id');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('question_body', 'like', $like)
              ->orWhere('name', 'like', $like)
              ->orWhere('public_ref', 'like', $like)
              ->orWhere('lesson_code', 'like', $like);
        });
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous || blank($this->name)) {
            return __('questions.anonymous');
        }

        return $this->name;
    }

    public function getExcerptAttribute(?int $length = null): string
    {
        $length = $length ?? 180;

        $body = strip_tags((string) $this->question_body);
        $body = preg_replace('/\s+/u', ' ', $body) ?? '';

        return Str::limit($body, $length);
    }

    public function isPending(): bool
    {
        return $this->status === QuestionStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === QuestionStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === QuestionStatus::Rejected;
    }
}
