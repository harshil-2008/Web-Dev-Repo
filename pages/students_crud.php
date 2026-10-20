<?php
/* ====================================================================
   StudentHub - Student Management CRUD Module (students_crud.php)
   Practical 11: Student Management CRUD with Search and Filter
   Author: Harshil Prajapati (ID: 25DCE093)
   ==================================================================== */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db_connect.php';

// Flash message handlers
$successMsg = '';
$errorMsg = '';

// Check if CSV Export is requested
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=studenthub_students_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Student ID', 'Full Name', 'Email', 'Mobile', 'Course', 'Year', 'Gender', 'Status', 'Registered Date']);
    
    if ($conn) {
        $result = mysqli_query($conn, "SELECT student_id, name, email, mobile, course, year, gender, status, created_at FROM students WHERE status != 'deleted' ORDER BY student_id ASC");
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, $row);
        }
    }
    fclose($output);
    exit;
}

// 1. CREATE (Insert new student)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year   = intval($_POST['year'] ?? 1);
    $gender = trim($_POST['gender'] ?? 'Male');

    if (empty($name) || empty($email) || empty($mobile) || empty($course)) {
        $errorMsg = "All student fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Invalid email format.";
    } else {
        if ($conn) {
            $stmt = mysqli_prepare($conn, "INSERT INTO students (name, email, mobile, course, year, gender, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            mysqli_stmt_bind_param($stmt, "ssssis", $name, $email, $mobile, $course, $year, $gender);
            if (mysqli_stmt_execute($stmt)) {
                $newId = mysqli_insert_id($conn);
                $successMsg = "Student record #{$newId} ({$name}) created successfully!";
                recordAuditLog('INSERT', 'students', "Created student record #{$newId} ({$name})");
            } else {
                $errorMsg = "Failed to insert record: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $successMsg = "Demo: Student {$name} added successfully (mock mode).";
        }
    }
}

// 2. UPDATE (Edit existing student)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id     = intval($_POST['student_id'] ?? 0);
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $year   = intval($_POST['year'] ?? 1);
    $gender = trim($_POST['gender'] ?? 'Male');

    if ($id <= 0 || empty($name) || empty($email)) {
        $errorMsg = "Invalid student details provided for update.";
    } else {
        if ($conn) {
            $stmt = mysqli_prepare($conn, "UPDATE students SET name = ?, email = ?, mobile = ?, course = ?, year = ?, gender = ? WHERE student_id = ?");
            mysqli_stmt_bind_param($stmt, "ssssisi", $name, $email, $mobile, $course, $year, $gender, $id);
            if (mysqli_stmt_execute($stmt)) {
                $successMsg = "Student record #{$id} updated successfully!";
                recordAuditLog('UPDATE', 'students', "Updated student record #{$id} ({$name})");
            } else {
                $errorMsg = "Failed to update record: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $successMsg = "Demo: Student record #{$id} updated successfully (mock mode).";
        }
    }
}

// 3. DELETE (Soft delete using status column)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id > 0) {
        if ($conn) {
            // Soft delete to preserve historical relational integrity
            $stmt = mysqli_prepare($conn, "UPDATE students SET status = 'deleted' WHERE student_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $successMsg = "Student record #{$id} deleted successfully (soft-delete).";
                recordAuditLog('DELETE', 'students', "Soft deleted student record #{$id}");
            } else {
                $errorMsg = "Error deleting student: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $successMsg = "Demo: Student #{$id} deleted (mock mode).";
        }
    }
}

// 4. READ & SEARCH & FILTER (Fetch matching records)
$search = trim($_GET['search'] ?? '');
$courseFilter = trim($_GET['course_filter'] ?? '');
$yearFilter = trim($_GET['year_filter'] ?? '');

$students = [];
if ($conn) {
    $sql = "SELECT * FROM students WHERE status != 'deleted'";
    $params = [];
    $types = "";

    if (!empty($search)) {
        $sql .= " AND (name LIKE ? OR email LIKE ? OR mobile LIKE ?)";
        $searchParam = "%{$search}%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= "sss";
    }

    if (!empty($courseFilter)) {
        $sql .= " AND course = ?";
        $params[] = $courseFilter;
        $types .= "s";
    }

    if (!empty($yearFilter)) {
        $sql .= " AND year = ?";
        $params[] = intval($yearFilter);
        $types .= "i";
    }

    $sql .= " ORDER BY student_id DESC";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // 20+ Demo seeded records when MySQL server is not active
    $courses = ['Computer Engineering', 'Information Technology', 'Computer Science', 'Mechanical Engineering', 'Electronics & Communication'];
    for ($i = 1; $i <= 22; $i++) {
        $c = $courses[($i - 1) % count($courses)];
        $y = (($i - 1) % 4) + 1;
        $g = ($i % 2 === 0) ? 'Female' : 'Male';
        $names = ['Harshil Prajapati', 'Natasha Romanoff', 'Aarav Patel', 'Diya Sharma', 'Rohan Mehta', 'Ananya Iyer', 'Devansh Shah', 'Ishita Joshi', 'Kabir Verma', 'Meera Nair', 'Pranav Desai', 'Riya Kapoor', 'Siddharth Rao', 'Tanvi Trivedi', 'Utkarsh Bhatt', 'Vaidehi Dave', 'Yash Pandya', 'Zoya Khan', 'Aditya Choudhary', 'Bhavya Parikh', 'Chirag Solanki', 'Drashti Vyas'];
        $n = $names[($i - 1) % count($names)];
        if (empty($search) || stripos($n, $search) !== false || stripos($c, $search) !== false) {
            if (empty($courseFilter) || $courseFilter === $c) {
                if (empty($yearFilter) || intval($yearFilter) === $y) {
                    $students[] = [
                        'student_id' => $i,
                        'name' => $n,
                        'email' => strtolower(str_replace(' ', '.', $n)) . '@charusat.edu.in',
                        'mobile' => '98' . str_pad($i * 411, 8, '0', STR_PAD_LEFT),
                        'course' => $c,
                        'year' => $y,
                        'gender' => $g,
                        'status' => 'active',
                        'created_at' => date('Y-m-d H:i:s', strtotime("-{$i} days"))
                    ];
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management CRUD | StudentHub</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .crud-container {
            margin: 30px auto;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            padding: 25px;
        }
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .filter-inputs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .filter-inputs input, .filter-inputs select {
            padding: 10px 14px;
            border-radius: 8px;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 14px;
        }
        table.crud-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table.crud-table th, table.crud-table td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        table.crud-table th {
            background: rgba(15, 23, 42, 0.9);
            color: #93c5fd;
            font-weight: 600;
        }
        table.crud-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            border: none;
        }
        .btn-edit { background: #3b82f6; color: white; }
        .btn-delete { background: #ef4444; color: white; }
        .btn-export { background: #10b981; color: white; }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            padding: 25px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="home.html" class="logo">StudentHub</a>
            <button class="menu-btn" id="menuBtn" type="button">☰</button>
            <div class="nav-actions" id="navActions">
                <nav id="navbar">
                    <a href="home.html">Home</a>
                    <a href="admin_dashboard.php">Admin Dashboard</a>
                    <a href="students_crud.php" style="color: #60a5fa; font-weight: bold;">Manage Students</a>
                    <a href="events_crud.php">Manage Events</a>
                    <a href="ajax_crud.html">AJAX Portal</a>
                    <a href="logout.php">Logout</a>
                </nav>
                <button class="theme-btn" id="themeBtn" type="button">☀️ Light</button>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="hero" style="padding-bottom: 5px;">
            <div class="hero-content">
                <h1>Student Management CRUD Module</h1>
                <p>Add, search, filter, edit, soft-delete, and export student academic records.</p>
            </div>
        </section>

        <?php if (!empty($successMsg)): ?>
            <div class="error-summary" style="display:block; margin: 15px auto; max-width: 1000px; background: rgba(34, 197, 94, 0.2); border-color: #22c55e; color: #86efac;">
                ✅ <?php echo htmlspecialchars($successMsg); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="error-summary" style="display:block; margin: 15px auto; max-width: 1000px; background: rgba(239, 68, 68, 0.2); border-color: #ef4444; color: #fca5a5;">
                ⚠️ <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <div class="crud-container">
            <div class="filter-bar">
                <form method="GET" action="students_crud.php" class="filter-inputs">
                    <input type="text" name="search" placeholder="🔍 Search name, email..." value="<?php echo htmlspecialchars($search); ?>">
                    <select name="course_filter">
                        <option value="">All Departments</option>
                        <option value="Computer Engineering" <?php if ($courseFilter === 'Computer Engineering') echo 'selected'; ?>>Computer Engineering</option>
                        <option value="Computer Science" <?php if ($courseFilter === 'Computer Science') echo 'selected'; ?>>Computer Science</option>
                        <option value="Information Technology" <?php if ($courseFilter === 'Information Technology') echo 'selected'; ?>>Information Technology</option>
                        <option value="Electronics & Communication" <?php if ($courseFilter === 'Electronics & Communication') echo 'selected'; ?>>Electronics & Comm.</option>
                        <option value="Mechanical Engineering" <?php if ($courseFilter === 'Mechanical Engineering') echo 'selected'; ?>>Mechanical Eng.</option>
                    </select>
                    <select name="year_filter">
                        <option value="">All Years</option>
                        <option value="1" <?php if ($yearFilter === '1') echo 'selected'; ?>>Year 1</option>
                        <option value="2" <?php if ($yearFilter === '2') echo 'selected'; ?>>Year 2</option>
                        <option value="3" <?php if ($yearFilter === '3') echo 'selected'; ?>>Year 3</option>
                        <option value="4" <?php if ($yearFilter === '4') echo 'selected'; ?>>Year 4</option>
                    </select>
                    <button type="submit" class="btn btn-sm" style="background:#3b82f6;">Apply Filter</button>
                    <?php if (!empty($search) || !empty($courseFilter) || !empty($yearFilter)): ?>
                        <a href="students_crud.php" class="btn btn-sm" style="background:#64748b;">Reset</a>
                    <?php endif; ?>
                </form>

                <div style="display:flex; gap: 10px;">
                    <button class="btn btn-sm btn-export" onclick="window.location.href='students_crud.php?action=export_csv'">📥 Export CSV</button>
                    <button class="btn btn-sm" style="background:#22c55e;" onclick="openCreateModal()">➕ Add Student</button>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Email Address</th>
                            <th>Mobile</th>
                            <th>Department</th>
                            <th>Year</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; color: #94a3b8; padding: 25px;">No student records found matching the query.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $s): ?>
                                <tr>
                                    <td><strong>#<?php echo $s['student_id']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($s['name']); ?></td>
                                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                                    <td><?php echo htmlspecialchars($s['mobile']); ?></td>
                                    <td><?php echo htmlspecialchars($s['course']); ?></td>
                                    <td>Year <?php echo $s['year']; ?></td>
                                    <td><?php echo htmlspecialchars($s['gender']); ?></td>
                                    <td>
                                        <span class="status-badge" style="background: <?php echo $s['status'] === 'active' ? '#10b981' : '#f59e0b'; ?>">
                                            <?php echo htmlspecialchars($s['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-sm btn-edit" onclick='openEditModal(<?php echo json_encode($s); ?>)'>✏️ Edit</button>
                                        <button class="btn-sm btn-delete" onclick="confirmDelete(<?php echo $s['student_id']; ?>, '<?php echo addslashes($s['name']); ?>')">🗑️ Delete</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 15px; font-size: 13px; color: #94a3b8;">
                Showing <?php echo count($students); ?> student records. Database uses Prepared Statements for SQL injection defense.
            </div>
        </div>
    </main>

    <!-- Modal for Adding / Editing Student -->
    <div id="studentModal" class="modal-overlay">
        <div class="modal-box">
            <h2 id="modalTitle" style="color: #60a5fa; margin-top:0;">Add New Student</h2>
            <form id="studentForm" method="POST" action="students_crud.php">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="student_id" id="modalStudentId" value="">

                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="display:block; margin-bottom: 4px; font-size: 13px;">Full Name *</label>
                    <input type="text" name="name" id="modalName" required style="width: 100%; padding: 8px 10px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white;">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="display:block; margin-bottom: 4px; font-size: 13px;">Email Address *</label>
                    <input type="email" name="email" id="modalEmail" required style="width: 100%; padding: 8px 10px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white;">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="display:block; margin-bottom: 4px; font-size: 13px;">Mobile (10 digits) *</label>
                    <input type="tel" name="mobile" id="modalMobile" required pattern="[0-9]{10}" style="width: 100%; padding: 8px 10px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white;">
                </div>

                <div class="form-group" style="margin-bottom: 12px;">
                    <label style="display:block; margin-bottom: 4px; font-size: 13px;">Course / Department *</label>
                    <select name="course" id="modalCourse" required style="width: 100%; padding: 8px 10px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white;">
                        <option value="Computer Engineering">Computer Engineering</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Electronics & Communication">Electronics & Communication</option>
                        <option value="Mechanical Engineering">Mechanical Engineering</option>
                    </select>
                </div>

                <div style="display:flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="display:block; margin-bottom: 4px; font-size: 13px;">Year *</label>
                        <select name="year" id="modalYear" style="width: 100%; padding: 8px 10px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white;">
                            <option value="1">Year 1</option>
                            <option value="2">Year 2</option>
                            <option value="3">Year 3</option>
                            <option value="4">Year 4</option>
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label style="display:block; margin-bottom: 4px; font-size: 13px;">Gender *</label>
                        <select name="gender" id="modalGender" style="width: 100%; padding: 8px 10px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white;">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <div style="display:flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn btn-sm" style="background:#64748b;" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-sm" style="background:#22c55e;">Save Record</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>StudentHub</h3>
                    <p>Your one-stop platform for managing academic activities, college events, clubs, announcements, and your student profile.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="home.html">Home</a></li>
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="events.html">Events</a></li>
                        <li><a href="dashboard.html">Dashboard</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <ul class="footer-links">
                        <li><a href="FAQs.html">FAQ</a></li>
                        <li><a href="contact.html">Contact Us</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2026 StudentHub | All Rights Reserved</p>
            </div>
        </div>
    </footer>

    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Add New Student Record';
            document.getElementById('formAction').value = 'create';
            document.getElementById('modalStudentId').value = '';
            document.getElementById('studentForm').reset();
            document.getElementById('studentModal').classList.add('active');
        }

        function openEditModal(student) {
            document.getElementById('modalTitle').textContent = 'Edit Student Record #' + student.student_id;
            document.getElementById('formAction').value = 'update';
            document.getElementById('modalStudentId').value = student.student_id;
            document.getElementById('modalName').value = student.name;
            document.getElementById('modalEmail').value = student.email;
            document.getElementById('modalMobile').value = student.mobile;
            document.getElementById('modalCourse').value = student.course;
            document.getElementById('modalYear').value = student.year;
            document.getElementById('modalGender').value = student.gender;
            document.getElementById('studentModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('studentModal').classList.remove('active');
        }

        function confirmDelete(id, name) {
            if (confirm("Are you sure you want to delete student: " + name + " (ID #" + id + ")?\nThis action will soft-delete the record.")) {
                window.location.href = "students_crud.php?action=delete&id=" + id;
            }
        }
    </script>
    <script src="script.js"></script>
</body>
</html>
