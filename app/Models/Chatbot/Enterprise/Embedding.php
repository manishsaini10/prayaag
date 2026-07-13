<?php

namespace App\Models\Chatbot\Enterprise;

use App\Core\Models\BaseModel;

class Embedding extends BaseModel
{
    protected $table = 'chatbot_embeddings';

    protected $casts = [
        'embedding_vector' => 'array',
        'dimensions' => 'integer',
        'token_count' => 'integer',
    ];
}
