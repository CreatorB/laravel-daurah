<?php

namespace App\Enums;

enum QuestionStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('questions.status.pending'),
            self::Approved => __('questions.status.approved'),
            self::Rejected => __('questions.status.rejected'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-warning text-dark',
            self::Approved => 'bg-success',
            self::Rejected => 'bg-danger',
        };
    }
}
