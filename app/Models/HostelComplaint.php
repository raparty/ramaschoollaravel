<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * HostelComplaint Model
 * 
 * Represents student complaints in a hostel
 */
class HostelComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'hostel_id',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'response',
        'responded_by',
        'responded_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'hostel_id' => 'integer',
        'responded_by' => 'integer',
        'responded_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the student who filed this complaint.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'student_id');
    }

    /**
     * Get the hostel for this complaint.
     */
    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    /**
     * Get the warden who responded to this complaint.
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(HostelWarden::class, 'responded_by');
    }

    /**
     * Scope: Filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Pending complaints.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['Submitted', 'Under Review']);
    }

    /**
     * Scope: Order by created date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
