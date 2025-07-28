## Copilot Instructions for PWA for WP

### Project Overview
- WordPress plugin for Progressive Web App (PWA) features: offline caching, service workers, push notifications, banners, and deep WP/AMP integration.
- Major components: `admin/` (settings, dashboard), `service-work/` (service worker, file creation, push notification), `3rd-party/` (compatibility), `assets/` (JS/CSS).

### Key Architectural Patterns
- **Service Worker/Manifest:** Generated dynamically from settings (`service-work/class-pwaforwp-file-creation.php`, `class-pwaforwp-service-worker.php`).
- **Settings API:** All options managed via WordPress Settings API in `admin/settings.php`. Tabs: Dashboard, General, Features, Tools, Advanced, Help.
- **Visibility Targeting:** Enable/disable PWA features by post type, taxonomy, user type, etc.
- **Caching Strategies:** Customizable per asset type (HTML, JS/CSS, images, fonts).
- **Push Notifications:** Multiple providers (PushNotifications.io, FCM, OneSignal, Pushnami, Webpushr).
- **Multisite/AMP Support:** Dynamic file naming and URL handling for multisite and AMP.

### Developer Workflows
- **Activation/Deactivation:** Triggers rewrite rule flush and file creation/deletion (`pwa-for-wp.php`).
- **Settings Changes:** May regenerate service worker/manifest and update pre-caching.
- **Debugging:** Use Dashboard tab for status; admin notices for setup/errors.
- **Custom Assets:** Add JS/CSS to `assets/` and register via settings/hooks.
- **Push Notification Extensions:** Extend via `service-work/class-pwaforwp-push-notification.php` and `3rd-party/`.

### Project-Specific Conventions
- **File Naming:** Service worker/manifest files use dynamic names for multisite/AMP.
- **Settings Structure:** All options in `pwaforwp_settings` array.
- **Icon/Screenshot:** PNG, specific sizes (see settings UI).
- **Banner Customization:** Configurable via settings.
- **Pre-caching:** Manual/automatic URLs (posts/pages/custom posts).
- **Role-Based Access:** Super admins can restrict plugin access.

### Integration Points
- **External Providers:** OneSignal, Pushnami, Webpushr, FCM, PushNotifications.io.
- **AMP Plugins:** AMPforWP, AMP by Automattic.
- **CDN Compatibility:** Option to revert asset URLs.

### Example: Adding a New Feature
1. Add logic in the appropriate directory.
2. Register new settings in `admin/settings.php`.
3. Update service worker/manifest generation if needed.
4. Add UI to settings dashboard if user-facing.
5. Document in `README.md` and changelog.

### References
- Main entry: `pwa-for-wp.php`
- Settings/dashboard: `admin/settings.php`
- Service worker: `service-work/class-pwaforwp-service-worker.php`, `class-pwaforwp-file-creation.php`
- Push notification: `service-work/class-pwaforwp-push-notification.php`, `push-notification/`
- 3rd-party: `3rd-party/`
- Assets: `assets/`
- Languages: `languages/`
- Layouts/templates: `layouts/`

---

**Feedback:** If any section is unclear or missing, specify which workflows, conventions, or integration points need more detail.
