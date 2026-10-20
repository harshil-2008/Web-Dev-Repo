# Practical 9: Secure User Registration with Database Insert, Duplicate Email Check, and Password Hashing

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO1, CO3, CO5 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 2 Hours |

### Practical Aim
To implement secure student account registration into MySQL, verifying email uniqueness via parameterized queries and encrypting passwords using BCRYPT hashing.

### Problem Definition
Implement secure user registration by inserting user details into MySQL using MySQLi. Validate input on frontend and backend, check duplicate email/username, hash passwords using password_hash(), and display appropriate messages.

### Key Questions / Analysis / Interpretation

#### Question 1: Is password_hash() used before storing passwords?

Yes. Passwords are never stored in plain text. PHP's native password_hash($password, PASSWORD_BCRYPT) creates a secure 60-character one-way cryptographic hash with an automatically generated salt.

#### Question 2: Is duplicate email/username checked?

Yes. Prior to database insertion, a parameterized query (SELECT user_id FROM users WHERE email = ?) verifies that no account exists with that institutional email address. If found, a duplicate error alert is displayed.

#### Question 3: Are frontend and backend validations consistent?

Yes. The backend enforces identical constraints (Regex, mandatory values, string lengths) as the frontend JavaScript. Even if client-side validation is bypassed, the server rejects non-compliant inputs.

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** Secure user registration, password hashing, parameterized queries, duplicate prevention, backend error handling.
- **Applications:** User authentication, student profile creation, secure credential storage.
- **Learning Outcomes:** Students will implement secure user registration following industry-standard web security practices.
- **Tools / Technology Used:** PHP 8.x, MySQLi / PDO, MySQL, XAMPP.

### Implementation / Source Code
```text
<?php
// Secure Registration Handler from process_registration.php
require_once 'db_connect.php';

$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

// 1. Check duplicate email
$stmt = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    die("Error: An account with this email address already exists.");
}
mysqli_stmt_close($stmt);

// 2. Hash password securely using BCRYPT
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// 3. Insert record using prepared statement
$insertStmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'student')");
mysqli_stmt_bind_param($insertStmt, "sss", $username, $email, $passwordHash);
mysqli_stmt_execute($insertStmt);
mysqli_stmt_close($insertStmt);
?>
```

### Output & Verification Screenshots
![Figure 9: practical_09_secure_registration.png](../screenshots/practical_09_secure_registration.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** Why is MD5 or SHA1 not acceptable for password storage?
  - **Answer:** MD5 and SHA1 are general-purpose collision-prone checksums that are too fast, making them vulnerable to brute-force cracking with GPUs and rainbow tables. BCRYPT is an intentionally slow, salted, adaptive algorithm.
- **Viva Q2:** What does the 'salt' accomplish in password hashing?
  - **Answer:** A salt is random data concatenated with the password before hashing, guaranteeing that two identical passwords produce completely different hash outputs, mitigating rainbow table attacks.

### Conclusion
*With this practical we can conclude that pre-insert uniqueness checks combined with BCRYPT password hashing and prepared statements provide a secure user registration workflow.*

---

