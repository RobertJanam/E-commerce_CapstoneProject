<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Arena Support - Sports Gear Shop</title>
    <link rel="stylesheet" href="assets/css/contact.css">
</head>

<body>

<div class="contact-card">
    <h2>Get in Touch</h2>
    <p>Have issues with jersey sizes or stock deliveries? Drop our support team a message.</p>

    <?php if (isset($_GET['success'])): ?>
        <div class="status-alert">
            Your message has been sent! We will reach out as quick as possible.
        </div>
    <?php endif; ?>

    <form id="contactForm" action="contact-process.php" method="POST">
        <div class="input-group">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" placeholder="e.g. John Doe">
        </div>

        <div class="input-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="name@domain.com">
        </div>

        <div class="input-group">
            <label for="subject">Inquiry Subject</label>
            <select id="subject" name="subject">
                <option value="Order Tracking">Order Delivery / Tracking</option>
                <option value="Stock Availability">Bulk Team Kits & Restocks</option>
                <option value="Account Issue">Profile / Login Difficulties</option>
            </select>
        </div>

        <div class="input-group">
            <label for="message">Message Context</label>
            <textarea id="message" name="message" placeholder="Type details of your message here..."></textarea>
        </div>

        <button type="submit" name="submit_contact">Send Message</button>
    </form>
</div>

<script src="assets/js/contact.js"></script>
</body>
</html>