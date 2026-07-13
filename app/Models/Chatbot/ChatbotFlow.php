<?php

namespace App\Models\Chatbot;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class ChatbotFlow extends Model
{
    use HasUlids;

    protected $table = 'chatbot_flows';

    protected $guarded = ['id'];

    protected $casts = [
        'flow_data' => 'array',
        'is_active' => 'boolean',
    ];
}
