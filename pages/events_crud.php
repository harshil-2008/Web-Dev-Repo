<?php
/* ====================================================================
   StudentHub - Event Management CRUD Module (events_crud.php)
   Practical 12: Event Management CRUD with Poster Upload and File Validation
   Author: Harshil Prajapati (ID: 25DCE093)
   ==================================================================== */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db_connect.php';

$successMsg = '';
$errorMsg = '';
$uploadDir = __DIR__ . '/uploads/';

// Ensure uploads folder exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// 1. CREATE EVENT WITH POSTER UPLOAD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $eventDate   = trim($_POST['event_date'] ?? '');
    $venue       = trim($_POST['venue'] ?? '');
    $category    = trim($_POST['category'] ?? 'Technical');
    $status      = trim($_POST['status'] ?? 'Upcoming');
    $posterName  = 'default_event.jpg';

    // File Upload Validation Logic
    $fileUploaded = isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE;

    if (empty($title) || empty($eventDate) || empty($venue)) {
        $errorMsg = "Event title, date, and venue are mandatory fields.";
    } elseif ($fileUploaded) {
        $file = $_FILES['poster'];
        $maxSize = 2 * 1024 * 1024; // 2 MB limit
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // 1. Check upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = "File upload failed with code: " . $file['error'];
        }
        // 2. Validate file size
        elseif ($file['size'] > $maxSize) {
            $errorMsg = "File size exceeds 2 MB limit (Uploaded: " . round($file['size'] / 1024 / 1024, 2) . " MB).";
        }
        // 3. Validate file extension
        elseif (!in_array($fileExt, $allowedExtensions)) {
            $errorMsg = "Invalid file extension. Allowed formats: JPG, PNG, WEBP.";
        } else {
            // 4. Validate MIME type using finfo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowedMimes)) {
                $errorMsg = "Security rejection: Invalid file MIME type ({$mime}).";
            } else {
                // 5. Unique secure filename generation
                $posterName = uniqid('poster_', true) . '.' . $fileExt;
                $targetPath = $uploadDir . $posterName;
                if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $errorMsg = "Failed to save uploaded poster file.";
                }
            }
        }
    }

    // Insert to DB if no validation errors
    if (empty($errorMsg)) {
        if ($conn) {
            $stmt = mysqli_prepare($conn, "INSERT INTO events (title, description, event_date, venue, category, poster, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssssss", $title, $description, $eventDate, $venue, $category, $posterName, $status);
            if (mysqli_stmt_execute($stmt)) {
                $newId = mysqli_insert_id($conn);
                $successMsg = "Event '{$title}' created successfully with poster!";
                recordAuditLog('INSERT', 'events', "Created event #{$newId} ({$title})");
            } else {
                $errorMsg = "Database error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $successMsg = "Demo: Event '{$title}' added successfully with poster '{$posterName}' (mock mode).";
        }
    }
}

// 2. DELETE EVENT
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($conn) {
        // Fetch poster filename to clean up storage
        $posterRes = mysqli_query($conn, "SELECT poster, title FROM events WHERE event_id = {$id}");
        $row = mysqli_fetch_assoc($posterRes);
        if ($row && !empty($row['poster']) && $row['poster'] !== 'default_event.jpg') {
            @unlink($uploadDir . $row['poster']);
        }
        $stmt = mysqli_prepare($conn, "DELETE FROM events WHERE event_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $successMsg = "Event #{$id} deleted successfully!";
            recordAuditLog('DELETE', 'events', "Deleted event #{$id}");
        }
        mysqli_stmt_close($stmt);
    } else {
        $successMsg = "Demo: Event #{$id} deleted (mock mode).";
    }
}

// 3. READ & FILTER EVENTS
$catFilter = trim($_GET['cat_filter'] ?? '');
$statusFilter = trim($_GET['status_filter'] ?? '');

$eventsList = [];
if ($conn) {
    $sql = "SELECT * FROM events WHERE 1=1";
    if (!empty($catFilter)) {
        $sql .= " AND category = '" . mysqli_real_escape_string($conn, $catFilter) . "'";
    }
    if (!empty($statusFilter)) {
        $sql .= " AND status = '" . mysqli_real_escape_string($conn, $statusFilter) . "'";
    }
    $sql .= " ORDER BY event_date ASC";
    $res = mysqli_query($conn, $sql);
    while ($r = mysqli_fetch_assoc($res)) {
        $eventsList[] = $r;
    }
} else {
    // Fallback demo events
    $eventsList = [
        ['event_id' => 1, 'title' => 'Hackathon 2026', 'description' => '24-hour campus coding competition.', 'event_date' => '2026-11-15', 'venue' => 'Innovation Hall', 'category' => 'Competition', 'poster' => 'hackathon.png', 'status' => 'Upcoming'],
        ['event_id' => 2, 'title' => 'Cultural Fest: Spandan', 'description' => 'Annual grand cultural celebration.', 'event_date' => '2026-11-28', 'venue' => 'Open Grounds', 'category' => 'Cultural', 'poster' => 'spandan.png', 'status' => 'Upcoming'],
        ['event_id' => 3, 'title' => 'Robotics & AI Workshop', 'description' => 'Autonomous bot creation hands-on.', 'event_date' => '2026-12-05', 'venue' => 'Lab 302', 'category' => 'Workshop', 'poster' => 'robotics.png', 'status' => 'Upcoming'],
        ['event_id' => 4, 'title' => 'Cyber Security Summit', 'description' => 'Web exploits and ethical defense strategies.', 'event_date' => '2026-12-18', 'venue' => 'Seminar Hall 1', 'category' => 'Seminar', 'poster' => 'cybersec.png', 'status' => 'Upcoming'],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management CRUD | StudentHub</title>
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
        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }
        .event-card {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .event-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }
        .poster-preview {
            width: 100%;
            height: 180px;
            background: #1e1b4b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #93c5fd;
            font-size: 40px;
            overflow: hidden;
        }
        .poster-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .event-body {
            padding: 18px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .badge-cat {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            background: #3b82f6;
            color: white;
            margin-bottom: 8px;
            width: fit-content;
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
                    <a href="students_crud.php">Manage Students</a>
                    <a href="events_crud.php" style="color: #60a5fa; font-weight: bold;">Manage Events</a>
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
                <h1>Campus Event Management & Poster Upload</h1>
                <p>Add new campus events, upload posters with MIME/size validation, and administer registrations.</p>
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

        <!-- Create Event Form -->
        <div class="crud-container">
            <h2 style="color: #60a5fa; margin-top: 0;">➕ Create New Event</h2>
            <form action="events_crud.php" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px;">
                <input type="hidden" name="action" value="create">

                <div>
                    <label style="display:block; margin-bottom: 4px; font-size: 13px;">Event Title *</label>
                    <input type="text" name="title" required placeholder="e.g. AI Innovation Summit 2026" style="width: 100%; padding: 10px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white;">
                </div>

                <div>
                    <label style="display:block; margin-bottom: 4px; font-size: 13px;">Event Date *</label>
                    <input type="date" name="event_date" required style="width: 100%; padding: 10px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white;">
                </div>

                <div>
                    <label style="display:block; margin-bottom: 4px; font-size: 13px;">Venue / Location *</label>
                    <input type="text" name="venue" required placeholder="e.g. Auditorium A, CE Dept" style="width: 100%; padding: 10px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white;">
                </div>

                <div>
                    <label style="display:block; margin-bottom: 4px; font-size: 13px;">Category *</label>
                    <select name="category" style="width: 100%; padding: 10px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white;">
                        <option value="Technical">Technical</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Cultural">Cultural</option>
                        <option value="Sports">Sports</option>
                        <option value="Seminar">Seminar</option>
                        <option value="Competition">Competition</option>
                    </select>
                </div>

                <div style="grid-column: 1 / -1;">
                    <label style="display:block; margin-bottom: 4px; font-size: 13px;">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief synopsis of the event..." style="width: 100%; padding: 10px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white;"></textarea>
                </div>

                <div style="grid-column: 1 / -1;">
                    <label style="display:block; margin-bottom: 4px; font-size: 13px;">Event Poster (JPG, PNG, WEBP — Max 2 MB)</label>
                    <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp" style="padding: 8px; border-radius: 6px; background: #0f172a; border: 1px solid #475569; color: white; width: 100%;">
                    <small style="color: #94a3b8; display: block; margin-top: 4px;">Server validates MIME type using finfo_file and generates randomized secure filename.</small>
                </div>

                <div style="grid-column: 1 / -1; text-align: right;">
                    <button type="submit" class="btn" style="padding: 10px 24px;">Publish Event</button>
                </div>
            </form>
        </div>

        <!-- Event Cards Display -->
        <div class="crud-container">
            <div style="display:flex; justify-content: space-between; align-items:center; flex-wrap:wrap; gap: 15px;">
                <h2 style="color: #60a5fa; margin: 0;">📅 Published Campus Events</h2>
                <form method="GET" action="events_crud.php" style="display: flex; gap: 10px;">
                    <select name="cat_filter" onchange="this.form.submit()" style="padding: 8px; border-radius: 6px; background: #0f172a; color: white; border: 1px solid #475569;">
                        <option value="">All Categories</option>
                        <option value="Technical" <?php if ($catFilter === 'Technical') echo 'selected'; ?>>Technical</option>
                        <option value="Workshop" <?php if ($catFilter === 'Workshop') echo 'selected'; ?>>Workshop</option>
                        <option value="Cultural" <?php if ($catFilter === 'Cultural') echo 'selected'; ?>>Cultural</option>
                        <option value="Sports" <?php if ($catFilter === 'Sports') echo 'selected'; ?>>Sports</option>
                        <option value="Seminar" <?php if ($catFilter === 'Seminar') echo 'selected'; ?>>Seminar</option>
                        <option value="Competition" <?php if ($catFilter === 'Competition') echo 'selected'; ?>>Competition</option>
                    </select>
                </form>
            </div>

            <div class="event-grid">
                <?php foreach ($eventsList as $ev): ?>
                    <div class="event-card">
                        <div class="poster-preview">
                            <?php if (!empty($ev['poster']) && file_exists($uploadDir . $ev['poster'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($ev['poster']); ?>" alt="Event Poster">
                            <?php else: ?>
                                <span>📅</span>
                            <?php endif; ?>
                        </div>
                        <div class="event-body">
                            <span class="badge-cat"><?php echo htmlspecialchars($ev['category']); ?></span>
                            <h3 style="margin: 6px 0 10px 0; color: white; font-size: 18px;"><?php echo htmlspecialchars($ev['title']); ?></h3>
                            <p style="color: #94a3b8; font-size: 14px; margin-bottom: 12px; flex: 1;"><?php echo htmlspecialchars($ev['description'] ?? ''); ?></p>
                            <div style="font-size: 13px; color: #cbd5e1; margin-bottom: 12px;">
                                <div>📍 <strong>Venue:</strong> <?php echo htmlspecialchars($ev['venue']); ?></div>
                                <div>🗓️ <strong>Date:</strong> <?php echo htmlspecialchars($ev['event_date']); ?></div>
                                <div>🏷️ <strong>Status:</strong> <span class="status-badge" style="background:#10b981;"><?php echo htmlspecialchars($ev['status'] ?? 'Upcoming'); ?></span></div>
                            </div>
                            <div style="display:flex; justify-content: flex-end; gap: 8px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px;">
                                <a href="events_crud.php?action=delete&id=<?php echo $ev['event_id']; ?>" class="btn-sm btn-delete" onclick="return confirm('Delete event: <?php echo addslashes($ev['title']); ?>?')">🗑️ Delete Event</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

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
    <script src="script.js"></script>
</body>
</html>
