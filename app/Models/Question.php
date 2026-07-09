<?php

namespace App\Models;

use App\Enums\QuestionStatus;
use Carbon\CarbonInterface;
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
            if (empty($question->public_ref) && $question->status === QuestionStatus::Approved) {
                $question->public_ref = static::generatePublicRefForDate(
                    $question->published_at ?: $question->approved_at ?: now()
                );
            }
        });
    }

    public static function generatePublicRefForDate(CarbonInterface $date): string
    {
        $prefix = $date->format('ymd').'-';
        $maxAttempts = 50;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $sequence = static::nextDailySequence($date, $prefix);
            $ref = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

            if (! static::where('public_ref', $ref)->exists()) {
                return $ref;
            }
        }

        throw new \RuntimeException('Unable to generate unique public_ref for date '.$date->toDateString());
    }

    protected static function nextDailySequence(CarbonInterface $date, string $prefix): int
    {
        $latest = static::query()
            ->where('public_ref', 'like', $prefix.'%')
            ->orderByDesc('public_ref')
            ->value('public_ref');

        if (! $latest) {
            return 1;
        }

        $lastSeq = (int) substr($latest, strlen($prefix));

        return $lastSeq + 1;
    }

    public static function generatePublicRef(): string
    {
        return static::generatePublicRefForDate(now());
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

    public function getDisplayRefAttribute(): string
    {
        if (! empty($this->public_ref)) {
            return $this->public_ref;
        }

        $date = $this->created_at ?: $this->updated_at ?: now();
        $prefix = $date->format('ymd').'-';
        $sequence = (int) static::query()
            ->whereDate('created_at', $date->toDateString())
            ->where('id', '<=', $this->id)
            ->count();
        if ($sequence < 1) {
            $sequence = 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
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
