# DNSC IC IAC - Covenant of Commitment System

A PHP-based Digital Signing System designed to collect digital signatures from partners, generate official stamped PDF certificates, automatically email these documents, and display live updates on a signature wall.

## 🚀 Features
- **Dynamic Multi-Event Architecture**: Admins can create, edit, deactivate, and permanently delete events. Deleting an event safely cascades to remove all associated database records, PDF certificates, and signature files to free up server space.
- **Advanced Event Security & Lockdowns**: Granular control over event signing. Events can be set to **Standby/Upcoming** (locked forms with frosted-glass UI overlays), **Active** (open signing), or **Strict Mode** (signing unlocks *only* on the exact calendar day of the event).
- **Authentication & Roles**: Secure login/registration that supports drawing or uploading signatures. Distinct roles for Partners and Admins.
- **Smart Digital Signature Engine**: Integration of `Signature Pad.js` for drawn signatures and file uploads for existing e-signatures. Advanced bounding algorithms automatically crop, scale, and correctly anchor both tall uploaded images and wide drawn signatures so they perfectly align with PDF lines without vertical bleed.
- **Automated PDF Engine**: Generates professional certificates using FPDF, dynamically stamping the active event title, venue, and date, along with the user's signature and audit metadata.
- **Automated Email Delivery**: Integrates with the **Brevo API** via `.env` configuration to automatically deliver generated PDF certificates to partners upon signing.
- **Live Signature Wall**: A projector-mode screen that auto-refreshes to display new signees live at an event, with randomized scaling and rotation for organic visual spread.
- **Granular Admin Dashboard**: Real-time stats, submission management, and CSV data exports that are all dynamically filterable by specific active or past events.

## 📁 Directory Architecture
- **`/admin`** - Administrative backend for tracking submissions, event management, attendance filtering, and system analytics.
- **`/api`** - API endpoints for frontend data fetching (e.g., Live Signature Wall payload delivery).
- **`/assets`** - CSS, JavaScript, fonts, and images (featuring a "White-Tech" glassmorphism design system).
- **`/database`** - SQL scripts and setup logic for system tables.
- **`/includes`** - Shared backend logic (`db.php`, `brevo_mailer.php`, `pdf_generator.php`).
- **`/vendor`** - Third-party dependencies (FPDF library).

## ⚙️ Configuration Details

### Environment Variables (.env)
The system uses a `.env` file at the root for sensitive configuration. 
```ini
BREVO_API_KEY=YOUR_PRODUCTION_API_KEY_HERE
```
Ensure this file is included in `.gitignore` and never committed to public repositories.

### Clean URLs
The system is configured via `.htaccess` to use clean, extensionless URLs. 
- Example: `http://localhost/DNSC-IAC/covenant` (Internal redirect from `covenant.php`)
- AJAX routes like `api/fetch_signatures` are explicitly mapped to avoid 301 redirect data loss.

## 🛠️ Setup Instructions
1. Clone or copy the project into your local server environment (e.g., `htdocs` for XAMPP).
2. Set up the database by running `migrate.php` or executing the scripts in the `/database` folder.
3. Update database credentials in `includes/db.php`.
4. Create a `.env` file in the root directory and add your Brevo API Key.
5. Access the application via your local server (e.g., `http://localhost/DNSC-IAC`).

---

<div align="center">
  <b>Architected & Developed by <a href="#">Luminari Systems</a></b><br>
  <i>Empowering Next-Gen Technology & Seamless Digital Experiences</i>
</div>
