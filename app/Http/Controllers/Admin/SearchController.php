<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Media;
use App\Models\Notice;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Powers the ⌘K command palette's live search. Queries the most-searched
 * models and returns a flat, grouped result list. Users are only included
 * when the viewer holds the users.view permission.
 */
class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $like = '%' . $q . '%';
        $results = [];

        foreach (Page::where('title', 'like', $like)->orWhere('slug', 'like', $like)->limit(5)->get(['id', 'title', 'slug']) as $p) {
            $results[] = ['label' => $p->title, 'sub' => '/' . $p->slug, 'group' => 'Pages', 'href' => url('/admin/pages/' . $p->id . '/edit')];
        }
        foreach (Post::where('title', 'like', $like)->orWhere('slug', 'like', $like)->limit(5)->get(['id', 'title', 'status']) as $p) {
            $results[] = ['label' => $p->title, 'sub' => $p->status, 'group' => 'Posts', 'href' => url('/admin/m/posts/' . $p->id . '/edit')];
        }
        foreach (Event::where('title', 'like', $like)->limit(5)->get(['id', 'title']) as $e) {
            $results[] = ['label' => $e->title, 'sub' => 'Event', 'group' => 'Events', 'href' => url('/admin/m/events/' . $e->id . '/edit')];
        }
        foreach (Notice::where('title', 'like', $like)->limit(5)->get(['id', 'title']) as $n) {
            $results[] = ['label' => $n->title, 'sub' => 'Notice', 'group' => 'Notices', 'href' => url('/admin/m/notices/' . $n->id . '/edit')];
        }
        foreach (Media::where('original_name', 'like', $like)->orWhere('title', 'like', $like)->limit(5)->get(['id', 'original_name']) as $m) {
            $results[] = ['label' => $m->original_name, 'sub' => 'Media', 'group' => 'Media', 'href' => url('/admin/m/media')];
        }

        if (Gate::allows('users.view')) {
            foreach (User::where('name', 'like', $like)->orWhere('email', 'like', $like)->limit(5)->get(['id', 'name', 'email']) as $u) {
                $results[] = ['label' => $u->name, 'sub' => $u->email, 'group' => 'Users', 'href' => url('/admin/m/users/' . $u->id . '/edit')];
            }
        }

        return response()->json(['results' => $results]);
    }
}
