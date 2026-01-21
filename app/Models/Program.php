<?php

namespace App\Models;

use App\Enums\ProgramStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Program extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => ProgramStatus::class,
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function catatans(): HasMany
    {
        return $this->hasMany(Catatan::class);
    }

    public function beritaAcara(): HasOne
    {
        return $this->hasOne(BeritaAcara::class);
    }
}
