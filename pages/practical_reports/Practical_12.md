# Practical 12: Event Management CRUD with Poster Upload and File Validation

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO1, CO4, CO5 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 2 Hours |

### Practical Aim
To construct an Event Management CRUD portal allowing administrators to schedule campus events and upload promotional posters with strict MIME type validation, file size restrictions, and unique filename generation.

### Problem Definition
Develop event management functionality for admin users. Admin can add, view, update, delete, and upload event posters with file type and file size validation.

### Key Questions / Analysis / Interpretation

#### Question 1: Are add, update, and delete functionalities implemented correctly?

Yes. Administrators can create events with title, description, date, venue, category, and poster, edit existing listings, and delete obsolete entries with automated storage cleanup.

#### Question 2: Is the UI linked with the database correctly?

Yes. Published events are fetched from the events MySQL table and rendered into a responsive card grid complete with poster thumbnails, category tags, and dates.

#### Question 3: Are poster uploads validated for type and size?

Yes. Multi-layer validation enforces:
1. Size restriction: Rejects files larger than 2MB ($_FILES['poster']['size'] <= 2 * 1024 * 1024).
2. Extension restriction: Whitelists jpg, jpeg, png, webp.
3. MIME verification: Uses finfo_file() to inspect actual file binary headers.
4. Randomized renaming: Filenames are generated via uniqid('poster_', true) to prevent file overwrites.

#### Question 4: Are success and failure messages displayed?

Yes. Flash feedback alerts inform the administrator of successful uploads, validation failures, or file size rejections.

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** PHP-MySQL CRUD, file upload handling, MIME validation, server-side security, upload directory protection.
- **Applications:** Event portals, media asset managers, campus activity boards.
- **Learning Outcomes:** Students will implement a secure CRUD module with validated file upload and media management.
- **Tools / Technology Used:** PHP 8.x, MySQL, Bootstrap / Custom CSS, XAMPP.

### Implementation / Source Code
```text
<?php
// Secure File Upload Validation from events_crud.php
$maxSize = 2 * 1024 * 1024; // 2 MB limit
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
$file = $_FILES['poster'];

if ($file['size'] > $maxSize) {
    $error = 'File size exceeds 2 MB limit.';
} else {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedMimes)) {
        $error = 'Invalid MIME type. Only JPG, PNG, and WEBP allowed.';
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newFilename = uniqid('poster_', true) . '.' . $ext;
        move_uploaded_file($file['tmp_name'], __DIR__ . '/uploads/' . $newFilename);
    }
}
?>
```

### Output & Verification Screenshots
![Figure 12: practical_12_events_crud.png](../screenshots/practical_12_events_crud.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** Why is checking $_FILES['file']['type'] insecure on its own?
  - **Answer:** $_FILES['file']['type'] is populated by the client's browser based on the file extension and can be forged. Using finfo_file() on the server inspects the file's binary magic bytes directly, guaranteeing true MIME type detection.
- **Viva Q2:** How does an .htaccess file in the upload directory prevent remote code execution?
  - **Answer:** It disallows execution of server scripts (such as .php or .cgi) within the uploads folder, ensuring that even if an attacker manages to upload a script, the server will refuse to execute it.

### Conclusion
*With this practical we can conclude that validating file MIME signatures, enforcing file size limits, randomizing filenames, and securing the storage directory prevent arbitrary file upload vulnerabilities.*

---

