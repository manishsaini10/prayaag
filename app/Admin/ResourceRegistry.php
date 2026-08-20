<?php

namespace App\Admin;

use App\Models\AcademicCalendarEntry;
use App\Models\Achievement;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Download;
use App\Models\Event;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\MediaFolder;
use App\Models\Notice;
use App\Models\Permission;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Role;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Models\User;

/**
 * Declarative definitions for every model-backed admin module. The generic
 * ResourceController reads these to render list tables, build create/edit forms,
 * validate input, and persist changes — so a new CRUD module is a config entry,
 * not a new controller. Columns drive the index table; fields drive the form.
 */
class ResourceRegistry
{
    /** @return array<string, array<string, mixed>> */
    public static function all(): array
    {
        return [
            'posts' => [
                'model' => Post::class, 'label' => 'Posts', 'singular' => 'Post', 'icon' => 'pencil',
                'order' => ['created_at', 'desc'], 'search' => ['title', 'slug'],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'category', 'label' => 'Category', 'type' => 'relation', 'attr' => 'name'],
                    ['key' => 'status', 'label' => 'Status', 'type' => 'badge'],
                    ['key' => 'published_at', 'label' => 'Published', 'type' => 'datetime'],
                ],
                'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'slug', 'label' => 'Slug', 'type' => 'slug', 'rules' => 'required|string|max:255', 'unique' => 'posts,slug'],
                    ['key' => 'category_id', 'label' => 'Category', 'type' => 'belongsTo', 'model' => Category::class, 'attr' => 'name', 'rules' => 'nullable'],
                    ['key' => 'excerpt', 'label' => 'Excerpt', 'type' => 'textarea', 'rules' => 'nullable|string'],
                    ['key' => 'body', 'label' => 'Body', 'type' => 'textarea', 'rows' => 10, 'rules' => 'nullable|string'],
                    ['key' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'published' => 'Published'], 'rules' => 'required|in:draft,published'],
                    ['key' => 'published_at', 'label' => 'Publish date', 'type' => 'datetime', 'rules' => 'nullable|date'],
                    ['key' => 'featured_image', 'label' => 'Featured image (path or URL)', 'type' => 'text', 'rules' => 'nullable|string|max:2048'],
                ],
            ],

            'categories' => [
                'model' => Category::class, 'label' => 'Categories', 'singular' => 'Category', 'icon' => 'tag',
                'order' => ['name', 'asc'], 'search' => ['name', 'slug'],
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'slug', 'label' => 'Slug'],
                    ['key' => 'parent', 'label' => 'Parent', 'type' => 'relation', 'attr' => 'name'],
                ],
                'fields' => [
                    ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'slug', 'label' => 'Slug', 'type' => 'slug', 'rules' => 'required|string|max:255', 'unique' => 'categories,slug'],
                    ['key' => 'parent_id', 'label' => 'Parent category', 'type' => 'belongsTo', 'model' => Category::class, 'attr' => 'name', 'rules' => 'nullable'],
                ],
            ],

            'notices' => [
                'model' => Notice::class, 'label' => 'Notices', 'singular' => 'Notice', 'icon' => 'megaphone',
                'order' => ['created_at', 'desc'], 'search' => ['title'],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'is_pinned', 'label' => 'Pinned', 'type' => 'bool'],
                    ['key' => 'starts_at', 'label' => 'Starts', 'type' => 'datetime'],
                    ['key' => 'ends_at', 'label' => 'Ends', 'type' => 'datetime'],
                ],
                'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'body', 'label' => 'Body', 'type' => 'textarea', 'rows' => 6, 'rules' => 'nullable|string'],
                    ['key' => 'starts_at', 'label' => 'Starts at', 'type' => 'datetime', 'rules' => 'nullable|date'],
                    ['key' => 'ends_at', 'label' => 'Ends at', 'type' => 'datetime', 'rules' => 'nullable|date'],
                    ['key' => 'is_pinned', 'label' => 'Pin to top', 'type' => 'bool', 'rules' => 'nullable|boolean'],
                ],
            ],

            'events' => [
                'model' => Event::class, 'label' => 'Events', 'singular' => 'Event', 'icon' => 'calendar',
                'order' => ['starts_at', 'desc'], 'search' => ['title', 'category', 'location'],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'category', 'label' => 'Category', 'type' => 'badge'],
                    ['key' => 'starts_at', 'label' => 'Starts', 'type' => 'datetime'],
                    ['key' => 'ends_at', 'label' => 'Ends', 'type' => 'datetime'],
                    ['key' => 'location', 'label' => 'Location'],
                ],
                'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'slug', 'label' => 'Slug', 'type' => 'slug', 'rules' => 'required|string|max:255', 'unique' => 'events,slug'],
                    ['key' => 'category', 'label' => 'Category', 'type' => 'select', 'options' => Event::categories(), 'rules' => 'required|string|max:100'],
                    ['key' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 6, 'rules' => 'nullable|string'],
                    ['key' => 'starts_at', 'label' => 'Starts at', 'type' => 'datetime', 'rules' => 'required|date'],
                    ['key' => 'ends_at', 'label' => 'Ends at', 'type' => 'datetime', 'rules' => 'nullable|date'],
                    ['key' => 'location', 'label' => 'Location', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                ],
            ],

            'downloads' => [
                'model' => Download::class, 'label' => 'Downloads', 'singular' => 'Download', 'icon' => 'download',
                'order' => ['sort_order', 'asc'], 'search' => ['title', 'category'],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'category', 'label' => 'Category'],
                    ['key' => 'file_type', 'label' => 'Type'],
                    ['key' => 'is_published', 'label' => 'Published', 'type' => 'bool'],
                ],
                'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'category', 'label' => 'Category', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                    ['key' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => 'nullable|string'],
                    ['key' => 'file', 'label' => 'File (path or URL)', 'type' => 'text', 'rules' => 'required|string|max:2048'],
                    ['key' => 'file_type', 'label' => 'File type (pdf, docx…)', 'type' => 'text', 'rules' => 'nullable|string|max:32'],
                    ['key' => 'file_size', 'label' => 'File size (bytes)', 'type' => 'number', 'rules' => 'nullable|integer|min:0'],
                    ['key' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'rules' => 'nullable|integer'],
                    ['key' => 'is_published', 'label' => 'Published', 'type' => 'bool', 'rules' => 'nullable|boolean'],
                ],
            ],

            'testimonials' => [
                'model' => Testimonial::class, 'label' => 'Testimonials', 'singular' => 'Testimonial', 'icon' => 'star',
                'order' => ['sort_order', 'asc'], 'search' => ['author', 'role'],
                'columns' => [
                    ['key' => 'author', 'label' => 'Author'],
                    ['key' => 'role', 'label' => 'Role'],
                    ['key' => 'is_published', 'label' => 'Published', 'type' => 'bool'],
                    ['key' => 'sort_order', 'label' => 'Order'],
                ],
                'fields' => [
                    ['key' => 'author', 'label' => 'Author', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'role', 'label' => 'Role / title', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                    ['key' => 'quote', 'label' => 'Quote', 'type' => 'textarea', 'rows' => 4, 'rules' => 'required|string'],
                    ['key' => 'photo', 'label' => 'Photo (path or URL)', 'type' => 'text', 'rules' => 'nullable|string|max:2048'],
                    ['key' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'rules' => 'nullable|integer'],
                    ['key' => 'is_published', 'label' => 'Published', 'type' => 'bool', 'rules' => 'nullable|boolean'],
                ],
            ],

            'achievements' => [
                'model' => Achievement::class, 'label' => 'Achievements', 'singular' => 'Achievement', 'icon' => 'star',
                'order' => ['sort_order', 'asc'], 'search' => ['title'],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'year', 'label' => 'Year'],
                    ['key' => 'is_published', 'label' => 'Published', 'type' => 'bool'],
                    ['key' => 'sort_order', 'label' => 'Order'],
                ],
                'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'year', 'label' => 'Year', 'type' => 'number', 'rules' => 'nullable|integer|min:1900|max:2200'],
                    ['key' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => 'nullable|string'],
                    ['key' => 'icon', 'label' => 'Icon (path or name)', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                    ['key' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'rules' => 'nullable|integer'],
                    ['key' => 'is_published', 'label' => 'Published', 'type' => 'bool', 'rules' => 'nullable|boolean'],
                ],
            ],

            'galleries' => [
                'model' => Gallery::class, 'label' => 'Gallery', 'singular' => 'Gallery', 'icon' => 'collection',
                'order' => ['created_at', 'desc'], 'search' => ['title', 'slug'],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'slug', 'label' => 'Slug'],
                    ['key' => 'is_published', 'label' => 'Published', 'type' => 'bool'],
                ],
                'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'slug', 'label' => 'Slug', 'type' => 'slug', 'rules' => 'required|string|max:255', 'unique' => 'galleries,slug'],
                    ['key' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => 'nullable|string'],
                    ['key' => 'is_published', 'label' => 'Published', 'type' => 'bool', 'rules' => 'nullable|boolean'],
                ],
            ],

            'sliders' => [
                'model' => Slider::class, 'label' => 'Sliders', 'singular' => 'Slider', 'icon' => 'photo',
                'order' => ['created_at', 'desc'], 'search' => ['title', 'location'],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'location', 'label' => 'Location'],
                    ['key' => 'is_published', 'label' => 'Published', 'type' => 'bool'],
                ],
                'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'location', 'label' => 'Location', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'is_published', 'label' => 'Published', 'type' => 'bool', 'rules' => 'nullable|boolean'],
                ],
            ],

            'academic_calendar' => [
                'model' => AcademicCalendarEntry::class, 'label' => 'Academic Calendar', 'singular' => 'Calendar entry', 'icon' => 'calendar',
                'order' => ['starts_on', 'desc'], 'search' => ['title'],
                'columns' => [
                    ['key' => 'title', 'label' => 'Title'],
                    ['key' => 'type', 'label' => 'Type', 'type' => 'badge'],
                    ['key' => 'starts_on', 'label' => 'Starts', 'type' => 'date'],
                    ['key' => 'ends_on', 'label' => 'Ends', 'type' => 'date'],
                ],
                'fields' => [
                    ['key' => 'title', 'label' => 'Title', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'type', 'label' => 'Type', 'type' => 'select', 'options' => ['event' => 'Event', 'holiday' => 'Holiday', 'term' => 'Term', 'exam' => 'Exam'], 'rules' => 'required|in:event,holiday,term,exam'],
                    ['key' => 'starts_on', 'label' => 'Starts on', 'type' => 'date', 'rules' => 'required|date'],
                    ['key' => 'ends_on', 'label' => 'Ends on', 'type' => 'date', 'rules' => 'nullable|date'],
                    ['key' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rules' => 'nullable|string'],
                ],
            ],

            'media' => [
                'model' => Media::class, 'label' => 'Media Library', 'singular' => 'File', 'icon' => 'photo',
                'order' => ['created_at', 'desc'], 'search' => ['original_name', 'title'],
                'actions' => ['index', 'destroy'],
                'columns' => [
                    ['key' => 'thumb', 'label' => '', 'type' => 'image', 'disk' => 'public'],
                    ['key' => 'original_name', 'label' => 'File'],
                    ['key' => 'mime_type', 'label' => 'Type'],
                    ['key' => 'size', 'label' => 'Size', 'type' => 'bytes'],
                    ['key' => 'created_at', 'label' => 'Uploaded', 'type' => 'datetime'],
                ],
                'fields' => [],
            ],

            'folders' => [
                'model' => MediaFolder::class, 'label' => 'Folders', 'singular' => 'Folder', 'icon' => 'folder',
                'order' => ['name', 'asc'], 'search' => ['name', 'slug'],
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'slug', 'label' => 'Slug'],
                    ['key' => 'parent', 'label' => 'Parent', 'type' => 'relation', 'attr' => 'name'],
                ],
                'fields' => [
                    ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'slug', 'label' => 'Slug', 'type' => 'slug', 'rules' => 'required|string|max:255'],
                    ['key' => 'parent_id', 'label' => 'Parent folder', 'type' => 'belongsTo', 'model' => MediaFolder::class, 'attr' => 'name', 'rules' => 'nullable'],
                    ['key' => 'path', 'label' => 'Path', 'type' => 'text', 'rules' => 'nullable|string|max:2048'],
                ],
            ],

            'users' => [
                'model' => User::class, 'label' => 'Users', 'singular' => 'User', 'icon' => 'users', 'permission' => 'users.view',
                'order' => ['created_at', 'desc'], 'search' => ['name', 'email'],
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'email', 'label' => 'Email'],
                    ['key' => 'created_at', 'label' => 'Joined', 'type' => 'datetime'],
                ],
                'fields' => [
                    ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'rules' => 'required|email|max:255', 'unique' => 'users,email'],
                    ['key' => 'password', 'label' => 'Password', 'type' => 'password', 'rules' => 'nullable|string|min:6', 'rules_create' => 'required|string|min:6'],
                ],
            ],

            'roles' => [
                'model' => Role::class, 'label' => 'Roles', 'singular' => 'Role', 'icon' => 'shield', 'permission' => 'users.view',
                'order' => ['name', 'asc'], 'search' => ['name'],
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'guard_name', 'label' => 'Guard'],
                ],
                'fields' => [
                    ['key' => 'name', 'label' => 'Name', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    ['key' => 'guard_name', 'label' => 'Guard', 'type' => 'text', 'rules' => 'required|string|max:255'],
                ],
            ],

            'permissions' => [
                'model' => Permission::class, 'label' => 'Permissions', 'singular' => 'Permission', 'icon' => 'shield', 'permission' => 'users.view',
                'order' => ['name', 'asc'], 'search' => ['name'],
                'actions' => ['index'],
                'columns' => [
                    ['key' => 'name', 'label' => 'Name'],
                    ['key' => 'guard_name', 'label' => 'Guard'],
                ],
                'fields' => [],
            ],

            'activitylog' => [
                'model' => ActivityLog::class, 'label' => 'Activity Logs', 'singular' => 'Activity', 'icon' => 'bolt',
                'order' => ['created_at', 'desc'], 'search' => ['description', 'log_name'],
                'actions' => ['index'],
                'columns' => [
                    ['key' => 'description', 'label' => 'Activity'],
                    ['key' => 'log_name', 'label' => 'Module', 'type' => 'badge'],
                    ['key' => 'created_at', 'label' => 'When', 'type' => 'datetime'],
                ],
                'fields' => [],
            ],

            'redirects' => [
                'model' => Redirect::class, 'label' => 'Redirects', 'singular' => 'Redirect', 'icon' => 'globe',
                'order' => ['from_path', 'asc'], 'search' => ['from_path', 'to_path'],
                'columns' => [
                    ['key' => 'from_path', 'label' => 'From'],
                    ['key' => 'to_path', 'label' => 'To'],
                    ['key' => 'status_code', 'label' => 'Code', 'type' => 'badge'],
                    ['key' => 'is_active', 'label' => 'Active', 'type' => 'bool'],
                    ['key' => 'hits', 'label' => 'Hits'],
                ],
                'fields' => [
                    ['key' => 'from_path', 'label' => 'From path (e.g. /old-page)', 'type' => 'text', 'rules' => 'required|string|max:2048', 'unique' => 'redirects,from_path'],
                    ['key' => 'to_path', 'label' => 'To path or URL (e.g. /new-page)', 'type' => 'text', 'rules' => 'required|string|max:2048'],
                    ['key' => 'status_code', 'label' => 'Type', 'type' => 'select', 'options' => ['301' => '301 Permanent', '302' => '302 Temporary'], 'rules' => 'required|in:301,302'],
                    ['key' => 'is_active', 'label' => 'Active', 'type' => 'bool', 'rules' => 'nullable|boolean'],
                ],
            ],
        ];
    }

    /** @return array<string, mixed>|null */
    public static function find(string $key): ?array
    {
        return static::all()[$key] ?? null;
    }
}
