# Practical 11: Student Management CRUD with Search and Filter

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO1, CO4, CO5 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 2 Hours |

### Practical Aim
To build a complete Student Management CRUD module allowing administrators to create, view, edit, soft-delete, search, filter, and export student academic records across 20+ seeded entries.

### Problem Definition
Create a student management module to add, view, edit, delete, search and filter student records. All database operations must use MySQLi statements and clear success/failure messages.

### Key Questions / Analysis / Interpretation

#### Question 1: Are SELECT, INSERT, UPDATE, and DELETE operations working?

Yes. All four CRUD primitives are fully operational via MySQLi prepared statements:
• CREATE: Modal form adds new student with real-time field validation.
• READ: Tabular view displays paginated student records.
• UPDATE: Pre-populated edit modal modifies student parameters.
• DELETE: Soft-deletion updates record status to 'deleted' while preserving relational historical records.

#### Question 2: Is search by name/email/course implemented?

Yes. A search box dynamically queries the database using: WHERE (name LIKE ? OR email LIKE ? OR course LIKE ?), providing instantaneous record retrieval.

#### Question 3: Are delete operations confirmed before execution?

Yes. Clicking the delete icon triggers a JavaScript confirm() modal. Only upon explicit user confirmation is the deletion request dispatched.

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** PHP-MySQL CRUD, parameterized SQL queries, search & filter pipelines, CSV file export, soft-delete pattern.
- **Applications:** Academic student administration, database record management, back-office university registries.
- **Learning Outcomes:** Students will develop a complete, secure data-management module with responsive search and filtering.
- **Tools / Technology Used:** PHP 8.x, MySQL, XAMPP, Bootstrap / Custom CSS.

### Implementation / Source Code
```text
<?php
// Multi-Criteria Search & Filter from students_crud.php
$sql = "SELECT * FROM students WHERE status != 'deleted'";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR mobile LIKE ?)";
    $sp = "%{$search}%";
    $params[] = $sp; $params[] = $sp; $params[] = $sp;
    $types .= "sss";
}
if (!empty($courseFilter)) {
    $sql .= " AND course = ?";
    $params[] = $courseFilter;
    $types .= "s";
}
$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$students = mysqli_stmt_get_result($stmt);
?>
```

### Output & Verification Screenshots
![Figure 11: practical_11_student_crud.png](../screenshots/practical_11_student_crud.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** What is the advantage of 'Soft Delete' compared to 'Hard Delete'?
  - **Answer:** Hard delete permanently removes rows from disk, destroying historical associations. Soft delete updates a status flag (e.g. status='deleted'), preserving foreign key relational integrity and audit logs.
- **Viva Q2:** Why are parameterized statements essential in CRUD applications?
  - **Answer:** Parameterized statements separate SQL query structure from user data, neutralizing SQL injection attempts regardless of input content.

### Conclusion
*With this practical we can conclude that building data-driven CRUD modules with parameterized queries, multi-criteria filtering, and soft-delete safeguards data integrity and user control.*

---

