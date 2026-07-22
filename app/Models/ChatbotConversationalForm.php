<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotConversationalForm extends Model
{
    use HasFactory;

    protected $table = 'chatbot_conversational_forms';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'collected_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
