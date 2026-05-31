# RAISE Davao - Digital Signing System TODO

## ✅ COMPLETED TASKS
- [x] **Project Foundation**: Core folder structure and design system (White-Tech).
- [x] **Database Architecture**: `users` and `covenant_submissions` tables with auto-setup.
- [x] **Authentication Module**: Secure Login, Registration (with Draw/Upload signature), and Role-based routing.
- [x] **Signature Collection**: Integrated Signature Pad.js for registration and auto-signing on documents.
- [x] **PDF Generation Engine**: FPDF-based document creator that stamps signatures and audit metadata.
- [x] **Partner Dashboard**: Central hub for partners to view/download their signed endorsements.
- [x] **Signature Wall**: Live "Projector Mode" board that auto-refreshes every 5 seconds.
- [x] **Admin Dashboard**: Real-time stats, submission management, and user control panels.
- [x] **Data Export**: CSV export functionality for all covenant signatories.
- [x] **Admin Management**: Full CRUD for submissions and user account status toggling.
- [x] **Email Integration**: Brevo API module for auto-sending signed PDFs to partner inboxes.

## 🔜 PENDING / FUTURE ENHANCEMENTS
- [ ] **Brevo Configuration**: Replace placeholder API Key in `includes/brevo_mailer.php` with a real production key.
- [ ] **Celebration FX**: Add a sound effect or a special "New Signatory" pop-up toast to the Signature Wall when someone signs live.
- [ ] **Custom PDF Typography**: Integrate specific fonts (like Montserrat or Inter) into FPDF to match the brand perfectly.
- [ ] **Admin User Creation**: Add feature for Super Admin to manually add and invite new admins.
- [ ] **CSRF Protection**: Add token-based security to all POST forms for production-level hardening.
- [ ] **Password Reset**: Implement "Forgot Password" flow via Brevo.
- [ ] **Projector Mode Tuning**: Add a "Fullscreen" auto-scroll if the number of signatories exceeds the screen.
- [ ] **Production Deployment**: Verify InfinityFree server compatibility (PHP 8.x + PDO support).

---
*Created on March 11, 2026*
