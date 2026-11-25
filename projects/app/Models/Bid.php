<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'developer_id',
    'harga',
    'durasi',
    'proposal',
    'is_winner',
])]
class Bid extends Model
{
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'durasi' => 'integer',
            'is_winner' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }
}
