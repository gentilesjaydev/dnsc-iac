# IAC Covenant - Commitment System (RAISE Davao)

A PHP-based Digital Signing System designed to collect digital signatures from partners, generate official stamped PDF certificates, automatically email these documents, and display live updates on a signature wall.

## 🚀 Features
- **Authentication & Roles**: Secure login/registration that supports drawing or uploading signatures. Distinct roles for Partners and Admins.
- **Digital Signature Collection**: Integration of `Signature Pad.js` to capture signatures.
- **Automated PDF Engine**: Generates professional certificates using FPDF, stamping user signatures and audit metadata onto the document.
- **Branded PDF Viewer**: Custom viewer with CDITE favicon.
- **Automated Email Delivery**: Integrates with the **Brevo API** to automatically deliver the generated PDF certificates to partners upon signing.
- **Live Signature Wall**: A projector-mode screen that auto-refreshes every 5 seconds to display new signees live at an event.
- **Admin Dashboard**: Real-time stats, submission management, and CSV data export functionality.

## 📁 Directory Architecture
- **`/admin`** - Administrative backend for tracking submissions and managing users.
- **`/api`** - API endpoints for frontend data fetching (e.g., Live Signature Wall).
- **`/assets`** - CSS, JavaScript, and images ("White-Tech" design system).
- **`/database`** - SQL scripts and setup logic for `users` and `covenant_submissions` tables.
- **`/includes`** - Shared backend logic (`db.php`, `brevo_mailer.php`).
- **`/vendor`** - Third-party dependencies (FPDF library).

## ⚙️ Configuration Details

### Brevo Email Integration
- **API Key**: `YOUR_BREVO_API_KEY_HERE` *(Placeholder - replace for production)*
- **Verified Sender**: `dnsciac@gmail.com`
- **Location**: `includes/brevo_mailer.php`

### Clean URLs
The system is configured via `.htaccess` to use clean URLs. Example links:
- `http://localhost/iac-covenant/covenant`
- `http://localhost/iac-covenant/admin/dashboard`

## 🛠️ Setup Instructions
1. Clone or copy the project into your local server environment (e.g., `htdocs` for XAMPP).
2. Set up the database by running the scripts provided in the `/database` folder.
3. Update database credentials in `includes/db.php`.
4. Replace the Brevo API Key in `includes/brevo_mailer.php` with a production key.
5. Access the application via your local server (e.g., `http://localhost/DNSC-IAC`).
