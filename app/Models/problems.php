<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Problems extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'type', 'status', 'delete_reason', 'user_id', 'problem_attachments','approved'
    ];

    protected $dates = ['deleted_at']; // Soft delete column

    protected $casts = [
        'problem_attachments' => 'array', // Convert JSON to array
    ];

    /**
     * Relationship: A problem belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function solutions()
    {
        return $this->hasMany(Solutions::class, 'problem_id'); // 👈 Add this if not already
    }

    public function getAttachmentsAttribute()
{
    if (empty($this->problem_attachments)) {
        return [];
    }

    if (is_array($this->problem_attachments)) {
        return $this->problem_attachments;
    }

    return json_decode($this->problem_attachments, true) ?: [];
}

}
