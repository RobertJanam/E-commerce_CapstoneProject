<?php
session_start();

if (isset($_POST['action']) && $_POST['action'] === 'terminate') {
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    header("Location: ../login.php");
    exit();
}

$displayName = 'Staff Member';
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $displayName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Logout | APEX SPRINT</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #0b0c10;
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .logout-container {
            background: linear-gradient(145deg, #1f2833, #151a21);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-top: 3px solid #ff0055; /* Crimson indicator accent */
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5), 0 0 25px rgba(255, 0, 85, 0.15);
            padding: 40px;
            border-radius: 8px;
            text-align: center;
            max-width: 450px;
            width: 90%;
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .logout-container:hover {
            transform: translateY(-5px);
        }

        .logo-display {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 20px;
            color: #fff;
        }

        .logo-accent {
            color: #ff0055;
        }

        .logout-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .logout-desc {
            color: #c5a880;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .user-highlight {
            color: #66fcf1;
            font-weight: 600;
        }

        .action-button-matrix {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            display: inline-block;
        }

        .btn-terminate {
            background-color: #ff0055;
            color: #ffffff;
            border: 1px solid #ff0055;
            box-shadow: 0 4px 12px rgba(255, 0, 85, 0.3);
        }

        .btn-terminate:hover {
            background-color: #d60048;
            box-shadow: 0 6px 20px rgba(255, 0, 85, 0.5);
        }

        .btn-cancel {
            background-color: transparent;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-cancel:hover {
            background-color: rgba(255, 255, 255, 0.08);
            border-color: #ffffff;
        }
    </style>
</head>
<body>

<div class="logout-container">
    <div class="logo-display">APEX<span class="logo-accent">SPRINT</span></div>
    <h2 class="logout-title">Logout</h2>
    <p class="logout-desc">
        Attention <span class="user-highlight"><?php echo $displayName; ?></span>.
        Are you sure you want to log out? Ending this session will revoke your access.
    </p>

    <div class="action-button-matrix">
        <a href="../admin/index.php" class="btn btn-cancel">Cancel</a>

        <form action="logout.php" method="POST" style="margin: 0;">
            <input type="hidden" name="action" value="terminate">
            <button type="submit" class="btn btn-terminate">Logout</button>
        </form>
    </div>
</div>

</body>
</html>