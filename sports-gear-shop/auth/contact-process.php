<?php
// Establish secure route handling checks
if (isset($_POST['submit_contact'])) {

    // Capturing and running input processing sanitization filters
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email    = htmlspecialchars(trim($_POST['email']));
    $subject  = htmlspecialchars($_POST['subject']);
    $message  = htmlspecialchars(trim($_POST['message']));

    // Server-side fallback confirmation checks
    if (empty($fullname) || empty($email) || empty($message)) {
        echo "Processing Error: Complete all mandatory fields before transmission.";
        exit();
    }

    header("Location: contact.php?success=true");
    exit();

} else {
    // Prevent manual URL traversal exploitation
    header("Location: contact.php");
    exit();
}
?>