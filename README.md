# Detail Lab - Appointment Booking System

## Overview
This is a PHP-based web application for booking car detailing appointments, built with a layered architecture (data layer, controller, viewer, entry point, configuration). It uses MySQL and runs on XAMPP.

## Requirements
- XAMPP (Apache, MySQL) installed.
- PHP 8.2+.
- MySQL/MariaDB.

## Installation
1. **Copy Project**:
   - Place the `detail_lab` folder in `C:\xampp\htdocs\projects\` (or your XAMPP `htdocs` equivalent).
   - Path: `C:\xampp\htdocs\projects\detail_lab\`.

2. **Set Up Database**:
   - Open `http://localhost/phpmyadmin`.
   - Create a database named `detail_lab`.
   - Import `detail_lab.sql` (in the project root) to create tables and sample data.

3. **Start XAMPP**:
   - Launch XAMPP Control Panel.
   - Start Apache and MySQL.

4. **Access the Site**:
   - Open a browser and go to `http://localhost/projects/detail_lab/public/index.php`.
   - Navigate using links:
     - Home: `?action=index`
     - Services: `?action=services`
     - Book Now: `?action=book`
     - Admin: `?action=admin` (for CRUD operations).

## Testing
- **Booking**:
  - Go to Book Now.
  - Select a date and time (8:00 AM–5:30 PM, 30-min intervals).
  - For today’s date, past times are disabled (e.g., if 2:00 PM, 8:00 AM–1:30 PM grayed out).
  - For future dates (e.g., 2025-04-14), all times are available.
  - Fill details (e.g., Name: Test User, Email: test@example.com, Phone: 123-456-7890, Address: 123 St, Package: Bronze).
  - Submit → Shows a green message ("Appointment booked successfully!") and clears the form, or a red message (e.g., "Selected time slot is already booked") if the time conflicts.
  - Try booking the same time again → Expect an error message.
- **Admin**:
  - Go to Admin.
  - Add, update, or delete clients, services, appointments.
  - Update an appointment’s status (e.g., to "Confirmed").
  - Verify success messages and table updates.
- **Navigation**:
  - Test all links (Home, Services, Book Now, Admin).

## Notes
- No configuration changes (e.g., `httpd.conf`, `AllowOverride`) are required.
- The project uses a default XAMPP setup.
- Database credentials are in `config/database.php` (default: host=localhost, user=root, password="", database=detail_lab).
- Booking feedback appears as on-page messages, not redirects.
- Time conflicts are checked on submission, preventing double-booking.
- The time dropdown enables all times for future dates, with only past times disabled for the current date.
- Implemented a basic authentication system using PHP and sessions.
- Created a LoginController.php to handle login and logout logic securely.
- Added LoginDB.php for verifying admin credentials against the database.
- Updated header.php to:
  Display login form when not authenticated.
  Show logout button and "Admin" navigation link when logged in.
  Show login error messages if credentials are incorrect.
- Modified index.php to route login and logout requests through LoginController.
- Restricted access to the admin page via ?action=admin to logged-in users only.
- Added session checks and session regeneration to improve security.
- Contact Carlos or Niyo for issues.

## Structure
- `config/`: Database settings.
- `data/`: Data layer (database operations).
- `controllers/`: Business logic.
- `views/`: UI templates (header, footer, pages).
- `public/`: Web-accessible entry point and assets.
- `detail_lab.sql`: Database schema.