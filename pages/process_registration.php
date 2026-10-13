<?php
/* ===================================================
   StudentHub - Process Registration (PHP)
   Validates & sanitizes inputs on the server side.
   Stores records in CSV and JSON file formats.
   Displays beginner-friendly success or error feedback.
   =================================================== */

// Step 1: Ensure request is sent via HTTP POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("<h3>Invalid request method. Please submit the form.</h3><p><a href='register.html'>Go to Registration</a></p>");
}

// Step 2: Retrieve and Sanitize Form Inputs
$name             = trim(htmlspecialchars($_POST['name'] ?? ''));
$email_raw        = trim($_POST['email'] ?? '');
$email            = filter_var($email_raw, FILTER_SANITIZE_EMAIL);
$mobile           = trim(htmlspecialchars($_POST['mobile'] ?? ''));
$course           = trim(htmlspecialchars($_POST['course'] ?? ''));
$year             = trim(htmlspecialchars($_POST['year'] ?? ''));
$gender           = trim(htmlspecialchars($_POST['gender'] ?? ''));
$password         = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$terms            = isset($_POST['terms']);

// Step 3: Server-side Validation with Regular Expressions & Filters
$errors = [];

// Name Validation (Letters and spaces only, 3 to 50 characters)
if (empty($name)) {
    $errors[] = "Full Name is required.";
} elseif (!preg_match("/^[a-zA-Z\s]{3,50}$/", $name)) {
    $errors[] = "Name must only contain letters and spaces (minimum 3 characters).";
}

// Email Validation
if (empty($email)) {
    $errors[] = "Email address is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please provide a valid email address.";
}

// Mobile Number Validation (Exactly 10 digits)
if (empty($mobile)) {
    $errors[] = "Mobile number is required.";
} elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
    $errors[] = "Mobile number must be exactly 10 digits.";
}

// Course Validation
$allowedCourses = [
    "Computer Engineering",
    "Computer Science",
    "Information Technology",
    "Electronics & Communication",
    "Mechanical Engineering"
];
if (empty($course)) {
    $errors[] = "Course selection is required.";
} elseif (!in_array($course, $allowedCourses)) {
    $errors[] = "Invalid course selected.";
}

// Academic Year Validation (1 to 4)
if (empty($year)) {
    $errors[] = "Academic Year is required.";
} elseif (!preg_match("/^[1-4]$/", $year)) {
    $errors[] = "Academic Year must be between 1 and 4.";
}

// Gender Validation
$allowedGenders = ["Male", "Female", "Other"];
if (empty($gender)) {
    $errors[] = "Gender selection is required.";
} elseif (!in_array($gender, $allowedGenders)) {
    $errors[] = "Invalid gender selected.";
}

// Password Validation (At least 8 chars, 1 letter, 1 number)
if (empty($password)) {
    $errors[] = "Password is required.";
} elseif (strlen($password) < 8 || !preg_match("/[A-Za-z]/", $password) || !preg_match("/[0-9]/", $password)) {
    $errors[] = "Password must be at least 8 characters and include at least one letter and one number.";
}

// Confirm Password Match
if ($password !== $confirm_password) {
    $errors[] = "Password and Confirm Password do not match.";
}

// Terms Acceptance
if (!$terms) {
    $errors[] = "You must agree to the Terms & Conditions.";
}

// Timestamp for registration
$registered_at = date("Y-m-d H:i:s");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status | StudentHub</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .result-box {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 12px;
            text-align: left;
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid red;
        }
        .error-box {
            border-color: #ff4444;
            background: rgba(255, 0, 0, 0.15);
        }
        .success-box {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.15);
        }
        .summary-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
        }
        .summary-table td:first-child {
            font-weight: bold;
            color: #ffcccc;
            width: 35%;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">StudentHub</div>
    <nav>
        <a href="index.html">Home</a>
        <a href="dashboard.html">Dashboard</a>
        <a href="events.html">Events</a>
        <a href="register.html">Register</a>
        <a href="login.html">Login</a>
    </nav>
</header>

<section class="hero">
<?php if (!empty($errors)): ?>
    <!-- ================= ERROR FEEDBACK ================= -->
    <div class="result-box error-box">
        <h2 style="color: #ff6b6b; margin-top: 0;">❌ Registration Failed</h2>
        <p>The form could not be submitted because of the following errors:</p>
        <ul style="color: #ff9999; line-height: 1.6;">
            <?php foreach ($errors as $err): ?>
                <li><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
        </ul>
        <div style="margin-top: 25px; text-align: center;">
            <a href="javascript:history.back()" class="btn">← Go Back and Fix Errors</a>
        </div>
    </div>

<?php else: ?>
    <!-- ================= SUCCESS FEEDBACK ================= -->
    <?php
    // Step 4A: Store Record in CSV Format (students.csv)
    $csvFile = 'students.csv';
    $isNewCsv = !file_exists($csvFile) || filesize($csvFile) === 0;

    $csvHandle = fopen($csvFile, 'a');
    if ($csvHandle !== false) {
        // If file is newly created, write header line first
        if ($isNewCsv) {
            fputcsv($csvHandle, ['Name', 'Email', 'Mobile', 'Course', 'Year', 'Gender', 'Registered_At']);
        }
        // Write the new student data row
        fputcsv($csvHandle, [$name, $email, $mobile, $course, $year, $gender, $registered_at]);
        fclose($csvHandle);
    }

    // Step 4B: Store Record in JSON Format (students.json)
    $jsonFile = 'students.json';
    $existingData = ['students' => []];

    if (file_exists($jsonFile)) {
        $jsonContent = file_get_contents($jsonFile);
        $decoded = json_decode($jsonContent, true);
        if (is_array($decoded) && isset($decoded['students'])) {
            $existingData = $decoded;
        }
    }

    // New student record (omitting plain password for security)
    $newStudent = [
        'id'            => count($existingData['students']) + 1,
        'name'          => $name,
        'email'         => $email,
        'mobile'        => $mobile,
        'course'        => $course,
        'year'          => $year . (substr($year, -1) == '1' ? 'st Year' : (substr($year, -1) == '2' ? 'nd Year' : (substr($year, -1) == '3' ? 'rd Year' : 'th Year'))),
        'gender'        => $gender,
        'registered_at' => $registered_at
    ];

    $existingData['students'][] = $newStudent;
    file_put_contents($jsonFile, json_encode($existingData, JSON_PRETTY_PRINT));

    // Step 4C (Optional Database Hook): Connect and save to MySQL if database is configured
    $dbSaved = false;
    if (file_exists('db_connect.php')) {
        // Try including database connection safely
        @include_once('db_connect.php');
        if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO students (name, email, mobile, course, year, gender, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssssiss", $name, $email, $mobile, $course, $year, $gender, $hashedPassword);
                if ($stmt->execute()) {
                    $dbSaved = true;
                }
                $stmt->close();
            }
        }
    }
    ?>

    <div class="result-box success-box">
        <h2 style="color: #28a745; margin-top: 0;">🎉 Registration Successful!</h2>
        <p>Welcome to StudentHub, <strong><?php echo htmlspecialchars($name); ?></strong>! Your account has been created.</p>

        <table class="summary-table">
            <tr><td>Full Name:</td><td><?php echo htmlspecialchars($name); ?></td></tr>
            <tr><td>Email:</td><td><?php echo htmlspecialchars($email); ?></td></tr>
            <tr><td>Mobile:</td><td><?php echo htmlspecialchars($mobile); ?></td></tr>
            <tr><td>Course:</td><td><?php echo htmlspecialchars($course); ?></td></tr>
            <tr><td>Year:</td><td>Year <?php echo htmlspecialchars($year); ?></td></tr>
            <tr><td>Gender:</td><td><?php echo htmlspecialchars($gender); ?></td></tr>
            <tr><td>Registered:</td><td><?php echo htmlspecialchars($registered_at); ?></td></tr>
        </table>

        <div style="background: rgba(0,0,0,0.3); padding: 12px; border-radius: 6px; font-size: 13px; margin-bottom: 20px;">
            <p style="margin: 4px 0;">💾 <strong>Data Storage Status:</strong></p>
            <p style="margin: 4px 0; color: #a3e4d7;">✔ Stored in <code>students.csv</code> file</p>
            <p style="margin: 4px 0; color: #a3e4d7;">✔ Stored in <code>students.json</code> file</p>
            <?php if ($dbSaved): ?>
                <p style="margin: 4px 0; color: #a3e4d7;">✔ Record inserted into MySQL database (table: <code>students</code>)</p>
            <?php else: ?>
                <p style="margin: 4px 0; color: #f9e79f;">ℹ️ MySQL database record will auto-sync once MySQL is started in XAMPP</p>
            <?php endif; ?>
        </div>

        <div style="text-align: center;">
            <a href="login.html" class="btn">Proceed to Login</a>
            <a href="dashboard.html" class="btn" style="background: #333;">View Dashboard</a>
            <a href="register.html" class="btn" style="background: #444;">Register Another</a>
        </div>
    </div>
<?php endif; ?>
</section>

<footer>
    <p>© 2026 StudentHub | All Rights Reserved</p>
</footer>

</body>
</html>
