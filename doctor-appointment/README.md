# Doctor Appointment Booking (Local PHP + MySQL)

**What this is:** A simple modular web app (HTML/CSS/JS + PHP) for booking doctor appointments.  
Intended to run locally on your PC with a MySQL server.

## Structure
- `public/` - frontend files (HTML, CSS, JS)
- `api/` - PHP backend endpoints and DB connection
- `sql/` - database schema + sample data
- `assets/` - images and other static assets
- `README.md` - this file

## Quick setup
1. Install PHP (>=7.4) and MySQL locally.
2. Create a MySQL database, e.g. `doctor_app`.
3. Import `sql/schema.sql` into the database.
4. Edit `api/db.php` and update DB credentials.
5. Start PHP built-in server from project root:
   ```
   php -S localhost:8000 -t public
   ```
6. Open `http://localhost:8000` in your browser.

## Default sample users (from seed SQL)
- Admin: admin@example.com / password: admin123
- Patient: patient1@example.com / password: patient123
- Doctor: doc1@example.com / password: doctor123

## Notes
- This is a starter project (educational). For production, use stronger security, password hashing with bcrypt, HTTPS, CSRF protection, prepared statements (already used), and input validation.
- Feel free to modify and expand functionality (email reminders, calendar integration, nicer UI).
