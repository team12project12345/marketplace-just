<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$other_user = (int)$_GET['user'];
$item_id = (int)$_GET['item'];


$conv = mysqli_query($conn,
    "SELECT * FROM conversations 
     WHERE ((user1=$user_id AND user2=$other_user) 
        OR (user1=$other_user AND user2=$user_id))
     AND item_id=$item_id"
);

if (mysqli_num_rows($conv) == 0) {
    mysqli_query($conn,
        "INSERT INTO conversations (user1, user2, item_id)
         VALUES ($user_id, $other_user, $item_id)"
    );
    $conversation_id = mysqli_insert_id($conn);
} else {
    $conversation = mysqli_fetch_assoc($conv);
    $conversation_id = $conversation['id'];
}


if (isset($_POST['send'])) {
    $msg = trim($_POST['message']);
    if ($msg !== '') {
        $msg = mysqli_real_escape_string($conn, $msg);

        mysqli_query($conn,
            "INSERT INTO messages (conversation_id, sender_id, message)
             VALUES ($conversation_id, $user_id, '$msg')"
        );
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Chat</title>
<link rel="stylesheet" href="css/style.css?v=3">

<style>

.chat-box {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 12px;
    height: 350px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}


.message {
    padding: 10px 15px;
    margin: 6px 0;
    max-width: 60%;
    border-radius: 20px;
    word-wrap: break-word;
    font-size: 14px;
}


.me {
    background: #4f46e5;
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 5px;
}


.other {
    background: #e4e6eb;
    color: #333;
    margin-right: auto;
    border-bottom-left-radius: 5px;
}


.chat-form {
    display: flex;
    margin-top: 10px;
    gap: 10px;
}

.chat-form input {
    flex: 1;
    padding: 14px;
    font-size: 18px; 
    border-radius: 25px;
}


.chat-form button {
    padding: 12px 20px;
    font-size: 16px; 
    border-radius: 25px;
}


.chat-form button:hover {
    background: #4338ca;
}

</style>

</head>

<body>

<div class="navbar">
    <a href="dashboard.php">Dashboard</a>
    <a href="all_items.php">All Items</a>
    <a href="messages_list.php">Chats</a>
    <a href="logout.php">Logout</a>
</div>

<a href="<?= $_SERVER['HTTP_REFERER'] ?? 'all_items.php' ?>"
style="margin:15px;display:inline-block;color:#4f46e5;">
⬅ Back
</a>

<div class="container">

<h2>Chat</h2>

<div class="chat-box">

<?php
$messages = mysqli_query($conn,
    "SELECT * FROM messages 
     WHERE conversation_id=$conversation_id 
     ORDER BY created_at ASC"
);

while ($m = mysqli_fetch_assoc($messages)):
?>

<div class="message <?= $m['sender_id']==$user_id ? 'me' : 'other' ?>">
    <?= htmlspecialchars($m['message']) ?>
</div>

<?php endwhile; ?>

</div>

<form method="POST" class="chat-form">
    <input name="message" placeholder="Type message..." required>
    <button name="send">Send</button>
</form>

</div>

</body>
</html>
