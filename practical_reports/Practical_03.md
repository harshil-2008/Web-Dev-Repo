# Practical 3: Responsive UI Design using CSS Grid, Flexbox, and Bootstrap/Tailwind

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO2 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 2 Hours |

### Practical Aim
To construct a modern responsive design system using CSS Grid Layout, Flexbox, and media queries across mobile, tablet, and desktop viewports.

### Problem Definition
Design responsive layouts for Home, About, Registration, Dashboard, and Events pages using CSS Grid and Flexbox. Optional to use CSS framework such as Bootstrap or Tailwind. Ensure mobile-first responsiveness.

### Key Questions / Analysis / Interpretation

#### Question 1: How does the layout adapt for mobile, tablet, and desktop screens?

The layout uses fluid containers, relative units (rem, %, vh), and media query breakpoints:
• Mobile Screens (<= 480px): Single-column vertical flow (grid-template-columns: 1fr), hamburger menu navigation drawer, full-width touch buttons.
• Tablet Screens (481px - 768px): Two-column adaptive card grid (repeat(auto-fit, minmax(280px, 1fr))), condensed horizontal header.
• Desktop Screens (>= 1024px): Multi-column grid layout, sticky glassmorphic navigation header, side-by-side dashboard widgets, max container width 1200px centered with auto margins.

#### Question 2: Which layout technique is used and why?

StudentHub leverages both CSS Grid and Flexbox for their respective strengths:
1. CSS Grid: Employed for 2-dimensional layouts where alignment across both rows and columns is needed (e.g., feature cards, event catalogs, admin dashboard widgets).
2. CSS Flexbox: Employed for 1-dimensional layouts where items are aligned along a single axis (e.g., header logo/nav alignment, button toolbars, form inputs, badge alignment).

#### Question 3: Are typography, spacing, colors, and contrast readable?

Yes. The styling employs the modern 'Inter' typeface from Google Fonts, standard 1.6 line height for legibility, an 8pt spatial grid for padding and margins, and curated dark/light color tokens meeting WCAG AAA contrast ratios (> 7:1).

#### Question 4: Is the framework used consistently without unnecessary inline CSS?

Yes. All styles are declared inside a centralized styles.css file using reusable CSS classes and custom properties (--primary-color, --bg-dark, --glass-bg). Zero unmaintainable inline styling is used.

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** CSS3, CSS Grid Layout, CSS Flexbox, media queries, mobile-first responsive design, typography, color harmony.
- **Applications:** Multi-device web user interfaces, cross-platform layouts, responsive web applications.
- **Learning Outcomes:** Students will design professional, responsive, and reusable UI layouts that dynamically adapt to any screen resolution.
- **Tools / Technology Used:** HTML5, CSS3, browser responsive design tools, device simulators.

### Implementation / Source Code
```text
/* Responsive CSS Grid & Media Queries from styles.css */
.features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin: 40px 0;
}

.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
}

@media (max-width: 768px) {
    .header-container { flex-wrap: wrap; }
    .menu-btn { display: block; }
    .nav-actions {
        display: none;
        width: 100%;
        flex-direction: column;
    }
    .nav-actions.active { display: flex; }
    .hero h1 { font-size: 32px; }
}
```

### Output & Verification Screenshots
![Figure 3: practical_03_responsive_mobile.png](../screenshots/practical_03_responsive_mobile.png)

![Figure 3: practical_03_responsive_tablet.png](../screenshots/practical_03_responsive_tablet.png)

![Figure 3: practical_03_responsive_desktop.png](../screenshots/practical_03_responsive_desktop.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** What is the difference between 'minmax(280px, 1fr)' and '1fr' in CSS Grid?
  - **Answer:** 'minmax(280px, 1fr)' enforces that a column will never shrink below 280px, and when extra space is available, it expands to consume a fraction (1fr) of the remaining space, providing automatic wrapping without manual media queries.
- **Viva Q2:** Why is the mobile-first CSS approach recommended?
  - **Answer:** Mobile-first ensures lightweight styling is loaded by default for constrained devices, progressively layering richer multi-column styles as viewport width expands via min-width media queries.

### Conclusion
*With this practical we can conclude that the harmonious combination of CSS Grid for two-dimensional component arrays and Flexbox for one-dimensional alignments produces an adaptable, mobile-first responsive web interface across any device.*

---

