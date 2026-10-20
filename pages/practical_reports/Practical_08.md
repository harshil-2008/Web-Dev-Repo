# Practical 8: MySQL Schema Design, ER Model, PDO Connectivity, and Prepared Statements

**Name:** Harshil Prajapati | **ID:** 25DCE093 | **Program:** B.Tech (CE) Sem III

| Parameter | Details |
| :--- | :--- |
| **CO Mapping** | CO1, CO5 |
| **Bloom's Taxonomy** | L3 – Apply / L4 – Analyze |
| **Lab Duration** | 4 Hours |
| **Implementation Hours** | 4 Hours |
| **Testing / Evaluation Hours** | 2 Hours |

### Practical Aim
To architect a normalized relational database schema for StudentHub, model entity relationships, and implement secure PHP database connections using MySQLi and PDO prepared statements.

### Problem Definition
Design the database for StudentHub with students, events and registrations. Create PHP database connection using MYSQL statements (and PDO prepared statements).

### Key Questions / Analysis / Interpretation

#### Question 1: Is the schema normalized properly?

Yes. The database schema satisfies Third Normal Form (3NF):
• 1NF: Atomic columns with no repeating groups.
• 2NF: All non-key attributes fully depend on the entire primary key (elimination of partial dependencies).
• 3NF: Elimination of transitive dependencies. Entities are partitioned into discrete tables: users, students, events, registrations, and audit_logs.

#### Question 2: Are primary keys and foreign keys correctly defined?

Yes. Each table defines an auto-incrementing integer PRIMARY KEY. Relationships are enforced via FOREIGN KEY constraints:
• registrations.student_id REFERENCES students(student_id) ON DELETE CASCADE
• registrations.event_id REFERENCES events(event_id) ON DELETE CASCADE
• students.user_id REFERENCES users(user_id) ON DELETE SET NULL

#### Question 3: Is the database connection handled securely?

Yes. Connection parameters reside in db_connect.php. The PDO configuration sets PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, uses utf8mb4 encoding, and disables emulated prepares, preventing SQL injection.

### Key Skills, Applications & Learning Outcomes
- **Key Skills:** Entity Relationship modeling, database normalization, MySQL schema authoring, PDO database abstraction, prepared statements.
- **Applications:** Enterprise database design, secure web data storage, transactional student information systems.
- **Learning Outcomes:** Students will design and connect a normalized database securely using PHP PDO and MySQLi.
- **Tools / Technology Used:** MySQL, phpMyAdmin, PHP 8.x, XAMPP.

### Implementation / Source Code
```text
-- Normalized Table Schemas from studenthub.sql
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    course VARCHAR(60) NOT NULL,
    year INT NOT NULL CHECK (year BETWEEN 1 AND 4),
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    status ENUM('active', 'inactive', 'deleted') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    UNIQUE KEY unique_reg (student_id, event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Output & Verification Screenshots
![Figure 8: practical_08_database_connectivity.png](../screenshots/practical_08_database_connectivity.png)

### Post Laboratory Work / Viva Questions
- **Viva Q1:** What is the primary difference between PDO and MySQLi?
  - **Answer:** MySQLi works exclusively with MySQL databases, whereas PDO (PHP Data Objects) is a database-agnostic abstraction layer supporting 12+ database engines with unified error-handling.
- **Viva Q2:** Why is 'utf8mb4' preferred over 'utf8' in MySQL?
  - **Answer:** 'utf8mb4' provides full 4-byte UTF-8 encoding support, accommodating emojis and extended multilingual plane characters, whereas MySQL's legacy 'utf8' only supports up to 3 bytes.

### Conclusion
*With this practical we can conclude that relational normalization to 3NF ensures structural integrity, while PHP PDO prepared statements eliminate SQL injection vulnerabilities.*

---

