<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class CannedResponse extends Model
{
    use HasUlids;

    protected $table = 'chatbot_canned_responses';

    protected $fillable = ['shortcut', 'body', 'category', 'department_id', 'created_by'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Chatbot\Enterprise\Department::class, 'department_id');
    }

    /** Search by shortcut or body keyword */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('shortcut', 'like', "%{$term}%")
              ->orWhere('body', 'like', "%{$term}%");
        });
    }

    /** Filter by category */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /** All unique categories */
    public static function categories(): array
    {
        return static::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->toArray();
    }
}
