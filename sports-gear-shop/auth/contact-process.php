<?php
if (isset($_POST['submit_contact'])) {

    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email    = htmlspecialchars(trim($_POST['email']));
    $subject  = htmlspecialchars($_POST['subject']);
    $message  = htmlspecialchars(trim($_POST['message']));

    if (empty($fullname) || empty($email) || empty($message)) {
        echo "Processing Error: Complete all mandatory fields before transmission.";
        exit();
    }

    header("Location: contact.php?success=true");
    exit();

} else {
    header("Location: contact.php");
    exit();
}
?>