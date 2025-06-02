<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    $user_id = $_SESSION['user_id'];

    // Get the image file name to delete it from the server
    $stmt = $conn->prepare("SELECT image FROM items WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $item_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    // Delete from DB
    $delete_stmt = $conn->prepare("DELETE FROM items WHERE id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $item_id, $user_id);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Remove image file
    if ($image && file_exists("uploads/" . $image)) {
        unlink("uploads/" . $image);
    }

    header("Location: dashboard.php");
    exit;
}
?>
