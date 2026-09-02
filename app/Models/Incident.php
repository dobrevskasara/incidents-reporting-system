<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'age_group',
        'platform',
        'status',
        'description',
        'reported_at',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
    ];

    /**
     * Available incident types.
     *
     * @return array<string>
     */
    public static function types(): array
    {
        return [
            'Harassment / bullying',
            'Hate speech',
            'Fraud / financial abuse',
            'Inappropriate content',
            'Sexual abuse or exploitation',
            'Fake profile / identity theft',
            'Personal data abuse',
            'Other',
        ];
    }

    /**
     * Available age groups.
     *
     * @return array<string>
     */
    public static function ageGroups(): array
    {
        return [
            'under 12',
            '13-15',
            '16-17',
            '18-24',
            '25-34',
            '35-44',
            '45+',
            'unknown',
        ];
    }

    /**
     * Available platforms.
     *
     * @return array<string>
     */
    public static function platforms(): array
    {
        return [
            'Facebook',
            'Instagram',
            'TikTok',
            'WhatsApp',
            'Snapchat',
            'YouTube',
            'Discord',
            'Website / Forum',
            'Other',
        ];
    }

    /**
     * Available report statuses.
     *
     * @return array<string>
     */
    public static function statuses(): array
    {
        return [
            'reported',
            'in progress',
            'resolved',
            'rejected',
        ];
    }

    /**
     * Scope: incidents reported within a given [from, to] window.
     */
    public function scopeReportedBetween(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('reported_at', [$from, $to]);
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $type ? $query->where('type', $type) : $query;
    }

    public function scopeOnPlatform(Builder $query, ?string $platform): Builder
    {
        return $platform ? $query->where('platform', $platform) : $query;
    }

    public function scopeOfAgeGroup(Builder $query, ?string $ageGroup): Builder
    {
        return $ageGroup ? $query->where('age_group', $ageGroup) : $query;
    }
}
