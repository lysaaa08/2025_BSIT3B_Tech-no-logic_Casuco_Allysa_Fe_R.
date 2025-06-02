<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT 
        m.sender_id,
        u.username,
        u.profile_pic,
        MAX(m.sent_at) AS last_message_time,
        (SELECT message FROM messages 
         WHERE (sender_id = m.sender_id AND receiver_id = ?) OR 
               (sender_id = ? AND receiver_id = m.sender_id) 
         ORDER BY sent_at DESC LIMIT 1) AS last_message,
        SUM(IF(m.receiver_id = ? AND m.is_read = 0, 1, 0)) AS unread_count
    FROM messages m
    JOIN users u ON u.id = m.sender_id
    WHERE m.receiver_id = ? OR m.sender_id = ?
    GROUP BY m.sender_id
    ORDER BY last_message_time DESC
");
$stmt->bind_param("iiiii", $current_user, $current_user, $current_user, $current_user, $current_user);
$stmt->execute();
$result = $stmt->get_result();

// Helper function for human-readable timestamps
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $difference = time() - $timestamp;

    if ($difference < 60) return 'Just now';
    elseif ($difference < 3600) return floor($difference / 60) . ' mins ago';
    elseif ($difference < 86400) return floor($difference / 3600) . ' hrs ago';
    else return date("M d, Y", $timestamp);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inbox | Eco Trade</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #e8f5e9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 720px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #388e3c;
            color: white;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        ul.message-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li.message-item {
            border-bottom: 1px solid #e0e0e0;
        }

        a.message-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            color: #2e7d32;
            transition: background 0.2s;
        }

        a.message-link:hover {
            background-color: #f1f8e9;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: #c8e6c9;
            margin-right: 15px;
            object-fit: cover;
        }

        .message-info {
            flex: 1;
        }

        .username {
            font-size: 16px;
            font-weight: bold;
        }

        .preview {
            font-size: 14px;
            color: #555;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 85%;
        }

        .meta {
            text-align: right;
            min-width: 70px;
        }

        .time {
            font-size: 12px;
            color: #888;
        }

        .unread-badge {
            background-color: #d32f2f;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            margin-top: 5px;
            display: inline-block;
        }

        .footer {
            text-align: center;
            padding: 15px 0;
            background: #f9fbe7;
        }

        .footer a {
            color: #388e3c;
            font-weight: bold;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">Your Inbox</div>
    <ul class="message-list">
        <?php while ($row = $result->fetch_assoc()): 
            $avatar = !empty($row['profile_pic']) ? $row['profile_pic'] : 'assets/default-avatar.jpg';
        ?>
            <li class="message-item">
                <a class="message-link" href="chat.php?user_id=<?= $row['sender_id'] ?>">
                    <img class="avatar" src="<?= $avatar ?>" alt="Avatar">
                    <div class="message-info">
                        <div class="username"><?= htmlspecialchars($row['username']) ?></div>
                        <div class="preview"><?= htmlspecialchars($row['last_message']) ?></div>
                    </div>
                    <div class="meta">
                        <div class="time"><?= timeAgo($row['last_message_time']) ?></div>
                        <?php if ($row['unread_count'] > 0): ?>
                            <span class="unread-badge"><?= $row['unread_count'] ?> new</span>
                        <?php endif; ?>
                    </div>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
    <div class="footer">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
