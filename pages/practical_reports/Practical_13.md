# Practical 13: PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO3, CO4, CO5 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 2 Hours |

### Practical Aim
To construct a RESTful PHP JSON API endpoint supporting GET, POST, and DELETE requests, and consume it asynchronously using JavaScript Fetch API without triggering full page reloads.

### Problem Definition
Create simple PHP JSON API endpoints for events or students and consume them using Fetch API. Perform list, add, update, and delete operations asynchronously without full page reload.

### Key Questions / Analysis / Interpretation

#### Question 1: Do PHP endpoints return valid JSON responses?

Yes. Every response from api_events.php sets header('Content-Type: application/json; charset=UTF-8') and outputs data serialized via json_encode(), accompanied by appropriate HTTP status codes (200 OK, 201 Created, 400 Bad Request, 404 Not Found).

#### Question 2: Are GET and POST requests handled properly?

Yes. GET requests parse query parameters for filtering and output event lists; POST requests decode incoming JSON via json_decode(file_get_contents('php://input'), true), validate parameters, insert the event, and return HTTP 201.

#### Question 3: Are loading, success, and error states shown on the frontend?

Yes. The frontend displays an animated loading spinner during network round-trips and non-intrusive toast notification banners upon success or error.

#### Question 4: Is input validation performed before database operations?

Yes. The API verifies mandatory fields (title, event_date, venue) prior to database execution, returning HTTP 400 Bad Request with an itemized error message if any field is missing.

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** RESTful PHP JSON APIs, Fetch API, asynchronous CRUD, JSON response formatting, frontend-backend API integration, debouncing.
- **Applications:** Single-page applications (SPAs), mobile app backends, real-time interactive administrative dashboards.
- **Learning Outcomes:** Students will integrate frontend JavaScript with PHP backends using RESTful JSON APIs.
- **Tools / Technology Used:** PHP 8.x, JavaScript Fetch API, MySQL, browser Network tab.

### Implementation / Source Code
```text
// RESTful JSON API Endpoint (api_events.php)
header('Content-Type: application/json; charset=UTF-8');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $events = fetchAllEvents($conn);
    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $events]);
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (empty($input['title']) || empty($input['event_date'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }
    $newId = insertEvent($conn, $input);
    http_response_code(201);
    echo json_encode(['status' => 'success', 'id' => $newId, 'message' => 'Event created successfully']);
}
```

### Output & Verification Screenshots
![Figure 13: practical_13_ajax_crud.png](../screenshots/practical_13_ajax_crud.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** What is the purpose of HTTP status codes 200, 201, 400, and 404 in REST APIs?
  - **Answer:** 200: Successful GET/PUT/DELETE request; 201: Successfully created new entity (POST); 400: Bad Request due to client-side validation failure; 404: Target resource not found.
- **Viva Q2:** What is debouncing in client-side search?
  - **Answer:** Debouncing delays the execution of a function until a specified time interval (e.g. 300ms) has elapsed since the last keystroke, preventing redundant API calls on every character typed.

### Conclusion
*With this practical we can conclude that RESTful PHP JSON APIs combined with the JavaScript Fetch API enable asynchronous CRUD interactions without full page reloads, improving user experience and responsiveness.*

---

