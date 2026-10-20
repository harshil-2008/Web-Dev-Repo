# Practical 6: Rendering External JSON Data using Fetch API, Search and Filter

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO3, CO4 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 2 Hours |

### Practical Aim
To asynchronously consume external JSON files using the JavaScript Fetch API, dynamically generating UI cards with real-time text searching, category filtering, and sorting.

### Problem Definition
Display event lists, student profiles, notices, or FAQs from external JSON files using Fetch API. Implement dynamic rendering, search, filter, sorting, and pagination.

### Key Questions / Analysis / Interpretation

#### Question 1: How is JSON fetched, parsed, and rendered?

The Fetch API executes an asynchronous HTTP GET request:
fetch('events.json')
  .then(response => { if (!response.ok) throw new Error(); return response.json(); })
  .then(data => { eventsData = data; renderEvents(data); })
  .catch(err => { showErrorMessage(err); });
The parsed JavaScript objects are converted into HTML cards using template literals and injected into the DOM container.

#### Question 2: Which array methods are used for search, filter, sort, and pagination?

1. Search & Filter: Array.prototype.filter() checks if event title includes the search string and matches category dropdown.
2. Sort: Array.prototype.sort() re-orders cards alphabetically by title or chronologically by date.
3. Pagination: Array.prototype.slice(startIndex, endIndex) isolates the slice of records for the active page.

#### Question 3: How are loading and error states handled?

Prior to fetch completion, an animated spinner element is displayed in the container. If the network promise rejects, the catch block renders a user-friendly error notice with a 'Retry' action.

#### Question 4: How is modularity maintained in JavaScript files?

Logic is divided into single-responsibility functions: loadData(), updateView(), filterData(), and renderCards().

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** JSON handling, Fetch API, array manipulation (map/filter/sort/slice), asynchronous JavaScript, pagination logic.
- **Applications:** Single-page application data rendering, dynamic catalog listings, headless API consumption.
- **Learning Outcomes:** Students will consume external JSON data and render reusable, interactive frontend views asynchronously.
- **Tools / Technology Used:** JavaScript ES6+, JSON, HTML5, CSS3, browser dev tools.

### Implementation / Source Code
```text
// Fetch API & Dynamic Filter Pipeline from script.js
let allEvents = [];

async function loadEventsData() {
    try {
        const res = await fetch('events.json');
        allEvents = await res.json();
        updateEventsView();
    } catch (e) {
        document.getElementById('eventsContainer').innerHTML = '<p>Error loading data.</p>';
    }
}

function updateEventsView() {
    const query = document.getElementById('eventSearch').value.toLowerCase();
    const category = document.getElementById('filterCategory').value;

    const filtered = allEvents.filter(ev => {
        const matchesSearch = ev.title.toLowerCase().includes(query) || ev.description.toLowerCase().includes(query);
        const matchesCat = (category === 'All' || category === '' || ev.category === category);
        return matchesSearch && matchesCat;
    });

    renderEvents(filtered);
}
```

### Output & Verification Screenshots
![Figure 6: practical_06_fetch_api_cards.png](../screenshots/practical_06_fetch_api_cards.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** What is the difference between synchronous XMLHttpRequest and Fetch API?
  - **Answer:** XMLHttpRequest relies on callback handlers and was historically synchronous or event-driven, whereas Fetch API returns native Promises, supports async/await, and offers cleaner request/response stream handling.
- **Viva Q2:** What is JSON and why is it preferred over XML?
  - **Answer:** JSON (JavaScript Object Notation) is a lightweight, human-readable data-interchange format that directly maps to native JavaScript objects without requiring heavy XML parsing trees.

### Conclusion
*With this practical we can conclude that the JavaScript Fetch API enables seamless asynchronous retrieval of external JSON records, allowing dynamic card generation and responsive filtering without page reloads.*

---

