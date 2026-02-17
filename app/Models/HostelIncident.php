<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * HostelIncident Model
 * 
 * Represents an incident reported in a hostel
 */
class HostelIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'reported_by',
        'student_id',
        'title',
        'description',
        'severity',
        'incident_date',
        'status',
        'resolution_notes',
        'resolved_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hostel_id' => 'integer',
        'reported_by' => 'integer',
        'student_id' => 'integer',
        'incident_date' => 'datetime',
        'resolved_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the hostel for this incident.
     */
    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    /**
     * Get the warden who reported this incident.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(HostelWarden::class, 'reported_by');
    }

    /**
     * Get the student involved in this incident.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'student_id');
    }

    /**
     * Scope: Filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Filter by severity.
     */
    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope: Open incidents only.
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['Open', 'In Progress']);
    }

    /**
     * Scope: Order by incident date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('incident_date', 'desc');
    }
}
