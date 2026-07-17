# Schema Dependency Graph

**Generated:** 2026-07-17
**Source:** FK constraints from 65 migration files → SQLite

## Dependency Levels

### Level 0 (Root Tables — no FK dependencies)

- **`academic_sessions`** — root table (referenced by: `academic_terms`, `academic_calendar_entries`)
- **`cache`**, **`cache_locks`** — Laravel system tables
- **`chatbot_flows`**, **`chatbot_settings`**, **`chatbot_visitors`** — chatbot core
- **`chatbot_embeddings`** — polymorphic embeddings
- **`chatbot_companies`**, **`chatbot_lead_sources`**, **`chatbot_pipelines`** — CRM
- **`chatbot_languages`** — translation base
- **`chatbot_tags`** — polymorphic taggable
- **`page_layouts`**, **`theme_components`** — CMS core (no FK deps)
- **`users`**, **`password_reset_tokens`** — auth
- **`jobs`**, **`job_batches`**, **`failed_jobs`** — queue
- **`categories`**, **`notices`**, **`events`** — content
- **`enquiries`**, **`downloads`**, **`galleries`**, **`sliders`** — feature modules
- **`classes`**, **`settings`**, **`setting_groups`** — config/academic
- **`menus`**, **`tags`**, **`forms`**, **`redirects`** — CMS features
- **`not_found_logs`**, **`page_views`** — analytics
- **`personal_access_tokens`**, **`analytics_events`** — auth/analytics
- **`widget_definitions`**, **`admin_notifications`** — admin UI
- **`popup_categories`**, **`popup_templates`**, **`popup_integrations`** — popups (base)
- **`instagram_accounts`** — social media
- **`chatbot_departments`**, **`chatbot_teams`**, **`chatbot_leads`** — chatbot enterprise
- **`api_tokens`** — API management

### Level 1 (Direct children of Level 0)

- **`academic_terms`** → depends on: `academic_sessions`
- **`menu_items`** → depends on: `menus`, `pages` (and self-ref `menu_items`)
- **`posts`** → depends on: `categories`
- **`job_listings`** → root (no FK deps) — moved up
- **`subscribers`** → root
- **`media_folders`** → depends on: `media_folders` (self-ref)
- **`activity_logs`** → polymorphic (no FK deps in schema)
- **`permissions`**, **`roles`** → root
- **`role_user`** → depends on: `roles`, `users`
- **`permission_role`** → depends on: `permissions`, `roles`
- **`testimonials`** → depends on: `users`
- **`chatbot_canned_responses`** → depends on: `chatbot_departments`, `users`
- **`chatbot_form_fields`** → root
- **`chatbot_kb_categories`** → depends on: `chatbot_kb_categories` (self-ref)
- **`chatbot_kb_documents`** → depends on: `chatbot_kb_categories`
- **`chatbot_widget_themes`** → root
- **`chatbot_api_tokens`** → depends on: `users`
- **`chatbot_integrations`** → root

### Level 2

- **`pages`** → depends on: `page_layouts`
- **`media`** → depends on: `media_folders`
- **`chatbot_conversations`** → depends on: `chatbot_visitors`, `users`
- **`chatbot_kb_chunks`** → depends on: `chatbot_kb_documents`
- **`academic_calendar_entries`** → depends on: `academic_sessions`, `academic_terms`, `classes`, `users`
- **`chatbot_contacts`** → depends on: `chatbot_visitors`
- **`chatbot_agent_statuses`** → depends on: `users`

### Level 3

- **`page_sections`** → depends on: `pages`
- **`job_applications`** → depends on: `job_listings`, `media`
- **`chatbot_messages`** → depends on: `chatbot_conversations`
- **`chatbot_translations`** → depends on: `chatbot_languages`
- **`chatbot_pipeline_stages`** → depends on: `chatbot_pipelines`
- **`chatbot_deals`** → depends on: `chatbot_contacts`, `chatbot_companies`, `chatbot_pipelines`, `chatbot_pipeline_stages`, `users`

### Level 4

- **`page_rows`** → depends on: `page_sections`
- **`form_submissions`** → depends on: `forms`
- **`popups`** → depends on: `popup_categories`, `popup_templates`, `users`, `popup_ab_tests`
- **`chatbot_notifications`** → depends on: `users`
- **`chatbot_notification_channels`** → depends on: `users`
- **`chatbot_analytics_events`** → depends on: `chatbot_visitors`, `users`
- **`chatbot_agent_performance`** → depends on: `users`
- **`chatbot_audit_logs`** → depends on: `users`

### Level 5

- **`page_columns`** → depends on: `page_rows`
- **`popup_rules`**, **`popup_schedules`**, **`popup_leads`**, **`popup_assets`**, **`popup_revisions`** → depends on: `popups` (and `users`)
- **`chatbot_tickets`** → depends on: `chatbot_conversations`, `chatbot_visitors`, `chatbot_contacts`, `chatbot_departments`, `chatbot_teams`, `users`
- **`chatbot_campaigns`** → depends on: `users`
- **`chatbot_automations`** → depends on: `users`
- **`chatbot_webhooks`** → depends on: `users`
- **`chatbot_reports`** → depends on: `users`
- **`chatbot_notes`** → depends on: `users`
- **`chatbot_activities`** → depends on: `users`
- **`instagram_media`** → depends on: `instagram_accounts`

### Level 6

- **`page_widgets`** → depends on: `page_columns`
- **`page_widget_settings`** → depends on: `page_widgets`
- **`gallery_images`** → depends on: `galleries`
- **`slides`** → depends on: `sliders`
- **`popup_analytics`** → depends on: `popups`, `popup_ab_test_variants`
- **`popup_ab_test_variants`** → depends on: `popup_ab_tests`
- **`popup_integration_logs`** → depends on: `popup_integrations`, `popups`
- **`popup_activity_logs`** → depends on: `popups`, `users`
- **`chatbot_ticket_replies`** → depends on: `chatbot_tickets`, `users`
- **`chatbot_campaign_logs`** → depends on: `chatbot_campaigns`
- **`chatbot_automation_logs`** → depends on: `chatbot_automations`
- **`chatbot_webhook_logs`** → depends on: `chatbot_webhooks`
- **`instagram_sync_logs`** → depends on: `instagram_accounts`

### Level 7+

- **`post_tag`** → depends on: `posts`, `tags`
- **`chatbot_department_agent`** → depends on: `chatbot_departments`, `users`
- **`chatbot_team_member`** → depends on: `chatbot_teams`, `users`
- **`chatbot_visitor_sessions`** → depends on: `chatbot_visitors`
- **`chatbot_visitor_devices`** → depends on: `chatbot_visitors`
- **`chatbot_visitor_locations`** → depends on: `chatbot_visitors`
- **`chatbot_visitor_pages`** → depends on: `chatbot_visitors`, `chatbot_visitor_sessions`
- **`chatbot_visitor_events`** → depends on: `chatbot_visitors`, `chatbot_visitor_sessions`
- **`chatbot_typing_status`** → depends on: `chatbot_conversations`
- **`chatbot_read_receipts`** → depends on: `chatbot_messages`, `users`
- **`chatbot_conversation_tags`** → depends on: `chatbot_conversations`
- **`chatbot_company_contact`** → depends on: `chatbot_companies`, `chatbot_contacts`

## Key Dependency Chains

```
users
  └─ role_user
  └─ chatbot_conversations
  └─ chatbot_leads
  └─ chatbot_tickets
  └─ popups, popup_leads, popup_revisions, popup_activity_logs
  └─ chatbot_notifications, chatbot_agent_statuses, chatbot_performance
  └─ chatbots_api_tokens, chatbot_canned_responses

pages → page_sections → page_rows → page_columns → page_widgets → page_widget_settings

chatbot_visitors
  └─ chatbot_conversations → chatbot_messages → chatbot_read_receipts
  └─ chatbot_visitor_sessions → chatbot_visitor_pages, chatbot_visitor_events
  └─ chatbot_visitor_devices, chatbot_visitor_locations
  └─ chatbot_contacts → chatbot_deals
  └─ chatbot_leads

chatbot_departments
  └─ chatbot_department_agent
  └─ chatbot_tickets

popup_categories → popups → popup_rules, popup_schedules, popup_analytics, popup_leads
popup_templates → popups
popup_ab_tests → popup_ab_test_variants → popup_analytics

academic_sessions → academic_terms → academic_calendar_entries
classes → academic_calendar_entries
```

## Summary

| Depth | Tables | Description |
|-------|--------|-------------|
| 0 | ~40 | Root (no FK deps) |
| 1 | ~15 | Direct children of root |
| 2 | ~10 | Depend on level 1 |
| 3 | ~10 | Depend on level 2 |
| 4 | ~15 | Depend on level 3 |
| 5 | ~15 | Depend on level 4 |
| 6 | ~15 | Depend on level 5 |
| 7+ | ~10 | Deepest dependencies |

**Total: 130 tables, 150+ FK relationships**
