# A Simple Project , Time And Budget Management App

Project Manager is a lightweight and easy-to-use project management application built with Laravel (PHP 8.3) and Filament admin panel.  
Designed primarily for personal use to help you organize projects, tasks, work times, and payments in a clean and efficient way.

---

## Features

- Create, edit, and manage projects with titles, descriptions, statuses (`todo`, `doing`, `done`), estimated and actual durations.
- Define tasks linked to projects, with start/end dates and detailed descriptions.
- Track work times per task with start/end times and notes, supporting Persian (Jalali) date display.
- Record payments received from clients against projects, with a progress bar indicating payment completion.
- User authentication with simple login and registration.
- Responsive UI using Bootstrap and enhanced tables with Yajra DataTables.
- MySQL database backend.

---

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/farhad-deh/project-manager.git
   cd your-project-folder
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   npm run dev
   ```

3. Configure `.env` file (database credentials, etc.)

4. Run migrations:
   ```bash
   php artisan migrate
   ```

5. Install Filament Admin Panel:
   ```bash
   composer require filament/filament:"^3.3" -W
   php artisan filament:install --panels
   ```

6. Create a user (register through the app or seed an admin user).
   ```bash
   php artisan make:filament-user
   ```
   
7. Run the development server:
   ```bash
   php artisan serve
   ```

---

## Usage

- Access the admin panel at `/admin`.
- Manage projects, tasks, work times, and payments via the admin interface.
- Track progress and payments visually with progress bars.

---

## Technologies Used

- Laravel 10+
- PHP 8.3
- Filament Admin Panel
- Bootstrap 5
- Yajra DataTables
- MySQL

---

## Notes

- Dates are stored in Gregorian (Miladi) format internally, while the UI supports Jalali calendar for date input and display.
- This project is intended for personal use or as a lightweight starting point for more complex project management systems.

---
