<?php

namespace App\Http\Controllers\Admin;

use App\Admin\ResourceRegistry;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * One controller for every model-backed admin module. Behaviour is driven
 * entirely by ResourceRegistry definitions: columns render the list, fields
 * render and validate the form. Adding a module means adding a registry entry.
 */
class ResourceController extends Controller
{
    public function index(Request $request, string $resource): View
    {
        $def = $this->resolve($resource);
        $this->authorizeResource($def);

        $model = $def['model'];
        $query = $model::query();

        // Eager-load relations referenced by columns to avoid N+1.
        $with = collect($def['columns'])->where('type', 'relation')->pluck('key')->all();
        if ($with) {
            $query->with($with);
        }

        // Optional keyword search across the configured columns.
        $q = trim((string) $request->get('q', ''));
        if ($q !== '' && ! empty($def['search'])) {
            $query->where(function ($sub) use ($def, $q) {
                foreach ($def['search'] as $col) {
                    $sub->orWhere($col, 'like', "%{$q}%");
                }
            });
        }

        [$orderCol, $orderDir] = $def['order'] ?? ['created_at', 'desc'];
        $items = $query->orderBy($orderCol, $orderDir)->paginate(15)->withQueryString();

        return view('admin.resource.index', compact('def', 'resource', 'items', 'q'));
    }

    public function create(string $resource): View
    {
        $def = $this->resolve($resource);
        $this->authorizeResource($def);
        $this->ensureAction($def, 'create');

        $model = $def['model'];

        return view('admin.resource.form', [
            'def' => $def, 'resource' => $resource, 'mode' => 'create',
            'item' => new $model, 'options' => $this->options($def),
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $def = $this->resolve($resource);
        $this->authorizeResource($def);
        $this->ensureAction($def, 'store');

        $data = $this->validated($request, $def, true);

        $model = $def['model'];
        $model::create($data);

        return redirect()
            ->route('admin.resource.index', $resource)
            ->with('status', ($def['singular'] ?? 'Item') . ' created.');
    }

    public function edit(string $resource, string $id): View
    {
        $def = $this->resolve($resource);
        $this->authorizeResource($def);
        $this->ensureAction($def, 'edit');

        $item = $def['model']::findOrFail($id);

        return view('admin.resource.form', [
            'def' => $def, 'resource' => $resource, 'mode' => 'edit',
            'item' => $item, 'options' => $this->options($def, $item),
        ]);
    }

    public function update(Request $request, string $resource, string $id): RedirectResponse
    {
        $def = $this->resolve($resource);
        $this->authorizeResource($def);
        $this->ensureAction($def, 'update');

        $item = $def['model']::findOrFail($id);
        $data = $this->validated($request, $def, false, $id);

        $item->fill($data)->save();

        return redirect()
            ->route('admin.resource.index', $resource)
            ->with('status', ($def['singular'] ?? 'Item') . ' updated.');
    }

    public function destroy(string $resource, string $id): RedirectResponse
    {
        $def = $this->resolve($resource);
        $this->authorizeResource($def);
        $this->ensureAction($def, 'destroy');

        $def['model']::findOrFail($id)->delete();

        return back()->with('status', ($def['singular'] ?? 'Item') . ' deleted.');
    }

    /* ----------------------------------------------------------------- */

    /** @return array<string, mixed> */
    protected function resolve(string $resource): array
    {
        $def = ResourceRegistry::find($resource);
        abort_if($def === null, 404);

        return $def;
    }

    /** @param array<string, mixed> $def */
    protected function authorizeResource(array $def): void
    {
        if (! empty($def['permission'])) {
            abort_unless(Gate::allows($def['permission']), 403);
        }
    }

    /** @param array<string, mixed> $def */
    protected function ensureAction(array $def, string $action): void
    {
        $allowed = $def['actions'] ?? ['index', 'create', 'store', 'edit', 'update', 'destroy'];
        abort_unless(in_array($action, $allowed, true), 403);
    }

    /**
     * Validate the request against the field rules and return persistable data.
     *
     * @param  array<string, mixed>  $def
     * @return array<string, mixed>
     */
    protected function validated(Request $request, array $def, bool $isCreate, ?string $id = null): array
    {
        $rules = [];
        foreach ($def['fields'] as $f) {
            $rule = $isCreate
                ? ($f['rules_create'] ?? $f['rules'] ?? 'nullable')
                : ($f['rules'] ?? 'nullable');

            if (! empty($f['unique'])) {
                [$table, $col] = explode(',', $f['unique']);
                $rule .= $isCreate
                    ? "|unique:{$table},{$col}"
                    : "|unique:{$table},{$col},{$id},id";
            }

            $rules[$f['key']] = $rule;
        }

        $validated = $request->validate($rules);

        $data = [];
        foreach ($def['fields'] as $f) {
            $key = $f['key'];
            $type = $f['type'] ?? 'text';

            if ($type === 'password') {
                if (! empty($validated[$key])) {
                    $data[$key] = $validated[$key]; // hashed by the model cast
                }
                continue;
            }

            if ($type === 'bool') {
                $data[$key] = $request->boolean($key);
                continue;
            }

            $val = $validated[$key] ?? null;
            if ($val === '') {
                $val = null;
            }
            if ($val !== null && in_array($type, ['datetime'], true)) {
                $val = str_replace('T', ' ', $val);
            }

            $data[$key] = $val;
        }

        return $data;
    }

    /**
     * Build <select> option lists for belongsTo fields.
     *
     * @param  array<string, mixed>  $def
     * @return array<string, \Illuminate\Support\Collection>
     */
    protected function options(array $def, ?Model $item = null): array
    {
        $options = [];
        foreach ($def['fields'] as $f) {
            if (($f['type'] ?? '') !== 'belongsTo') {
                continue;
            }
            $related = $f['model'];
            $attr = $f['attr'] ?? 'name';
            $query = $related::query()->orderBy($attr);

            // A record can't be its own parent.
            if ($item && $item->exists && $related === $def['model']) {
                $query->where('id', '!=', $item->getKey());
            }

            $options[$f['key']] = $query->pluck($attr, 'id');
        }

        return $options;
    }
}
