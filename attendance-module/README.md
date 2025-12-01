# Attendance Module (Laravel)

This module implements the hybrid attendance strategy discussed (ephemeral QR + remote fallback + presence beacons) and is ready to drop into a Laravel application that already relies on an LMS for identity.

## Contents

- Database migrations for `training_sessions`, `attendances`, and `attendance_events`
- Models, services, controllers, middleware, and form requests
- Blade views for trainer dashboards and trainee check-in
- Front-end module (`resources/js/modules/attendance.js`) handling QR scans, remote challenges, and beacon heartbeats
- Seeder + feature test skeleton

## Installation

1. Copy the `attendance-module` contents into your Laravel project.
2. Register the middleware in `app/Http/Kernel.php` and routes in `routes/web.php`.
3. Publish the JavaScript module via your bundler (Vite/Mix) and include it where needed.
4. Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed --class=TrainingSessionSeeder
```

5. Wire up LMS SSO to the `auth:lms` guard referenced in the routes.

## Routes

See `routes/web.php` snippet provided in the documentation or adapt the controller methods to your existing routing file.

## Tests

Run the included feature test once you adapt namespaces:

```bash
php artisan test --filter=AttendanceFlowTest
```
