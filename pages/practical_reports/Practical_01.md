# Practical 1: Project Initiation, Requirement Analysis, Sitemap, Wireframe, and GitHub Setup

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO1 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 0 Hours |

### Practical Aim
To initiate the StudentHub web portal by conducting requirement analysis, creating a navigation sitemap, designing low-fidelity wireframes, and setting up a version-controlled GitHub repository.

### Problem Definition
Initiate the semester-long StudentHub portal by identifying the problem scope, user roles, key modules, navigation flow, and minimum 10 pages. Create a sitemap, low-fidelity wireframe, project folder structure, README file, and GitHub repository.

### Key Questions / Analysis / Interpretation

#### Question 1: What is URL and parts of URL?

A URL (Uniform Resource Locator) is a global unique identifier used to locate and access resources on the World Wide Web.

The primary structural components of a URL include:
1. Protocol / Scheme (e.g., https://): Specifies the communication protocol governing the transaction. HTTPS encrypts data via TLS/SSL.
2. Subdomain (e.g., www or portal): Identifies a specific subdivision or server cluster under the parent domain.
3. Domain Name (e.g., charusat.edu.in): Human-readable address mapped to server IP address through Domain Name System (DNS).
4. Port Number (e.g., :443 for HTTPS, :80 for HTTP): Logical gateway on the host operating system that directs network packets to the appropriate web service.
5. Path (e.g., /studenthub/home.html): Exact hierarchical file or virtual route directory structure on the server filesystem.
6. Query String (e.g., ?dept=CE&year=3): Key-value parameters passed after '?' used for dynamic server-side filtering or client routing.
7. Fragment / Anchor (e.g., #contact): Internal bookmark prefixed by '#' instructing the browser to scroll directly to an element with that ID.

#### Question 2: How does HTML file processed in web browser?

Browsers parse and render an HTML file via the Critical Rendering Path (CRP):
1. Network Retrieval: Browser sends an HTTP GET request and receives HTML bytes.
2. Tokenization & DOM Tree Construction: Raw bytes are decoded to characters, parsed into tokens (tags, attributes), and converted into Node objects forming the Document Object Model (DOM) tree.
3. CSSOM Tree Construction: As external stylesheets (<link rel='stylesheet'>) are fetched, CSS rules are parsed into the CSS Object Model (CSSOM) tree.
4. Render Tree Synthesis: The DOM and CSSOM trees are merged into the Render Tree, discarding non-visible elements (such as <head> and display:none).
5. Layout (Reflow): The layout engine calculates exact geometric coordinates, widths, and heights for each box in the viewport.
6. Paint & Compositing: Vector shapes and text are rasterized into pixels on layers, and GPU layers are composited onto the screen display.

#### Question 3: How will page navigation flow managed among all HTML pages?

Navigation flow in StudentHub is structured around a centralized hierarchical architecture:
1. Universal Sticky Header: Every page includes an identical <header> and <nav> with explicit relative links to all major modules (home.html, about.html, events.html, dashboard.html, profile.html, contact.html, FAQs.html, login.php, register.html).
2. Breadcrumb Navigation: Secondary pages implement breadcrumbs (e.g., Home > Events > Workshop) allowing one-click back-tracing.
3. Mobile Responsive Toggle: A JavaScript-driven hamburger button collapses the navigation menu into an accessible slide-down drawer on mobile screens.

#### Question 4: How will GitHub commits be maintained after each practical?

GitHub repository version control follows standard Git flow conventions:
1. Semantic Commit Messages: Commits follow Conventional Commits standard (e.g., 'feat: add registration regex validation', 'fix: resolve navbar flex alignment on tablet').
2. Atomic Commit Strategy: Each practical is committed as an isolated unit with clean diffs containing related source code and documentation.
3. Milestone Tagging: Each completed practical is tagged (e.g., git tag -a practical-01 -m 'Practical 1 Submission') for traceability.

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** Requirement analysis, sitemap design, wireframing, Git/GitHub version control, README authoring, folder structuring.
- **Applications:** Web application architectural planning, enterprise portal scoping, maintainable open-source repository design.
- **Learning Outcomes:** Students will be able to analyze user requirements, construct comprehensive sitemaps and wireframes, and establish professional Git repositories.
- **Tools / Technology Used:** VS Code, Git, GitHub, ASCII wireframing tools, Markdown.

### Implementation / Source Code
```text
# Project Folder Structure:
studenthub/
├── home.html, about.html, register.html, login.php, dashboard.html
├── events.html, profile.html, contact.html, FAQs.html, admin.html
├── styles.css (Universal Design System)
├── script.js (Interactive UI & Fetch API)
├── db_connect.php, studenthub.sql (Database Layer)
├── screenshots/ & docs/wireframe.md

# Wireframe ASCII Representation (Home Page):
+-------------------------------------------------------------+
| HEADER: Logo (StudentHub) | Nav Links | Theme Toggle Button |
+-------------------------------------------------------------+
| Notification: Welcome to StudentHub Campus Portal          |
+-------------------------------------------------------------+
| HERO: Welcome Banner | Registration CTA | Sign In CTA       |
+-------------------------------------------------------------+
| GRID: [Events]  [Notices]  [Dashboard]  [Feedback]          |
+-------------------------------------------------------------+
| FOOTER: About Summary | Quick Links | Support | Copyright   |
+-------------------------------------------------------------+
```

### Output & Verification Screenshots
![Figure 1: practical_01_wireframe.png](../screenshots/practical_01_wireframe.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** What is the primary difference between low-fidelity and high-fidelity wireframes?
  - **Answer:** Low-fidelity wireframes focus on layout structure, user flow, and content placement using simple boxes or ASCII text without color or imagery. High-fidelity wireframes include pixel-perfect graphics, typography, branding, and color palettes.
- **Viva Q2:** Why is a sitemap crucial before writing code?
  - **Answer:** A sitemap establishes the complete information architecture, page relationships, and navigation hierarchies, preventing broken user flows and scope creep during implementation.

### Conclusion
*With this practical we can conclude that proper architectural planning, requirement scoping, sitemap modeling, and structured Git version control provide an indispensable foundation for building large-scale, maintainable web applications.*

---

