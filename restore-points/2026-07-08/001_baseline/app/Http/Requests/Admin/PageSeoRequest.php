<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates per-page SEO overrides. All fields are optional — anything left
 * blank falls back to SeoManager's auto-generation chain. Returns a clean
 * `seo` array (empty strings stripped) ready to persist to Page::$seo.
 */
class PageSeoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Route is already behind auth; RBAC is enforced via Gate::before.
        return true;
    }

    public function rules(): array
    {
        return [
            'seo.title'               => ['nullable', 'string', 'max:255'],
            'seo.description'         => ['nullable', 'string', 'max:320'],
            'seo.keywords'            => ['nullable', 'string', 'max:255'],
            'seo.canonical'           => ['nullable', 'url', 'max:255'],
            'seo.og_type'             => ['nullable', 'string', 'in:website,article,profile'],
            'seo.schema_type'         => ['nullable', 'string', 'max:40'],
            'seo.og_title'            => ['nullable', 'string', 'max:160'],
            'seo.og_description'      => ['nullable', 'string', 'max:320'],
            'seo.og_image'            => ['nullable', 'url', 'max:500'],
            'seo.twitter_title'       => ['nullable', 'string', 'max:160'],
            'seo.twitter_description' => ['nullable', 'string', 'max:320'],
            'seo.twitter_image'       => ['nullable', 'url', 'max:500'],
            'seo.robots_raw'          => ['nullable', 'string', 'max:120'],
            'seo.robots_index'        => ['nullable', 'boolean'],
            'seo.robots_follow'       => ['nullable', 'boolean'],
        ];
    }

    /**
     * The cleaned SEO array to persist: empty strings removed so SeoManager
     * falls back to auto-generation; robots flags normalised to booleans.
     *
     * @return array<string, mixed>
     */
    public function seoData(): array
    {
        $in = (array) $this->input('seo', []);

        $out = [];
        foreach (['title', 'description', 'keywords', 'canonical', 'og_type', 'schema_type', 'og_title', 'og_description', 'og_image', 'twitter_title', 'twitter_description', 'twitter_image'] as $k) {
            $v = trim((string) ($in[$k] ?? ''));
            if ($v !== '') {
                $out[$k] = $v;
            }
        }

        // Raw robots override maps to the manager's `robots` key.
        $raw = trim((string) ($in['robots_raw'] ?? ''));
        if ($raw !== '') {
            $out['robots'] = $raw;
        }

        // Checkboxes: present + truthy = true; default true (so unchecked = false
        // only persists when the user explicitly unchecks).
        $out['robots_index']  = $this->boolean('seo.robots_index');
        $out['robots_follow'] = $this->boolean('seo.robots_follow');

        return $out;
    }
}
