<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    
     protected $fillable = [
        'name', 'email', 'password', 'avatar', 'plan', 'upload_count',
    ];
 
    protected $hidden = [
        'password', 'remember_token',
    ];
 
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'upload_count'      => 'integer',
    ];
  
 
    // ═══════════════════════════════════════
    // RELATIONSHIPS
    // ═══════════════════════════════════════
 
    /**
     * All resumes for this user (newest first).
     * Scopes defined on the Resume model are chainable:
     *   $user->resumes()->analyzed()->count()
     *   $user->resumes()->pending()->count()
     */
    public function resumes()
    {
        return $this->hasMany(Resume::class)->latest();
    }
 
    // ═══════════════════════════════════════
    // ACCESSORS
    // ═══════════════════════════════════════
 
    /** "JD" initials from "Jane Doe" — used for avatar placeholder */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        if (count($words) >= 2) {
            return strtoupper($words[0][0] . $words[1][0]);
        }
        return strtoupper(substr($this->name, 0, 2));
    }
 
    /** Is this a pro plan user? */
    public function getIsProAttribute(): bool
    {
        return $this->plan === 'pro';
    }
 
    // ═══════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════
 
    /**
     * Free plan = max 5 uploads. Pro = unlimited.
     * Called in ResumeController::upload() before accepting file.
     */
    public function canUpload(): bool
    {
        if ($this->plan === 'pro') return true;
        return $this->upload_count < 5;
    }
 
    public function remainingUploads(): int|string
    {
        if ($this->plan === 'pro') return '∞';
        return max(0, 5 - $this->upload_count);
    }
}
