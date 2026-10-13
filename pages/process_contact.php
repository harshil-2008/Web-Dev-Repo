<?php
/* ===================================================
   StudentHub - Process Contact Form (PHP)
   Validates & sanitizes contact inputs on the server.
   Stores messages in CSV and JSON formats.
   Displays beginner-friendly success or error messages.
   =================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("<h3>Invalid request. Please submit the form.</h3><p><a href='contact.html'>Go Back</a></p>");
}

// 1. Sanitize Inputs
$name        = trim(htmlspecialchars($_POST["name"] ?? ""));
$email_raw   = trim($_POST["email"] ?? "");
$email       = filter_var($email_raw, FILTER_SANITIZE_EMAIL);
$message     = trim(htmlspecialchars($_POST["message"] ?? ""));
$submitted_at = date("Y-m-d H:i:s");

// 2. Server-side Validation
$errors = [];

if (empty($name)) {
    $errors[] = "Name is required.";
} elseif (!preg_match("/^[a-zA-Z\s]{2,50}$/", $name)) {
    $errors[] = "Name must contain only letters and spaces.";
}

if (empty($email)) {
    $errors[] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

if (empty($message)) {
    $errors[] = "Message cannot be empty.";
} elseif (strlen($message) < 5) {
    $errors[] = "Message must be at least 5 characters long.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Feedback | StudentHub</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .result-box {
            max-width: 550px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 12px;
            text-align: left;
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid red;
        }
        .error-box { border-color: #ff4444; background: rgba(255, 0, 0, 0.15); }
        .success-box { border-color: #28a745; background: rgba(40, 167, 69, 0.15); }
    </style>
</head>
<body>

<header>
    <div class="logo">StudentHub</div>
    <nav>
        <a href="index.html">Home</a>
        <a href="about.html">About</a>
        <a href="contact.html">Contact</a>
        <a href="dashboard.html">Dashboard</a>
    </nav>
</header>

<section class="hero">
<?php if (!empty($errors)): ?>
    <div class="result-box error-box">
        <h2 style="color: #ff6b6b; margin-top: 0;">❌ Submission Failed</h2>
        <ul>
            <?php foreach ($errors as $err): ?>
                <li style="color: #ff9999;"><?php echo htmlspecialchars($err); ?></li>
            <?php endforeach; ?>
        </ul>
        <div style="text-align: center; margin-top: 20px;">
            <a href="javascript:history.back()" class="btn">← Go Back and Try Again</a>
        </div>
    </div>
<?php else: ?>
    <?php
    // 3A. Save to CSV (contacts.csv)
    $csvFile = 'contacts.csv';
    $isNewCsv = !file_exists($csvFile) || filesize($csvFile) === 0;
    $csvHandle = fopen($csvFile, 'a');
    if ($csvHandle !== false) {
        if ($isNewCsv) {
            fputcsv($csvHandle, ['Name', 'Email', 'Message', 'Submitted_At']);
        }
        fputcsv($csvHandle, [$name, $email, $message, $submitted_at]);
        fclose($csvHandle);
    }

    // 3B. Save to JSON (contacts.json)
    $jsonFile = 'contacts.json';
    $contactsData = [];
    if (file_exists($jsonFile)) {
        $existing = json_decode(file_get_contents($jsonFile), true);
        if (is_array($existing)) {
            $contactsData = $existing;
        }
    }
    $contactsData[] = [
        'name'         => $name,
        'email'        => $email,
        'message'      => $message,
        'submitted_at' => $submitted_at
    ];
    file_put_contents($jsonFile, json_encode($contactsData, JSON_PRETTY_PRINT));
    ?>

    <div class="result-box success-box">
        <h2 style="color: #28a745; margin-top: 0;">✉️ Message Sent Successfully!</h2>
        <p>Thank you, <strong><?php echo htmlspecialchars($name); ?></strong>! Your message has been received.</p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>Your Message:</strong><br><em>"<?php echo nl2br(htmlspecialchars($message)); ?>"</em></p>
        <p style="font-size: 13px; color: #a3e4d7;">✔ Stored in <code>contacts.csv</code> and <code>contacts.json</code></p>
        <div style="text-align: center; margin-top: 20px;">
            <a href="contact.html" class="btn">Send Another Message</a>
            <a href="home.html" class="btn" style="background: #333;">Back to Home</a>
        </div>
    </div>
<?php endif; ?>
</section>

<footer>
    <p>© 2026 StudentHub | All Rights Reserved</p>
</footer>

</body>
</html>
