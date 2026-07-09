# Changelog

## v1.1.0 (2026-07-08)

### Added
- **Marketing collapsible menu**: Marketing section now uses Alpine.js collapsible submenu with persistent open/close state (localStorage)
- **Popup Manager** in sidebar under Marketing > Popup Manager
- **Popup admin controller** (`App\Http\Controllers\Admin\PopupController`) replacing Livewire components
- **12 admin routes** for Popup CRUD, publish/unpublish, duplicate, analytics, leads, preview
- **Admin views**: `index.blade.php` (dashboard with stats + table), `form.blade.php` (editor with live preview), `analytics.blade.php`, `leads.blade.php`, `preview.blade.php`
- **PopupBuilderServiceProvider** registered in `bootstrap/providers.php` (was previously dormant)
- **Responsive popup runtime CSS v2.0**: Full responsive typography, buttons (min 44px height), images (lazy/retina), safe area support (iPhone notch), prevent overflow/h-scroll, `prefers-reduced-motion` support
- **Responsive popup runtime JS v2.0**: Touch event support, low-end device detection, resize handling, safe area application, fullscreen mode for mobile, device type tracking
- **Admin preview toggle**: Desktop/Tablet/Mobile view buttons in popup editor

### Changed
- `resources/views/admin/layout.blade.php`: Marketing section now Alpine.js collapsible with Popup Manager
- `routes/popup/admin.php`: Replaced Livewire routes with standard controller routes
- `public/css/popup-builder/popup-runtime.css`: Complete rewrite for responsive design
- `public/js/popup-builder/popup-runtime.js`: Complete rewrite with touch + device optimization
- `app/Providers/PopupBuilderServiceProvider.php`: Removed dead `registerAdminMenu()` event listener, removed unused `Route` import

### Fixed
- PopupBuilderServiceProvider now registered in bootstrap/providers.php (was not loaded)
- ULID PK issue (popup_categories.id) still present - models need `HasUlids` trait

### Removed
- Livewire admin routes from `routes/popup/admin.php`
- Dead `admin.menu.ready` event listener from PopupBuilderServiceProvider
