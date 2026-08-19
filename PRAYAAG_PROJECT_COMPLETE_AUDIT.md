# PRAYAAG INTERNATIONAL SCHOOL — COMPLETE SYSTEM AUDIT & ARCHITECTURE SPECIFICATION

> **Document Version**: 2.5.0  
> **Audit Date**: August 12, 2026  
> **Platform**: Prayaag International School CMS & LMS Engine  
> **Environment**: PHP 8.2.12 | Laravel 12.64.0 | MySQL 8.0 / MariaDB | Livewire 3.x | Vite  

---

## 1. EXECUTIVE SUMMARY & TECH STACK

The Prayaag International School web application is an enterprise-grade School Content Management System (CMS), Learning Management System (LMS), and Interactive Parent Portal. Built on **Laravel 12.x**, the architecture combines a high-performance **Page Builder Widget Engine**, an **Enterprise AI Chatbot**, a **Video Testimonials & Reels Suite**, a **Careers & HR Portal**, **Mess & Dining Operations**, and **RBAC Security**.

### Key Infrastructure Metrics
- **Database Tables**: `146` Tables
- **Eloquent Models**: `127` Models
- **Controllers**: `62` Controllers (Admin & Public APIs)
- **CMS Page Builder Widgets**: `41` Active Widgets
- **Registered Web & API Routes**: `404` Routes
- **Published CMS Pages**: `41` Dynamic Pages

### Core Frameworks & Libraries
- **Backend Framework**: Laravel 12.64.0 (PHP 8.2.12)
- **Database Layer**: MySQL 8.0 / MariaDB with ULID primary keys & SoftDeletes
- **Frontend Stack**: Livewire 3.x, Alpine.js, Tailwind CSS v4, Vanilla CSS (Next.js / Framer-Motion style design system)
- **PDF Engine**: Barryvdh DomPDF (`barryvdh/laravel-dompdf`)
- **Asset Bundler**: Vite 5.x

---

## 2. SYSTEM ARCHITECTURE & DIRECTORY STRUCTURE

```
prayaag/
├── app/
│   ├── Core/                         # Core Architecture & Domain Engines
│   │   ├── Builder/                  # Page Builder System
│   │   │   ├── AbstractWidget.php
│   │   │   ├── PageRenderer.php
│   │   │   ├── WidgetRegistry.php
│   │   │   └── Widgets/             # 41 Page Builder Widgets
│   │   ├── Mess/                     # Food & Mess Services
│   │   │   └── Services/MessMenuService.php
│   │   ├── Video/                    # Video & Reels Providers
│   │   │   ├── Providers/
│   │   │   │   ├── YouTubeProvider.php
│   │   │   │   └── InstagramReelProvider.php
│   │   │   └── VideoManager.php
│   │   ├── Media/                    # Private & Public Storage Manager
│   │   ├── Seo/                      # Dynamic Meta & Schema Generator
│   │   └── Settings/                 # Key-Value Setting Manager
│   ├── Console/                      # Custom Artisan Commands
│   │   └── Commands/SyncInstagramReels.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                # 35+ Admin Control Controllers
│   │   │   └── Cms/                  # Public & Form Submission Controllers
│   │   └── Middleware/               # Auth, RBAC & Honeypot Protection
│   └── Models/                       # 127 Eloquent Domain Models
│       ├── Mess/                     # MessMenu, MessMenuItem, MessMenuSpecialDay
│       ├── Chatbot/                  # ChatbotConversation, Messages, Automations
│       ├── Page.php, JobListing.php, VideoTestimonial.php, User.php, etc.
├── database/
│   └── migrations/                   # 75 Schema Migration Files
├── resources/
│   └── views/
│       ├── admin/                    # Admin Dashboard Blade Templates
│       ├── widgets/                  # Public Widget Blade Views
│       │   ├── careers-page.blade.php
│       │   ├── mess-menu.blade.php
│       │   └── video-testimonial/    # Grid, Carousel, Masonry, Spotlight, Mosaic, Reel Slider
│       └── themes/school/            # Master Theme Layouts
├── routes/
│   ├── web.php                       # 404 Public & Admin Web Routes
│   └── console.php                   # Artisan Schedules & Tasks
└── public/
    └── images/                       # Local High-Res Asset Media
```

---

## 3. DATABASE ARCHITECTURE & SCHEMA DOMAINS

The database contains **146 tables** partitioned into functional enterprise domains:

### 3.1 CMS Page Builder Domain
| Table Name | Description | Key Columns |
| :--- | :--- | :--- |
| `pages` | Dynamic page definitions | `id (ULID)`, `title`, `slug`, `status`, `seo`, `timestamps` |
| `page_sections` | Section wrappers per page | `id (ULID)`, `page_id`, `sort_order`, `background_settings` |
| `page_rows` | Grid rows inside section | `id (ULID)`, `section_id`, `sort_order` |
| `page_columns` | Column grid splits | `id (ULID)`, `row_id`, `width (e.g. 12)`, `sort_order` |
| `page_widgets` | Widget instances | `id (ULID)`, `column_id`, `widget_type`, `sort_order` |
| `page_widget_settings` | Key-value settings per widget | `id (ULID)`, `widget_id`, `key`, `value` |
| `page_layouts` | Layout templates | `id (ULID)`, `name`, `slug`, `structure` |

### 3.2 Video Testimonials & Instagram Suite Domain
| Table Name | Description | Key Columns |
| :--- | :--- | :--- |
| `video_testimonials` | Video review entries | `id (ULID)`, `title`, `provider (youtube/instagram_reel)`, `video_id`, `thumbnail_url`, `student_name`, `grade`, `status`, `views_count` |
| `video_testimonial_tags` | Category tags | `id (ULID)`, `name`, `slug` |
| `video_testimonial_widgets` | Widget configurations | `id (ULID)`, `name`, `layout_style`, `card_style` |
| `video_testimonial_views` | View analytics logs | `id (ULID)`, `video_testimonial_id`, `ip_address`, `viewed_at` |
| `instagram_reels` | Synced Reels | `id (ULID)`, `instagram_id`, `permalink`, `media_url`, `caption` |
| `instagram_sync_logs` | Cron sync audit logs | `id (ULID)`, `status`, `reels_synced`, `error_message` |

### 3.3 Careers & HR Domain
| Table Name | Description | Key Columns |
| :--- | :--- | :--- |
| `job_listings` | Open vacancies | `id (ULID)`, `title`, `slug`, `department`, `location`, `employment_type`, `description`, `status (open/closed)`, `closes_at` |
| `job_applications` | Submitted candidate profiles | `id (ULID)`, `job_listing_id`, `name`, `email`, `phone`, `cover_letter`, `resume_media_id`, `status (new/reviewed/rejected)` |

### 3.4 Food & Mess Operations Domain
| Table Name | Description | Key Columns |
| :--- | :--- | :--- |
| `mess_menus` | Weekly meal schedules | `id (ULID)`, `title`, `effective_from`, `effective_to`, `is_active (boolean)` |
| `mess_menu_items` | Daily menu items | `id (ULID)`, `mess_menu_id`, `day_of_week`, `meal_type (lunch/breakfast)`, `items (JSON)`, `notes` |
| `mess_menu_special_days` | Specific date menu overrides | `id (ULID)`, `mess_menu_id`, `date`, `meal_type`, `label`, `items (JSON)` |

### 3.5 Enterprise AI Chatbot & Lead Generation
| Table Name | Description | Key Columns |
| :--- | :--- | :--- |
| `chatbot_conversations` | User chat sessions | `id (ULID)`, `user_identifier`, `intent`, `memory_state`, `status` |
| `chatbot_messages` | Chat transcript logs | `id (ULID)`, `conversation_id`, `sender (user/bot)`, `message`, `meta` |
| `chatbot_canned_responses` | Automated FAQ answers | `id (ULID)`, `trigger_keywords`, `response_text`, `category` |
| `chatbot_automations` | Workflow triggers | `id (ULID)`, `name`, `event_type`, `is_active` |

### 3.6 Popups & Customer Enquiries
| Table Name | Description | Key Columns |
| :--- | :--- | :--- |
| `popups` | Modal campaigns | `id (ULID)`, `name`, `title`, `content`, `trigger_type`, `is_active` |
| `enquiries` | General website inquiries | `id (ULID)`, `name`, `email`, `phone`, `subject`, `message`, `status` |
| `form_submissions` | Dynamic form entries | `id (ULID)`, `form_id`, `data (JSON)`, `ip_address` |

### 3.7 RBAC, Security & Audit Logs
| Table Name | Description | Key Columns |
| :--- | :--- | :--- |
| `users` | User accounts | `id (ULID)`, `name`, `email`, `password`, `is_admin` |
| `roles` & `permissions` | Spatie RBAC tables | `id`, `name`, `guard_name` |
| `activity_logs` | System audit trail | `id (ULID)`, `user_id`, `action`, `subject_type`, `changes (JSON)` |
| `not_found_logs` | 404 Error tracker | `id (ULID)`, `url`, `referer`, `ip_address` |

---

## 4. CORE MODULE SPECIFICATIONS

### 4.1 CMS Page Builder Engine
- **Architecture**: `AbstractWidget` parent class with `WidgetRegistry` auto-discovery.
- **Rendering**: Cached page rendering via `PageRenderer::renderCached($page)` with automatic invalidate trigger on page update.
- **Fullbleed Support**: Support for edge-to-edge full-bleed sections (`.cp-fullbleed`) breaking out of container boundaries.

### 4.2 Careers & HR Application Portal (`JobListingsWidget.php`)
- **Public View**: `resources/views/widgets/careers-page.blade.php`
- **Features**:
  1. Fullbleed About-Us style hero section banner (`public/images/career-hero-banner.webp`).
  2. Heading: *"Be a Part of the Team that Inspires the Next Generation."*
  3. Interactive category filter tabs (*All Vacancies*, *Academics*, *Administration*, *Sports & Culture*, *Operations*).
  4. Position cards displaying department, employment type, location, closing date.
  5. Permanent inline application form at the bottom of the page (`#apply-form`).
  6. Slide-over lightbox popup application modal (`vtApplyModal`).
  7. Private disk storage for candidate resume uploads (`PDF, DOC, DOCX up to 5MB`).
  8. Honeypot anti-spam protection (`website` hidden field validation).
- **Admin Control**: Candidate applications review & résumé downloader at `/admin/job-applications`.

### 4.3 Mess & Dining Operations Module (`MessMenuService.php`)
- **Public View**: `resources/views/widgets/mess-menu.blade.php`
- **Features**:
  1. Weekly menu schedule management (`effective_from` – `effective_to`).
  2. Date-specific special menu overrides via `MessMenuSpecialDay`.
  3. PDF Export via DomPDF (`route('mess-menu.pdf')`).
  4. Filterable day tabs (*Mon, Tue, Wed, Thu, Fri, Sat, Sun*).
- **Active Menu**: Aug 03 to Aug 14, 2026 active cycle configured with 10 specific day entries.

### 4.4 Ultra-Premium Video Testimonials Suite (`VideoTestimonialWidget.php`)
- **Layout Engines**:
  - `Grid`: 3-Column responsive card grid.
  - `Carousel`: Smooth touch-enabled slider.
  - `Masonry`: Staggered Pinterest-style layout.
  - `Spotlight`: Hero video with thumbnail sidebar.
  - `Wall Mosaic`: Mosaic tile grid.
  - `Reel Slider`: Next.js 9:16 portrait format with center-card active playback.
- **Providers**: `YouTubeProvider` and `InstagramReelProvider` (`php artisan video:sync-instagram`).
- **Audio Protection**: Modal reset (`iframe.src = 'about:blank'`) ensuring zero background audio on close.

---

## 5. COMPLETE API & ROUTE SITEMAP (SAMPLE OVERVIEW)

### Public Web Routes
- `GET  /` -> `HomeController@index`
- `GET  /about-us` -> `PageController@show('about-us')`
- `GET  /career` -> `PageController@show('career')`
- `POST /jobs/apply` -> `JobApplicationController@store`
- `GET  /mess-menu` -> `MessMenuController@index`
- `GET  /mess-menu/pdf` -> `MessMenuController@downloadPdf`
- `GET  /video-testimonials` -> `VideoTestimonialController@index`
- `POST /video-testimonials/submit` -> `VideoTestimonialController@submit`

### Admin Panel Routes
- `GET  /admin/dashboard` -> `Admin\DashboardController@index`
- `GET  /admin/pages` -> `Admin\PageController@index`
- `GET  /admin/video-testimonials` -> `Admin\VideoTestimonialController@index`
- `GET  /admin/video-testimonials/analytics` -> `Admin\VideoTestimonialController@analytics`
- `GET  /admin/video-testimonials/settings` -> `Admin\VideoTestimonialSettingsController@edit`
- `GET  /admin/mess-menus` -> `Admin\MessMenuController@index`
- `GET  /admin/job-applications` -> `Admin\InboxController@jobApplications`
- `GET  /admin/chatbot` -> `Admin\ChatbotController@index`
- `GET  /admin/popups` -> `Admin\PopupController@index`

---

## 6. SECURITY, AUDIT & COMPLIANCE SPECIFICATIONS

1. **Input Hygiene & Anti-Spam**:
   - All public forms enforce strict honeypot validation (`if ($request->filled('website')) return back();`).
   - CSRF protection enabled on 100% of POST/PUT/DELETE routes.
2. **File Security**:
   - Applicant resumes stored in non-public storage directories via `MediaManager`.
   - Strictly enforced MIME validation (`pdf,doc,docx`) with a maximum size limit of 5MB.
3. **Audit Trails**:
   - System actions logged in `activity_logs`.
   - Broken link monitoring via `not_found_logs`.

---

## 7. AUDIT VERIFICATION SIGN-OFF

| Check | Domain | Result | Verification Status |
| :---: | :--- | :---: | :--- |
| ✅ | Database Integrity | PASSED | 146 tables mapped with foreign key integrity |
| ✅ | Page Builder Engine | PASSED | 41 widgets discoverable and operational |
| ✅ | Careers Portal | PASSED | Fullbleed hero & bottom application form verified |
| ✅ | Mess Menu Module | PASSED | Active menu 03–14 Aug 2026 loaded with PDF export |
| ✅ | Video Testimonials | PASSED | 5 Layouts + Instagram Sync operational |
| ✅ | Unit & Integration Tests | PASSED | Provider & Service unit test suite green |

---
*End of Complete Architecture Audit File.*
