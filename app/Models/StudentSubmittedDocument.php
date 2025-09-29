<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubmittedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scholarship_id',
        'document_category',
        'document_name',
        'file_path',
        'original_filename',
        'file_type',
        'file_size',
        'is_mandatory',
        'description',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'file_size' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    // Scopes
    public function scopeSfaoRequired($query)
    {
        return $query->where('document_category', 'sfao_required');
    }

    public function scopeScholarshipRequired($query)
    {
        return $query->where('document_category', 'scholarship_required');
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_mandatory', false);
    }

    public function scopeByUserAndScholarship($query, $userId, $scholarshipId)
    {
        return $query->where('user_id', $userId)->where('scholarship_id', $scholarshipId);
    }

    // Helper methods
    public function getDocumentCategoryDisplayName()
    {
        return match($this->document_category) {
            'sfao_required' => 'SFAO Required Document',
            'scholarship_required' => 'Scholarship Required Document',
            default => 'Unknown'
        };
    }

    public function getFileSizeFormatted()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFileTypeDisplayName()
    {
        return match(strtolower($this->file_type)) {
            'pdf' => 'PDF Document',
            'jpg', 'jpeg' => 'JPEG Image',
            'png' => 'PNG Image',
            'gif' => 'GIF Image',
            default => strtoupper($this->file_type) . ' File'
        };
    }

    public function getMandatoryStatusDisplayName()
    {
        return $this->is_mandatory ? 'Required' : 'Optional';
    }
}