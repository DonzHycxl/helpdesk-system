<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    protected $fillable = [
        'contact_email',
        'name',
        'datetime_reported',
        'datetime_action',
        'datetime_closed',
        'due_date',
        'subject',
        'description',
        'department_id',
        'priority_id',
        'status_id',
        'assigned_to_user_id',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'datetime_reported' => 'datetime',
            'datetime_action' => 'datetime',
            'datetime_closed' => 'datetime',
            'due_date' => 'datetime',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }
}
