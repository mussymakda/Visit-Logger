## QR Code Visit Logger

A Laravel + Filament application for managing sponsors with QR codes and tracking interior designer visits.

### Features

- **Admin Panel** (`/admin`): Full system management
  - Sponsor management with QR code generation
  - Interior designer user management
  - Visit tracking and analytics
  - Application settings configuration

- **Designer Panel** (`/designer`): Interior designer interface
  - QR code scanning for visits
  - Visit logging with photos
  - Personal visit history

### Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Set up environment: Copy `.env.example` to `.env` and configure database
4. Generate app key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Create storage link: `php artisan storage:link`
7. Create admin user: `php artisan make:filament-user`

### Usage

#### Admin Login
- URL: `/admin`
- Email: `admin@admin.com`
- Password: `password`

#### Creating Sponsors
1. Navigate to Admin Panel → Sponsors
2. Click "New Sponsor"
3. Fill in sponsor details (QR codes are auto-generated)
4. View/Download QR codes from the sponsors table

#### Interior Designer Workflow
1. Admin creates interior designer users
2. Designer logs in at `/designer`
3. Uses QR scanner to scan sponsor QR codes
4. Uploads site photos and logs visits

### Technical Details

- **Laravel 12** with **Filament 3**
- **MySQL** database
- **QR Code generation** with SimpleSoftwareIO
- **Image handling** with Intervention Image
- **Role-based access control**

### Database Schema

- **Users**: name, email, password, role (admin/interior_designer)
- **Sponsors**: name, company_name, contact, location, description, qr_code, qr_code_path
- **Visits**: user_id, sponsor_id, photo, notes, visit_location, visited_at
- **Settings**: app_name, app_logo, favicon, footer_text

### Development

Start the development server:
```bash
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

- Admin Panel: `http://127.0.0.1:8000/admin`
- Designer Panel: `http://127.0.0.1:8000/designer`

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
