# Practical 2: Semantic HTML5 Pages with Accessibility-Ready Structure

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO1, CO2 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 2 Hours |

### Practical Aim
To construct static HTML5 skeleton structures for at least 10 StudentHub pages using modern semantic tags and accessibility-compliant standards.

### Problem Definition
Develop static HTML5 skeletons for at least 10 StudentHub pages such as Home, About, Register, Login, Dashboard, Events, Profile, Contact, Admin, FAQ, and Feedback. Use semantic tags and accessibility-friendly structure.

### Key Questions / Analysis / Interpretation

#### Question 1: Are semantic tags such as header, nav, main, section, article, aside, and footer used properly?

Yes. Semantic tags communicate intrinsic meaning to both the web browser and assistive technologies:
• <header>: Encapsulates top branding, logo, and utility navigation.
• <nav>: Designates the primary menu block containing hyperlinked site pages.
• <main>: Wraps the primary unique content of each page, appearing exactly once per document.
• <section>: Groups conceptually related content together (e.g., hero section, features section).
• <article>: Houses self-contained, independently distributable entries such as event announcements.
• <aside>: Encapsulates sidebar links, quick notices, and supplementary info.
• <footer>: Anchors the bottom region with copyright, institutional disclaimers, and auxiliary links.

#### Question 2: Are form labels, headings, image alt text, and navigation links accessible?

Accessibility compliance was implemented according to WCAG 2.1 AA specifications:
1. Form Labels: Every form input contains an explicit <label for='...'> strictly matching the input's id.
2. Heading Hierarchy: Exactly one <h1> per document establishes the primary topic, followed sequentially by <h2> and <h3> tags without skipping hierarchy levels.
3. Image Alt Text: All <img> elements have informative alt attributes describing content or function.
4. Descriptive Links: Anchor tags use meaningful link copy (e.g., 'View Upcoming Events') rather than ambiguous text like 'click here'.

#### Question 3: Is the page structure consistent across all pages?

Yes. Every StudentHub page adheres to a standardized skeleton:
<!DOCTYPE html> -> <html lang='en'> -> <head> (meta charset, viewport, title, stylesheet) -> <body> -> <header> -> <div class='notification'> -> <main class='container'> -> <footer> -> <script>.
This guarantees architectural consistency across all 10 portal views.

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** HTML5 semantic elements, accessibility fundamentals, page linking, navigation menu architecture, content hierarchy.
- **Applications:** Accessible web portals, screen-reader optimized pages, SEO-friendly web development.
- **Learning Outcomes:** Students will create clean, linked, and accessibility-ready HTML page structures adhering to W3C standards.
- **Tools / Technology Used:** VS Code, HTML5, browser dev tools, Lighthouse / WAVE accessibility validators.

### Implementation / Source Code
```text
<!-- Semantic Structure from about.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | StudentHub</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="header-container">
            <a href="home.html" class="logo">StudentHub</a>
            <button class="menu-btn" id="menuBtn" type="button" aria-label="Toggle Menu">☰</button>
            <nav id="navbar">
                <a href="home.html">Home</a>
                <a href="about.html" class="active">About</a>
                <a href="events.html">Events</a>
                <a href="dashboard.html">Dashboard</a>
                <a href="contact.html">Contact</a>
                <a href="FAQs.html">FAQ</a>
            </nav>
        </div>
    </header>
    <main class="container">
        <section class="hero">
            <h1>About StudentHub Platform</h1>
            <p>Empowering CHARUSAT university students with centralized academic activities and events.</p>
        </section>
        <section class="features">
            <article class="card"><h2>Our Mission</h2><p>Deliver frictionless campus coordination.</p></article>
            <article class="card"><h2>Core Objectives</h2><p>Centralize notices, events, and records.</p></article>
        </section>
    </main>
    <footer><p>© 2026 StudentHub | All Rights Reserved</p></footer>
</body>
</html>
```

### Output & Verification Screenshots
![Figure 2: practical_02_semantic_about.png](../screenshots/practical_02_semantic_about.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** Why is using <section> better than a generic <div>?
  - **Answer:** <section> defines a thematic grouping of content with heading context, allowing screen readers and search engines to construct an intelligible document outline.
- **Viva Q2:** What does the 'lang' attribute in the <html> tag achieve?
  - **Answer:** It instructs user agents, speech synthesizers, and translation tools on the primary human language of the page, preventing incorrect pronunciation and translation errors.

### Conclusion
*With this practical we can conclude that building web pages with semantic HTML5 elements improves accessibility for assistive technologies, elevates SEO search indexing, and produces clean, maintainable code architectures.*

---

