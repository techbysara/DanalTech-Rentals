# DanalTech-Equipment-Rentals

## DanalTech Rentals (DTR)
> Rent smart. Work better. Zero stress.

A web-based equipment rental management system built with PHP, MySQL, Bootstrap 5 and custom CSS. Developed as coursework for CMM007 (Intranet Systems Development) at Robert Gordon University.

> **Learning project, not production software.** This was built under a coursework deadline as my first end-to-end full-stack build. It's a genuine, working system, not a toy demo, but it hasn't had a professional security review and isn't intended to handle real customer data, real payments, or real accounts. Treat it as a portfolio piece and a snapshot of where I started. I've written more about that here: [Building DanalTech Rentals: What My First Full-Stack Project Taught Me](https://mizamie.com/blog/danaltech-build-story).

## Tech Stack
- PHP, MySQL, XAMPP
- Bootstrap 5, Custom CSS, JavaScript

## Features
- Role-based login (Admin / User)
- Equipment and user management (CRUD)
- Rent and return system with quantity and availability tracking
- Cart system with per-day pricing
- Rental limits, automatic overdue detection, and account lockout after repeated failed logins
- Light/dark theme toggle
- Security fundamentals: prepared statements throughout (no SQL injection), CSRF tokens, output escaping, password hashing

## Known Limitations
Being upfront about what this project doesn't do yet:
- CSRF tokens are generated and included in every form, but only fully verified on login, registration, and the standalone rent-equipment flow, not yet on every admin/cart action.
- No automated tests.
- Image and asset naming in `images/equipment/` is inconsistent, a leftover of building quickly under deadline.
- No production deployment config; this is built and tested for local XAMPP only.

I'm rebuilding this with cleaner structure and these gaps closed as my skills grow.

## Setup
1. Clone repo into XAMPP htdocs
2. Create a local MySQL database: `danaltech_rentals`
3. Recreate the schema (`users`, `equipment`, `rentals` tables; see `includes/auth.php`, `danaltech-admin/equipment.php`, and `danaltech-admin/rentals.php` for the exact columns each table needs), then copy in your own `config/database.php` with your local DB credentials. It's gitignored, so it won't come with the repo.
4. Visit http://localhost/danaltech_wrentals/

## Disclaimer
This project was built for educational purposes as part of university coursework. It is not affiliated with, and does not represent, any real rental business. No real user data, payment details, or production credentials are included in this repository. All database credentials in the codebase are local development placeholders only.

© 2026 DanalTech Rentals
