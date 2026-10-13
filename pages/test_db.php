<?php
/* ====================================================================
   StudentHub - Database Test & Verification Page (test_db.php)
   Allows beginner students to verify MySQL connection and view records.
   ==================================================================== */

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "studenthub";

$statusMessage = "";
$conn = @mysqli_connect($servername, $username, $password);

if (!$conn) {
    $dbConnected = false;
    $statusMessage = "Cannot connect to MySQL server at '$servername'. Make sure MySQL is running in XAMPP.";
} else {
    // Check if database exists
    $dbSelect = @mysqli_select_db($conn, $dbname);
    
    // Auto-setup button handler
    if (isset($_POST['setup_db'])) {
        $sqlScript = file_get_contents('studenthub.sql');
        if ($sqlScript) {
            // Run multiple queries
            if (mysqli_multi_query($conn, $sqlScript)) {
                do {
                    // Flush multi_queries
                    if ($res = mysqli_store_result($conn)) {
                        mysqli_free_result($res);
                    }
                } while (mysqli_more_results($conn) && mysqli_next_result($conn));
                
                $statusMessage = "Database 'studenthub' and tables were successfully created & populated!";
                $dbSelect = @mysqli_select_db($conn, $dbname);
            } else {
                $statusMessage = "Error setting up database: " . mysqli_error($conn);
            }
        }
    }

    $dbConnected = $dbSelect;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Test | StudentHub</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container-box {
            max-width: 900px;
            margin: 30px auto;
            padding: 25px;
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid red;
            border-radius: 12px;
            text-align: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 25px 0;
            background: rgba(0,0,0,0.3);
        }
        th, td {
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 14px;
        }
        th {
            background: rgba(255, 0, 0, 0.4);
            color: white;
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
        <a href="test_db.php">DB Status</a>
    </nav>
</header>

<section class="hero">
    <h1>Database Connection Status</h1>
    <p>Testing connection using MySQLi statements.</p>

    <div class="container-box">
        <?php if (!empty($statusMessage)): ?>
            <div style="background: rgba(40,167,69,0.2); border: 1px solid #28a745; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($statusMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($dbConnected): ?>
            <div style="color: #28a745; font-size: 18px; font-weight: bold; margin-bottom: 15px;">
                ✔ Successfully Connected to MySQL Database: <code><?php echo $dbname; ?></code>
            </div>

            <!-- 1. Students Table -->
            <h3>1. Students Table (<code>students</code>)</h3>
            <?php
            $studentsQuery = mysqli_query($conn, "SELECT student_id, name, email, mobile, course, year, gender FROM students LIMIT 5");
            if ($studentsQuery && mysqli_num_rows($studentsQuery) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Course</th><th>Year</th><th>Gender</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($s = mysqli_fetch_assoc($studentsQuery)): ?>
                            <tr>
                                <td><?php echo $s['student_id']; ?></td>
                                <td><?php echo htmlspecialchars($s['name']); ?></td>
                                <td><?php echo htmlspecialchars($s['email']); ?></td>
                                <td><?php echo htmlspecialchars($s['mobile']); ?></td>
                                <td><?php echo htmlspecialchars($s['course']); ?></td>
                                <td><?php echo htmlspecialchars($s['year']); ?></td>
                                <td><?php echo htmlspecialchars($s['gender']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No records found in <code>students</code> table.</p>
            <?php endif; ?>

            <!-- 2. Events Table -->
            <h3>2. Events Table (<code>events</code>)</h3>
            <?php
            $eventsQuery = mysqli_query($conn, "SELECT event_id, title, category, event_date, venue FROM events LIMIT 5");
            if ($eventsQuery && mysqli_num_rows($eventsQuery) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Title</th><th>Category</th><th>Date</th><th>Venue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($e = mysqli_fetch_assoc($eventsQuery)): ?>
                            <tr>
                                <td><?php echo $e['event_id']; ?></td>
                                <td><?php echo htmlspecialchars($e['title']); ?></td>
                                <td><?php echo htmlspecialchars($e['category']); ?></td>
                                <td><?php echo htmlspecialchars($e['event_date']); ?></td>
                                <td><?php echo htmlspecialchars($e['venue']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No records found in <code>events</code> table.</p>
            <?php endif; ?>

            <!-- 3. Registrations Table -->
            <h3>3. Registrations Table (<code>registrations</code>)</h3>
            <?php
            $regQuery = mysqli_query($conn, "SELECT r.registration_id, s.name AS student_name, e.title AS event_title, r.registration_date, r.status 
                                             FROM registrations r 
                                             JOIN students s ON r.student_id = s.student_id 
                                             JOIN events e ON r.event_id = e.event_id 
                                             LIMIT 5");
            if ($regQuery && mysqli_num_rows($regQuery) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Reg ID</th><th>Student Name</th><th>Event Title</th><th>Date</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($r = mysqli_fetch_assoc($regQuery)): ?>
                            <tr>
                                <td><?php echo $r['registration_id']; ?></td>
                                <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($r['event_title']); ?></td>
                                <td><?php echo htmlspecialchars($r['registration_date']); ?></td>
                                <td><?php echo htmlspecialchars($r['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No records found in <code>registrations</code> table.</p>
            <?php endif; ?>

        <?php else: ?>
            <div style="color: #ff6b6b; font-size: 16px; margin-bottom: 15px;">
                ⚠️ Database <code><?php echo $dbname; ?></code> is not found or MySQL is not running.
            </div>
            <p>If MySQL is running in XAMPP, click the button below to automatically create and populate the database:</p>
            <form method="POST">
                <button type="submit" name="setup_db" class="btn">🚀 Initialize Database & Tables</button>
            </form>
        <?php endif; ?>

        <div style="margin-top: 25px; text-align: center;">
            <a href="register.html" class="btn">Go to Registration</a>
            <a href="dashboard.html" class="btn" style="background:#333;">Dashboard</a>
        </div>
    </div>
</section>

<footer>
    <p>© 2026 StudentHub | All Rights Reserved</p>
</footer>

</body>
</html>
