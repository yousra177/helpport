<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solutions extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'solutions'; // Ensure table name is correct

    protected $fillable = ['content', 'solution_attachments', 'problem_id', 'user_id'];

    protected $casts = ['solution_attachments' => 'array']; // Store attachments as an array
    protected $dates = ['deleted_at']; // Soft delete column

    public function problem()
{
    return $this->belongsTo(Problems::class, 'problem_id'); // 👈 important!
}
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'solution_id');
    }

}
