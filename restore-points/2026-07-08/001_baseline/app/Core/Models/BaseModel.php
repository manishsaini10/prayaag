<?php

namespace App\Core\Models;

use App\Core\Concerns\RecordsActivity;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Base for every domain model. Establishes the project-wide
 * conventions: ULID primary keys, soft deletes, and auditing.
 */
abstract class BaseModel extends Model
{
    use HasUlids;
    use SoftDeletes;
    use RecordsActivity;

    protected $guarded = ['id'];
}
