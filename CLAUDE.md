# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project overview

EcoRide is a French-language carpooling web app built for the ECF Développeur Web et Web Mobile certification. Stack: plain PHP (no framework) + MySQL via PDO + vanilla HTML/CSS/JS, intended to run under XAMPP. There is no build system, package manager, or automated test suite — see `docs/` for the project's submitted PDFs.

## Local setup (per README.md)

1. Place the repo inside XAMPP's `htdocs/` directory.
2. Start Apache + MySQL from the XAMPP control panel.
3. Create a database named `ecoride_db`, then import `sql/01_schema.sql` followed by `sql/02_seed.sql` (e.g. via phpMyAdmin).
4. Verify credentials in `includes/db_connect.php` — defaults are XAMPP-style: host `127.0.0.1`, user `root`, empty password.
5. Open `http://localhost/EcoRide/index.php`.

The seed creates two users; passwords are bcrypt-hashed in the dump, so log in via the registration flow if you don't have the originals.

## Architecture

The app is a flat collection of `.php` page scripts at the repo root. Each script is the controller, view, and template for one route (e.g. `login.php`, `register.php`, `covoiturages_db.php`, `covoiturage_detail.php`, `participate.php`, `cancel_booking.php`, `create_ride.php`, `profile.php`, `contact.php`). Navigation is via plain links — there is no router.

Shared code lives in `includes/`:

- `db_connect.php` — instantiates `$pdo` (PDO, ERRMODE_EXCEPTION, FETCH_ASSOC). Every page that touches the DB does `require __DIR__ . '/includes/db_connect.php';`.
- `auth.php` — `require_login()` redirects unauthenticated users to `login.php`; `current_user(PDO $pdo)` re-fetches the user row and refreshes `$_SESSION['user']` (used to keep the credit balance in sync).
- `header.php` / `footer.php` — included on every page. `header.php` calls `session_start()` if not already started and conditionally renders the logged-in nav based on `$_SESSION['user']['pseudo']`.

### Domain model (see `sql/01_schema.sql`)

- `users` — `id`, `pseudo`, `email` (unique), `password_hash`, `credits` (default 20).
- `rides` — `driver_id` → `users.id`, depart/arrivee/date/times, `prix`, `places_restantes`, `ecologique` flag.
- `bookings` — `(user_id, ride_id)` is unique; `status` is `'confirmed'` or `'cancelled'`. Cascading FKs on both sides.

### Booking flow invariants (critical when editing `participate.php` / `cancel_booking.php`)

The participate flow is the most fragile part of the app — preserve these properties when changing it:

- A user must be logged in (`require_login()`) and cannot book their own ride (`driver_id !== user_id`).
- Confirming costs **1 credit** and decrements `places_restantes` by 1; cancelling refunds 1 credit and increments `places_restantes` by 1. Always update `$_SESSION['user']['credits']` after the DB write so the header stays in sync.
- `participate.php` GET renders a confirmation page and stores a one-shot CSRF-style token in `$_SESSION['participate_token']`; the POST handler verifies it with `hash_equals` and unsets it. Do not remove this token check.
- The POST handler runs inside a transaction with `SELECT ... FOR UPDATE` on the user, ride, and any existing booking row. The `bookings` table has a `UNIQUE (user_id, ride_id)` constraint, so the "re-book after cancel" case must `UPDATE` the existing row to `'confirmed'` rather than INSERT.
- All error paths must `rollBack()` if `inTransaction()` and redirect back to `covoiturage_detail.php?id=...&error=...`.

### Search/listing

`covoiturages_db.php` builds a parameterized `WHERE` clause from `$_GET` (`depart`, `arrivee`, `date_ride`, `ecologique`, `prix_max`) and whitelists the sort key against `$allowedSort` before injecting it into `ORDER BY`. Keep the whitelist when adding new sort options — never interpolate `$_GET['sort']` directly.

## Conventions

- Always use prepared statements (`$pdo->prepare(...)->execute([...])`) — never string-concatenate user input into SQL.
- Escape output with `htmlspecialchars(...)` in templates; the codebase does this consistently.
- Hash passwords with `password_hash($pw, PASSWORD_DEFAULT)` and verify with `password_verify`.
- Pages that need a session call `session_start()` themselves (or rely on `header.php` / `auth.php` to do so) — check the start of each script before adding session reads.
- UI strings, comments, and commit messages are French.

## Assets

- `assets/css/style.css` — site-wide stylesheet, referenced as `assets/css/style.css` from every page.
- `assets/validation.js` — client-side password validation for the registration form (also duplicated inline in `register.php`).
- `docs/` — graded deliverables (Documentation Technique, Manuel Utilisateur, Documentation Déploiement) as PDFs. Treat as read-only artifacts.
