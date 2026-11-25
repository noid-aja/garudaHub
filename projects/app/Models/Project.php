<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['client_id',
    'judul',
    'deskripsi',
    'kategori',
    'budget_min',
    'budget_max',
    'batas_lelang',
    'status',])]
class project extends Model
{
    protected function casts(): array{
        return [
            'batas_lelang' => 'date',
            'budget_min' => 'integer',
            'budget_max' => 'integer',
        ];
    }

    public function client(): BelongsTo{
        return $this->belongsTo(User::class, 'client_id');
    }

    public function bids(): HasMany{
        return $this->hasMany(Bid::class, 'project_id');
    }
}
