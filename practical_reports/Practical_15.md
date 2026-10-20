# Practical 15: Final Integration, Testing, Documentation, GitHub Submission, and Local Deployment

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO2, CO4, CO5 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 0 Hours |

### Practical Aim
To integrate all 15 modules into a production-ready StudentHub campus portal, execute an end-to-end verification test matrix, compile documentation, and deploy locally using XAMPP/Apache.

### Problem Definition
Integrate all modules into a single deployable StudentHub portal. Verify navigation, responsiveness, authentication, database operations, security checks, AJAX functionality, and documentation. Deploy locally using XAMPP/Apache virtual host and submit GitHub repository.

### Key Questions / Analysis / Interpretation

#### Question 1: Are all pages properly linked and navigable?

Yes. A cross-module hyperlink audit verified 100% link integrity across all 10 core HTML pages, PHP endpoints, breadcrumb navigation trails, and footer directories without any 404 errors.

#### Question 2: Are sessions, roles, validations, and database operations working end-to-end?

Yes. The entire user lifecycle was verified: student registration -> BCRYPT hashing -> secure login -> session establishment -> event browsing -> administrative student CRUD -> poster uploads -> logout.

#### Question 3: Are security and accessibility checklists completed?

Yes. Security: SQL injection defense via prepared statements, XSS defense via htmlspecialchars(), session fixation defense, and upload directory protection. Accessibility: WCAG 2.1 AA compliant color contrast, ARIA landmarks, and alt tags.

#### Question 4: Is project documentation sufficient for installation and testing?

Yes. A master README.md details prerequisites, step-by-step XAMPP deployment, SQL database import steps, test credentials, and a 15-point verification matrix.

#### Question 5: Is the project repository clean and properly committed?

Yes. Hosted at https://github.com/harshil-2008/Web-Dev-Repo with atomic semantic commits and organized directories (docs/, pages/, screenshots/, uploads/).

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** Full-stack integration, system testing, software documentation, Git/GitHub collaboration, XAMPP/Apache local deployment.
- **Applications:** Enterprise web portal deployment, full-stack application lifecycle delivery.
- **Learning Outcomes:** Students will deliver a complete, secure, documented, and testable full-stack web application.
- **Tools / Technology Used:** HTML5, CSS3, JavaScript ES6+, PHP 8.x, MySQL, Git, GitHub, XAMPP.

### Implementation / Source Code
```text
# System Architecture & Local Deployment Commands
# 1. Clone repository
git clone https://github.com/harshil-2008/Web-Dev-Repo.git

# 2. Deploy to Apache web root (Windows PowerShell)
New-Item -ItemType SymbolicLink -Path 'C:\xampp\htdocs\studenthub' -Target 'D:\E\WEBDEV\My work'

# 3. Import MySQL Database via command line
mysql -u root -p studenthub < studenthub.sql

# 4. Access portal in modern web browser
http://localhost/studenthub/home.html
```

### Output & Verification Screenshots
![Figure 15: practical_15_final_portal.png](../screenshots/practical_15_final_portal.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** What is an Apache Virtual Host and why is it useful during development?
  - **Answer:** An Apache Virtual Host enables running multiple distinct domain names (e.g. studenthub.local) on a single physical server machine by routing HTTP Host headers to specific project directory paths.
- **Viva Q2:** What are the key stages of full-stack web application testing?
  - **Answer:** Unit testing (verifying isolated functions like regex or helper algorithms), integration testing (verifying database and session communication), security testing (penetration probes for SQLi/XSS), and end-to-end user acceptance testing.

### Conclusion
*With this practical we can conclude that full-stack integration, rigorous verification testing, adherence to web security principles, and clear documentation result in a production-ready, accessible campus web portal.*

---

