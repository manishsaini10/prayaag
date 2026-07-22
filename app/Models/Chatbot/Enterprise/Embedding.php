<?php

namespace App\Models\Chatbot\Enterprise;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Embedding extends Model
{
    use HasUlids;

    protected $table = 'chatbot_embeddings';

    protected $guarded = ['id'];

    protected $casts = [
        'embedding_vector' => 'array',
        'dimensions' => 'integer',
        'token_count' => 'integer',
    ];
}
