<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * An admin-defined custom widget. Rendered on pages by DynamicWidget, which
 * interpolates the saved `fields` into the `template`.
 *
 * @property string $name
 * @property string $slug
 * @property string $category
 * @property array  $fields
 * @property string $template
 * @property bool   $is_active
 */
class WidgetDefinition extends Model
{
    protected $fillable = ['name', 'slug', 'category', 'fields', 'template', 'is_active'];

    protected $casts = [
        'fields'    => 'array',
        'is_active' => 'boolean',
    ];
}
