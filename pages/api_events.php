<?php
/* ====================================================================
   StudentHub - RESTful JSON API Endpoint (api_events.php)
   Practical 13: PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload
   Author: Harshil Prajapati (ID: 25DCE093)
   ==================================================================== */

// Set Content-Type header to JSON and UTF-8
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Respond to preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db_connect.php';

// Helper function for centralized JSON responses with HTTP status codes
function sendJsonResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// File-based fallback data store for standalone execution without MySQL daemon
$jsonStoreFile = __DIR__ . '/events_store.json';
if (!file_exists($jsonStoreFile)) {
    $seedEvents = [
        ["event_id" => 1, "title" => "Hackathon 2026", "description" => "24-hour campus coding competition solving real-world challenges.", "event_date" => "2026-11-15", "venue" => "Innovation Hall", "category" => "Competition", "status" => "Upcoming"],
        ["event_id" => 2, "title" => "Cultural Fest: Spandan", "description" => "Annual grand cultural celebration with music, dance, and arts.", "event_date" => "2026-11-28", "venue" => "Open Ground", "category" => "Cultural", "status" => "Upcoming"],
        ["event_id" => 3, "title" => "Robotics & AI Workshop", "description" => "Hands-on robotics and computer vision workshop.", "event_date" => "2026-12-05", "venue" => "Lab 302", "category" => "Workshop", "status" => "Upcoming"],
        ["event_id" => 4, "title" => "Cyber Security Keynote", "description" => "Zero-day exploits, OWASP top 10, and ethical defense.", "event_date" => "2026-12-18", "venue" => "Seminar Hall 1", "category" => "Seminar", "status" => "Upcoming"]
    ];
    file_put_contents($jsonStoreFile, json_encode($seedEvents, JSON_PRETTY_PRINT));
}

// --------------------------------------------------------------------
// 1. GET: Fetch / Search / Filter Events
// --------------------------------------------------------------------
if ($method === 'GET') {
    $search = trim($_GET['search'] ?? '');
    $category = trim($_GET['category'] ?? '');

    if ($conn) {
        $sql = "SELECT * FROM events WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR description LIKE ? OR venue LIKE ?)";
            $sp = "%{$search}%";
            $params[] = $sp; $params[] = $sp; $params[] = $sp;
            $types .= "sss";
        }
        if (!empty($category)) {
            $sql .= " AND category = ?";
            $params[] = $category;
            $types .= "s";
        }
        $sql .= " ORDER BY event_date ASC";

        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            if (!empty($params)) {
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $events = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $events[] = $row;
            }
            mysqli_stmt_close($stmt);
            sendJsonResponse(200, ["status" => "success", "count" => count($events), "data" => $events]);
        } else {
            sendJsonResponse(500, ["status" => "error", "message" => "Database query failed"]);
        }
    } else {
        // Fallback from JSON file
        $events = json_decode(file_get_contents($jsonStoreFile), true) ?: [];
        if (!empty($search)) {
            $events = array_values(array_filter($events, function($e) use ($search) {
                return stripos($e['title'], $search) !== false || stripos($e['venue'], $search) !== false;
            }));
        }
        if (!empty($category)) {
            $events = array_values(array_filter($events, function($e) use ($category) {
                return $e['category'] === $category;
            }));
        }
        sendJsonResponse(200, ["status" => "success", "count" => count($events), "data" => $events]);
    }
}

// --------------------------------------------------------------------
// 2. POST: Create New Event
// --------------------------------------------------------------------
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }

    $title = trim($input['title'] ?? '');
    $description = trim($input['description'] ?? '');
    $eventDate = trim($input['event_date'] ?? '');
    $venue = trim($input['venue'] ?? '');
    $category = trim($input['category'] ?? 'Technical');

    // Validation
    if (empty($title) || empty($eventDate) || empty($venue)) {
        sendJsonResponse(400, [
            "status" => "error",
            "message" => "Validation failed: 'title', 'event_date', and 'venue' are required fields."
        ]);
    }

    if ($conn) {
        $stmt = mysqli_prepare($conn, "INSERT INTO events (title, description, event_date, venue, category) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss", $title, $description, $eventDate, $venue, $category);
        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            sendJsonResponse(201, [
                "status" => "success",
                "message" => "Event created successfully.",
                "data" => ["event_id" => $newId, "title" => $title, "event_date" => $eventDate, "venue" => $venue, "category" => $category]
            ]);
        } else {
            sendJsonResponse(500, ["status" => "error", "message" => "Failed to insert record: " . mysqli_error($conn)]);
        }
    } else {
        $events = json_decode(file_get_contents($jsonStoreFile), true) ?: [];
        $newId = time();
        $newEvent = ["event_id" => $newId, "title" => $title, "description" => $description, "event_date" => $eventDate, "venue" => $venue, "category" => $category, "status" => "Upcoming"];
        $events[] = $newEvent;
        file_put_contents($jsonStoreFile, json_encode($events, JSON_PRETTY_PRINT));
        sendJsonResponse(201, ["status" => "success", "message" => "Event created successfully (file store).", "data" => $newEvent]);
    }
}

// --------------------------------------------------------------------
// 3. DELETE: Remove Event
// --------------------------------------------------------------------
if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = intval($input['id'] ?? 0);
    }

    if ($id <= 0) {
        sendJsonResponse(400, ["status" => "error", "message" => "Missing or invalid 'id' parameter."]);
    }

    if ($conn) {
        $stmt = mysqli_prepare($conn, "DELETE FROM events WHERE event_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($affected > 0) {
            sendJsonResponse(200, ["status" => "success", "message" => "Event #{$id} deleted successfully."]);
        } else {
            sendJsonResponse(404, ["status" => "error", "message" => "Event #{$id} not found."]);
        }
    } else {
        $events = json_decode(file_get_contents($jsonStoreFile), true) ?: [];
        $initialCount = count($events);
        $events = array_values(array_filter($events, fn($e) => intval($e['event_id']) !== $id));
        if (count($events) < $initialCount) {
            file_put_contents($jsonStoreFile, json_encode($events, JSON_PRETTY_PRINT));
            sendJsonResponse(200, ["status" => "success", "message" => "Event #{$id} deleted successfully (file store)."]);
        } else {
            sendJsonResponse(404, ["status" => "error", "message" => "Event #{$id} not found."]);
        }
    }
}

sendJsonResponse(405, ["status" => "error", "message" => "Method Not Allowed"]);
?>
