<?php
session_start();
require_once 'includes/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user = $_SESSION['user_id'];

// Ensure ?user_id is passed
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    echo "No recipient selected.";
    exit;
}

$chat_user = intval($_GET['user_id']);

// Prevent chatting with yourself
if ($chat_user === $current_user) {
    echo "You can't chat with yourself.";
    exit;
}

// Check if chat user exists
$userStmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$userStmt->bind_param("i", $chat_user);
$userStmt->execute();
$userStmt->store_result();

if ($userStmt->num_rows === 0) {
    echo "User not found.";
    exit;
}

$userStmt->bind_result($chat_username);
$userStmt->fetch();
$userStmt->close();

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $current_user, $chat_user, $message);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch message history
$msgStmt = $conn->prepare("
    SELECT m.*, u.username AS sender_name 
    FROM messages m 
    JOIN users u ON m.sender_id = u.id 
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY m.sent_at ASC
");
$msgStmt->bind_param("iiii", $current_user, $chat_user, $chat_user, $current_user);
$msgStmt->execute();
$messages = $msgStmt->get_result();

// Mark received messages as read
$updateRead = $conn->prepare("UPDATE messages SET is_read = 1 WHERE receiver_id = ? AND sender_id = ?");
$updateRead->bind_param("ii", $current_user, $chat_user);
$updateRead->execute();
$updateRead->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat | Eco Trade</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e8f5e9;
            margin: 0;
            padding: 0;
        }

        .chat-container {
            width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .chat-header {
            padding: 15px;
            background-color: #388e3c;
            color: #fff;
            font-weight: bold;
        }

        .chat-messages {
            height: 400px;
            overflow-y: scroll;
            padding: 15px;
            background-color: #f9fbe7;
        }

        .chat-message {
            margin-bottom: 10px;
        }

        .chat-message.sent {
            text-align: right;
        }

        .chat-message p {
            display: inline-block;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }

        .chat-message.sent p {
            background-color: #c8e6c9;
        }

        .chat-message.received p {
            background-color: #eeeeee;
        }

        .chat-input {
            display: flex;
            border-top: 1px solid #ccc;
        }

        .chat-input textarea {
            flex: 1;
            padding: 10px;
            border: none;
            resize: none;
        }

        .chat-input button {
            background-color: #2e7d32;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        .back-link {
            display: inline-block;
            margin: 15px;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span>Chat with <?= htmlspecialchars($chat_username) ?></span>
            <div>
                <a href="message.php" style="color: #fff; margin-right: 15px; text-decoration: underline;">Inbox</a>
                <a href="dashboard.php" style="color: #fff; text-decoration: underline;">Dashboard</a>
            </div>
        </div>
    </div>


    <div class="chat-messages">
        <?php while ($msg = $messages->fetch_assoc()): ?>
            <div class="chat-message <?= $msg['sender_id'] == $current_user ? 'sent' : 'received' ?>">
                <p><strong><?= htmlspecialchars($msg['sender_name']) ?>:</strong> <?= htmlspecialchars($msg['message']) ?></p>
            </div>
        <?php endwhile; ?>
    </div>

    <form method="POST" class="chat-input">
        <textarea name="message" placeholder="Type your message..." required></textarea>
        <button type="submit">Send</button>
    </form>
</div>

</body>
</html>
