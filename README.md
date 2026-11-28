# php-native-crud

A clean, reusable **PHP (native, no framework) + MySQL CRUD starter** — student records management with PDO prepared statements, a tiny router-free page structure, and a Bootstrap 5 UI. Built as a learning starter and a real, usable admin panel: copy it, point it at your database, and it works.

> From the portfolio of **Muhammad Ridha Rezeki** — Informatics Engineering student, Politeknik Negeri Lhokseumawe. See also [ai-response-ranker](https://github.com/MuhammadRidhaRezeki/ai-response-ranker).

## Features

- Full CRUD for students: list (with search), create, edit, delete
- **Paginated list view** — 10 students per page, with page numbers and Previous/Next links that keep the active search
- **PDO + prepared statements everywhere** — no raw SQL interpolation
- Server-side validation (required fields, unique NIM & email, valid email format)
- Flash messages (success / error) via session
- Bootstrap 5 UI, responsive, no build step
- SQL schema + seed data included — import and go
- Works on **XAMPP / Laragon / any PHP 8.1+ + MySQL/MariaDB** stack

## Requirements

- PHP **8.1+** (with `pdo_mysql`)
- MySQL **8.0+** or MariaDB **10.6+**
- A web server (Apache via XAMPP is the easiest path)

## Quick start (XAMPP)

1. Clone or download this repo into your web root, e.g. `C:\xampp\htdocs\php-native-crud`.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`) and import, in order:
   - `database/schema.sql` — creates the `php_native_crud` database and the `students` table
   - `database/seed.sql` — inserts 5 sample students
4. Check the credentials in `config/database.php` (defaults: host `localhost`, user `root`, empty password — the XAMPP defaults).
5. Open `http://localhost/php-native-crud/public/` in your browser.

## Configuration

All database settings live in one place — `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'php_native_crud');
define('DB_USER', 'root');
define('DB_PASS', '');
```

For production, move these to environment variables and never commit real credentials.

## Project structure

```
php-native-crud/
├── config/
│   └── database.php      # PDO connection (single place for credentials)
├── database/
│   ├── schema.sql        # Database + students table
│   └── seed.sql          # 5 sample students
├── includes/
│   ├── header.php        # Shared <head>, navbar, flash-message display
│   └── footer.php        # Shared footer + Bootstrap JS
├── public/
│   ├── index.php         # List students + search
│   ├── create.php        # Add a student
│   ├── edit.php          # Edit a student
│   ├── delete.php        # Delete a student (POST only)
│   └── assets/
│       └── style.css     # Small custom tweaks on top of Bootstrap
└── src/
    └── Student.php       # Student model: all CRUD queries (PDO, prepared)
```

## How it works

- **`src/Student.php`** is the only file that talks to the database. Every query uses a prepared statement with bound parameters.
- **Pages in `public/`** handle one job each: read input, validate, call the model, redirect with a flash message (Post/Redirect/Get — refreshing never re-submits a form).
- **`includes/header.php`** starts the session, shows flash messages, and renders the navbar; every page includes it.

## Security notes

- All output is escaped with `htmlspecialchars()` (XSS protection).
- Deletes are **POST-only** with a confirmation dialog — no destructive GET links.
- NIM and email are unique at the database level; duplicates are caught and reported as friendly errors.

## Roadmap

- [x] Pagination on the list view (10 per page, search-preserving links)
- [ ] Sortable columns
- [ ] CSV export of the student list
- [ ] Login (admin-only access) with password hashing

## License

MIT — see [LICENSE](LICENSE).
