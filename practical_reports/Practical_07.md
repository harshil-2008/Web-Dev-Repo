# Practical 7: PHP Form Processing with Server-Side Validation and CSV/JSON File Storage

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO1, CO5 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 2 Hours |

### Practical Aim
To process submitted web form data using PHP, sanitize inputs against security vulnerabilities, validate parameters on the server side, and persist records in CSV and JSON formats.

### Problem Definition
Process submitted registration/contact form data using PHP. Validate and sanitize inputs on the server side and store records in CSV or JSON file format. Display success/error messages.

### Key Questions / Analysis / Interpretation

#### Question 1: Is the form submitted using POST?

Yes. The form sets method='POST'. HTTP POST encapsulates payload values within the HTTP request body rather than exposing data in the URL query string, safeguarding sensitive student data.

#### Question 2: Are inputs validated and sanitized server side?

Yes. PHP sanitization applies trim() to remove whitespace, htmlspecialchars() to neutralize potential Cross-Site Scripting (XSS) injection, and filter_var($email, FILTER_VALIDATE_EMAIL) to verify email syntax.

#### Question 3: Is file writing handled safely?

Yes. Writes to submissions.csv and submissions.json utilize exclusive file locking (LOCK_EX with file_put_contents() or flock()) to prevent race conditions during concurrent user submissions.

#### Question 4: Are success and error responses displayed clearly?

Yes. The PHP processor outputs an HTML confirmation view presenting feedback badges, an itemized summary of accepted fields, and navigational links back to the portal.

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** PHP forms, POST handling, server-side validation, input sanitization, file writing (fputcsv / file_put_contents), CSV/JSON storage.
- **Applications:** Lightweight backend processors, logging utilities, file-based content management systems.
- **Learning Outcomes:** Students will implement robust server-side form processing with safe file-based data storage.
- **Tools / Technology Used:** PHP 8.x, XAMPP / Apache, browser dev tools.

### Implementation / Source Code
```text
<?php
// Server-Side Processing from process_contact.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));

    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message)) {
        die('Error: Invalid input data.');
    }

    // Append to CSV File with safe locking
    $csvFile = __DIR__ . '/contact_submissions.csv';
    $fp = fopen($csvFile, 'a');
    if (flock($fp, LOCK_EX)) {
        fputcsv($fp, [date('Y-m-d H:i:s'), $name, $email, $subject, $message]);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
    echo "<p class='success'>Thank you, {$name}. Your message has been saved.</p>";
}
?>
```

### Output & Verification Screenshots
![Figure 7: practical_07_contact_form.png](../screenshots/practical_07_contact_form.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** Why is htmlspecialchars() necessary when outputting user data?
  - **Answer:** htmlspecialchars() converts special characters like '<', '>', '&', and '"' into their corresponding HTML entities, preventing malicious users from executing arbitrary JavaScript (XSS attacks).
- **Viva Q2:** What does the LOCK_EX parameter achieve in file operations?
  - **Answer:** LOCK_EX acquires an exclusive advisory lock on the target file, ensuring only one write operation occurs at any given millisecond, preventing file corruption.

### Conclusion
*With this practical we can conclude that server-side validation and sanitization are essential for secure data ingestion, and file-based persistence via CSV/JSON offers a lightweight storage mechanism for forms.*

---

