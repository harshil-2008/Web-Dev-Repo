# StudentHub - Comprehensive Web Development Frameworks Project

**Author:** Harshil Prajapati  
**Student ID:** 25DCE093  
**Program:** B.Tech (Computer Engineering) | Semester III  
**Course:** CSUC201 / ITUE203 – Web Development Frameworks  
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT University  
**Academic Year:** 2026–27  
**GitHub Repository:** [https://github.com/harshil-2008/Web-Dev-Repo](https://github.com/harshil-2008/Web-Dev-Repo)

---

## 📌 Executive Summary

**StudentHub** is an end-to-end, full-stack campus management and activity portal engineered specifically to fulfill all 15 practical specifications outlined by Charotar University of Science and Technology (CHARUSAT) for the *Web Development Frameworks* curriculum.

The application brings together modern frontend technologies, robust client-side verification, server-side data processing, relational database modeling, role-based access control, secure session management, and asynchronous REST APIs.

---

## 🛠️ Technology Stack

| Layer | Technologies & Tools Employed |
| :--- | :--- |
| **Structure & Semantics** | HTML5 Semantic Tags (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, `<footer>`) |
| **Styling & Responsive Layout** | Vanilla CSS3, CSS Grid Layout, CSS Flexbox, Glassmorphism, CSS Custom Properties |
| **Client-Side Interactivity** | Vanilla JavaScript ES6+, DOM Manipulation, Event Listeners, Fetch API, LocalStorage |
| **Backend & Server-Side** | PHP 8.x (Form sanitization, BCRYPT hashing, PDO & MySQLi prepared statements) |
| **Database Architecture** | MySQL 8.0 / MariaDB (Normalized Relational Schema, Foreign Key Constraints, Transactions) |
| **Security & Auditing** | Session Fixation Protection, Inactivity Timeout, RBAC, File MIME Verification, Audit Logging |
| **Tools & Environment** | Visual Studio Code, Git, GitHub, XAMPP / Apache, Playwright automated testing |

---

## 🗺️ Project Sitemap & Directory Architecture

```text
d:\E\WEBDEV\My work\
│
├── 📄 HTML5 Pages (Practicals 1, 2, 3, 4)
│   ├── home.html               # Landing Hero Banner & Navigation Hub
│   ├── about.html              # University Mission, Objectives & Campus Features
│   ├── register.html           # Practical 5: Regex validated student registration
│   ├── login.php               # Practical 10: Secure authentication & RBAC
│   ├── dashboard.html          # Practical 4 & 6: Student dashboard & directory
│   ├── events.html             # Practical 6: Fetch API event catalog & category filters
│   ├── profile.html            # User profile view with academic credentials
│   ├── contact.html            # Practical 7: Feedback form processed by PHP
│   ├── FAQs.html               # Practical 4: Interactive JavaScript accordion
│   └── admin.html              # Static Admin wireframe
│
├── 🎨 Styling & Assets
│   ├── styles.css              # Universal design system (Grid, Flexbox, Light/Dark)
│   ├── uploads/                # Practical 12: Secure event poster image repository
│   │   └── .htaccess           # Script execution prevention in uploads directory
│   └── screenshots/            # Automated high-resolution evaluation screenshots
│
├── ⚙️ Client-Side JavaScript Logic
│   ├── script.js               # Modal, Slider, Accordion, Theme, Regex, Fetch API
│   ├── data.json               # Seed student directory (20+ records)
│   ├── events.json             # Seed campus event records
│   └── events_store.json       # Asynchronous API JSON store
│
├── 🐘 Backend PHP & Database Modules
│   ├── db_connect.php          # Practical 8: Dual MySQLi & PDO prepared database connector
│   ├── studenthub.sql          # Practical 8 & 11: SQL Schema (users, students, events, registrations, audit_logs)
│   ├── test_db.php             # Practical 8: Interactive database connection and diagnostic test
│   ├── process_contact.php     # Practical 7: Form processing with CSV/JSON storage
│   ├── process_registration.php# Practical 7 & 9: Server-side validation, duplicate email check, password_hash
│   ├── auth.php                # Practical 10: Session timeout & RBAC middleware
│   ├── logout.php              # Practical 10: Secure session termination
│   ├── students_crud.php       # Practical 11: Full Student CRUD, Search, Filter & CSV Export
│   ├── events_crud.php         # Practical 12: Event CRUD with poster upload & validation
│   ├── api_events.php          # Practical 13: RESTful JSON API (GET, POST, DELETE)
│   ├── ajax_crud.html          # Practical 13: Asynchronous Fetch API frontend without page reload
│   └── admin_dashboard.php     # Practical 14: Dynamic metric counters, charts & audit logs
│
└── 📚 Documentation & Manual Deliverables
    ├── wireframe.md            # Low-fidelity ASCII UI wireframes for all 10 pages
    ├── README.md               # Master system documentation and deployment manual
    └── Practical Reports (1-15)# Word (.docx) & Markdown laboratory manual deliverables
```

---

## 🚀 Local Deployment with XAMPP / Apache

### Step 1: Database Setup
1. Launch **XAMPP Control Panel** and start **Apache** and **MySQL**.
2. Open your web browser and navigate to `http://localhost/phpmyadmin/`.
3. Create a new database named `studenthub` (Collation: `utf8mb4_unicode_ci`).
4. Click on the **Import** tab, choose `studenthub.sql` from the repository, and click **Import**.
5. Verify that all 5 tables (`users`, `students`, `events`, `registrations`, `audit_logs`) are created and populated.

### Step 2: Virtual Host / Web Server Deployment
1. Copy or symlink the project directory to your XAMPP `htdocs` folder:
   ```powershell
   # In Windows PowerShell:
   New-Item -ItemType SymbolicLink -Path "C:\xampp\htdocs\studenthub" -Target "d:\E\WEBDEV\My work"
   ```
2. Navigate to `http://localhost/studenthub/home.html` in any modern web browser.
3. Access diagnostic test via `http://localhost/studenthub/test_db.php`.

---

## 🔐 Credentials for Testing

| Role | Username / Identifier | Password | Destination Dashboard |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `admin` | `Admin@123` | `admin_dashboard.php` |
| **Enrolled Student** | `harshil` | `Student@123` | `dashboard.html` |
| **Enrolled Student** | `natasha` | `Student@123` | `dashboard.html` |

---

## 🧪 Comprehensive Verification & Test Matrix

| ID | Test Category | Target Module | Test Scenario & Input | Expected Output | Status |
| :---: | :--- | :--- | :--- | :--- | :---: |
| **TC-01** | Wireframe & Sitemap | Practical 1 | Navigation structure audit across 10 pages | 100% link coverage, zero broken references | **PASS** |
| **TC-02** | Semantic Accessibility | Practical 2 | Wave/Lighthouse accessibility audit on HTML5 | Alt text present, ARIA landmarks, WCAG 2.1 AA | **PASS** |
| **TC-03** | UI Responsiveness | Practical 3 | Viewport testing: 375px (Mobile), 768px, 1280px | Grid & flex adapt without horizontal scroll | **PASS** |
| **TC-04** | DOM Manipulation | Practical 4 | Theme toggle & accordion interaction | Instant state transition, preference saved | **PASS** |
| **TC-05** | Form Regex Validation| Practical 5 | Invalid email & password < 8 characters | Inline error spans trigger, form submission halted | **PASS** |
| **TC-06** | Fetch API Rendering | Practical 6 | Dynamic event loading with course filter | Smooth card injection from external JSON | **PASS** |
| **TC-07** | Server-side Processing| Practical 7 | POST contact submission with sanitize | Stored in CSV & JSON, sanitized response | **PASS** |
| **TC-08** | Database Connectivity| Practical 8 | Execute `test_db.php` connection probe | Successful PDO/MySQLi connection handshake | **PASS** |
| **TC-09** | Secure Registration | Practical 9 | Register with duplicate email | Duplicate rejected, password hashed via BCRYPT | **PASS** |
| **TC-10** | Session Authentication| Practical 10| Login with invalid password vs valid | Session fixation blocked, role-based redirect | **PASS** |
| **TC-11** | Student CRUD | Practical 11| Add/Edit/Soft-Delete student record | Database reflects changes with flash feedback | **PASS** |
| **TC-12** | Event Poster Upload | Practical 12| Upload `.exe` file vs `.png` file (< 2MB) | `.exe` rejected, valid image renamed & saved | **PASS** |
| **TC-13** | AJAX Fetch CRUD | Practical 13| Async POST & DELETE on `api_events.php` | Instant UI update without full page reload | **PASS** |
| **TC-14** | Admin Dashboard | Practical 14| Check metric counts & audit logging | SQL counts match DB rows, action logged | **PASS** |
| **TC-15** | System Integration | Practical 15| End-to-end full semester workflow | Complete portal operational and documented | **PASS** |

---

*Prepared by Harshil Prajapati (25DCE093) for CSUC201 / ITUE203 Laboratory Evaluation.*
