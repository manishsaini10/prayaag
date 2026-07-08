# Security Audit (Static Pass)

Static review of the code on disk. Dynamic items — live penetration testing,
auth fuzzing, load behaviour — still require a running, seeded instance and are
listed at the end as run-gated.

Severity: CRITICAL > HIGH > MEDIUM > LOW.

---

## FIXED in this pass

### 1. [CRITICAL] Cross-tenant IDOR via route-model binding
`bootstrap/app.php` registered `ResolveTenant` with `append`, placing it AFTER
`SubstituteBindings` in the web group. Route-model-bound tenant records (e.g.
`{page}` in the editor routes) were therefore resolved BEFORE the tenant was
set. With the old fail-open scope (below), a logged-in user could load another
tenant's page by ID.
**Fix:** changed `append` -> `prepend` for web and api groups, so tenant
resolution runs before binding. (This is also why EditorTest's cross-tenant
case would have failed on first run.)

### 2. [HIGH] TenantScope failed OPEN when no tenant resolved
`TenantScope` only applied the `tenant_id` constraint when a tenant was present;
with none (console, queue, pre-binding, a forgotten resolve) queries returned
EVERY tenant's rows.
**Fix:** constraint now applied unconditionally — null tenant becomes
`tenant_id is null`, matching nothing (fail closed). Legitimate platform-level
queries opt out via `Model::withoutGlobalScope(TenantScope::class)`.

### 3. [HIGH] No brute-force protection on login
`POST /login` had no rate limit.
**Fix:** added `throttle:5,1` (5 attempts/min). Login already uses a generic
error message and is tenant-scoped, so it doesn't leak which tenant an email
belongs to.

### 4. [HIGH] Custom-HTML widget sanitizer (partial)
The `html` widget sanitized via regex — inherently bypassable (malformed markup,
SVG/MathML, mutation XSS).
**Mitigation applied:** widened the stripped-tag set (svg, math, form, template,
frame, applet, …) and now neutralize `javascript:`/`vbscript:`/`data:` URLs on
href/src/xlink:href.
**Still required:** treat regex as a stopgap. Restrict the widget to trusted
authors, and/or pass content through HTMLPurifier, and/or render under a strict
CSP in a sandboxed iframe.

---

## OPEN — needs a decision or follow-up code

### 5. [HIGH] Uploaded files served from the public disk
Media and especially job-application résumés are stored on the `public` disk —
fetchable by anyone with the URL, no auth. URLs are ULID-based (not trivially
enumerable) but this is still PII on an unauthenticated path.
**Recommend:** move résumés (at least) to a private disk; serve via an
authenticated, tenant-scoped download route returning a short-lived signed URL.

### 6. [MEDIUM] Latent mass-assignment exposure
`GalleryImage`, `Slide`, `PageView` use `$guarded = []`; builder nested models
use `$guarded = ['id']`. They're currently only created from controlled
server-side arrays, so not exploitable today — but if any is ever filled from
request data, an attacker controls every column (including `tenant_id`/foreign
keys). **Recommend:** set explicit `$fillable` on these models.

### 7. [MEDIUM] Account-security features absent (by scope)
No password reset, email verification, or 2FA. Acceptable for an MVP; required
before real operator accounts exist.

### 8. [LOW] Analytics PII / consent
`page_views` stores a salted IP hash (good — never raw) and user-agent. If
serving EU users, surface this in a privacy policy / consent banner.

### 9. [LOW] `redirect()->intended('/admin')`
Low open-redirect risk (Laravel constrains intended URLs to the app host).
Noted for completeness.

---

## Verified OK
- Public forms (enquiry, job apply, subscribe): server validation + honeypot +
  `throttle:10,1`.
- CSRF: web group covers form posts; the editor sends `X-CSRF-TOKEN`.
- Admin views escape all output via Blade `{{ }}` — stored fields (enquiry
  message, subscriber source) are not rendered raw.
- File-upload validation on résumés: `mimes:pdf,doc,docx`, `max:5120`. Stored
  filename is a generated ULID (no path traversal from the original name).
- Tenant isolation now applied consistently and fails closed.

## Run-gated (cannot be done statically)
- Live IDOR/authorization fuzzing across tenants over HTTP.
- XSS payload testing against the (hardened) HTML widget in a real browser.
- Session/cookie flags, HTTPS/HSTS, security headers under the real web server.
- Dependency CVE scan (`composer audit`) — run once dependencies are installed.
