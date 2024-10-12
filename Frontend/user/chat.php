<?php
session_start();
include '../koneksi.php'; // Ganti dengan jalur yang sesuai ke file koneksi database

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];

// Mendapatkan daftar chat pengguna
$stmt = $conn->prepare("
    SELECT DISTINCT 
        CASE 
            WHEN sender_username = ? THEN receiver_username 
            ELSE sender_username 
        END AS chat_user
    FROM messages 
    WHERE sender_username = ? OR receiver_username = ?
");
$stmt->bind_param("sss", $username, $username, $username);
$stmt->execute();
$result = $stmt->get_result();
$chat_list = [];
while ($row = $result->fetch_assoc()) {
    $chat_list[] = $row['chat_user'];
}
$stmt->close();

// Mendapatkan daftar semua pengguna untuk memulai chat baru
$stmt = $conn->prepare("SELECT username FROM users WHERE username != ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row['username'];
}
$stmt->close();

// Menangani pengiriman pesan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $receiver_username = $_POST['receiver_username'];
    $message_content = $_POST['message_content'];

    // Menangani upload gambar
    $image_path = null; // Defaultnya null
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $file_name = basename($_FILES['image']['name']);
        $image_path = $upload_dir . $file_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    // Menyimpan pesan ke dalam database
    $stmt = $conn->prepare("INSERT INTO messages (sender_username, receiver_username, message_content, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $receiver_username, $message_content, $image_path);
    $stmt->execute();
    $stmt->close();
}

// Mendapatkan pesan untuk pengguna yang sedang login dan pengguna yang dipilih
$receiver_username = isset($_GET['receiver_username']) ? $_GET['receiver_username'] : null;

$stmt = $conn->prepare("SELECT * FROM messages WHERE (sender_username = ? AND receiver_username = ?) OR (sender_username = ? AND receiver_username = ?) ORDER BY sent_at ASC");
$stmt->bind_param("ssss", $username, $receiver_username, $receiver_username, $username);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Feature</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        height: 100vh;
        background-color: #e9ecef;
    }

    .chat-list {
        border-right: 1px solid #dee2e6;
        padding: 10px;
        width: 250px;
        height: 100%;
        overflow-y: auto;
        background-color: #ffffff;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    .chat-list div {
        padding: 15px;
        cursor: pointer;
        border-radius: 8px;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        font-weight: bold; /* Menambahkan gaya bold pada teks */

    }

    .chat-list div:hover {
        background-color: #f1f3f5;
    }

    .chat-container {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: #ffffff;
        box-shadow: -2px 0 5px rgba(0,0,0,0.1);
    }

    h2 {
        margin: 0 0 20px 0;
        font-size: 20px;
        color: #495057;
    }

    .messages {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 10px;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        background-color: #f8f9fa;
        max-height: 80%;
        margin-top: 10px;
    }

    .message {
        padding: 10px 15px;
        border-radius: 20px;
        position: relative;
        max-width: 70%;
        word-wrap: break-word;
    }

    .message.sender {
        background-color: #d4edda;
        align-self: flex-end;
        border-top-left-radius: 0;
    }

    .message.receiver {
        background-color: #ffffff;
        align-self: flex-start;
        border-top-right-radius: 0;
        border: 1px solid #ced4da;
    }

    img {
        max-width: 100%;
        border-radius: 10px;
    }

    form {
        display: flex;
        margin-top: 10px;
    }

    textarea {
        flex: 1;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 20px;
        resize: none;
        margin-right: 10px;
    }

    input[type="file"] {
        display: none;
    }

    .upload-button {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 10px 20px;
        cursor: pointer;
        margin-left: 5px;
        transition: background-color 0.3s;
    }

    .upload-button:hover {
        background-color: #0056b3;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    input[type="text"] {
        width: 99%;
        padding: 10px;
        border: 1px solid #ced4da;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    #openChatButton {
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 10px 20px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    #openChatButton:hover {
        background-color: #218838;
    }

</style>
<body>

<div class="chat-list">
<h2>Chat List <button onclick="openModal()" style="background-color: transparent; border: none; cursor: pointer; font-size: 20px; margin-left: 130px; color: #007bff;">✚</button></h2>
    <?php foreach ($chat_list as $chat): ?>
        <div onclick="selectUser('<?php echo htmlspecialchars($chat); ?>')"><?php echo htmlspecialchars($chat); ?></div>
    <?php endforeach; ?>
</div>

<div class="chat-container">
    <h2>@<span id="selected_user"><?php echo htmlspecialchars($receiver_username); ?></span></h2>
    
    <div class="messages" id="messages">
        <?php foreach ($messages as $message): ?>
            <div class="message <?php echo $message['sender_username'] == $username ? 'sender' : 'receiver'; ?>">
                <strong><?php echo htmlspecialchars($message['sender_username']); ?>:</strong>
                <p><?php echo htmlspecialchars($message['message_content']); ?></p>
                <?php if ($message['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($message['image_path']); ?>" alt="Image">
                <?php endif; ?>
                <small><?php echo htmlspecialchars($message['sent_at']); ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" enctype="multipart/form-data" onsubmit="return validateMessage()">
        <input type="hidden" name="receiver_username" id="receiver_username" value="<?php echo htmlspecialchars($receiver_username); ?>" required>
        <textarea name="message_content" placeholder="Ketik pesan Anda..." required></textarea>
        <input type="file" name="image" accept="image/*" id="imageUpload">
        <label for="imageUpload" class="upload-button">📎</label>
        <button type="submit" name="send_message" class="upload-button">Kirim</button>
    </form>
</div>

<!-- Modal untuk input username -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Masukkan Nama Pengguna</h2>
        <input type="text" id="newReceiver" placeholder="Nama Pengguna" />
        <button id="openChatButton" onclick="openChat()">Buka Chat</button>
    </div>
</div>
    <script>
        function openModal() {
            document.getElementById("myModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
        }

        function openChat() {
            const username = document.getElementById('newReceiver').value;
            if (username) {
                window.location.href = 'chat.php?receiver_username=' + username;
            } else {
                alert("Silakan masukkan nama pengguna!");
            }
        }

        function selectUser(username) {
            $('#receiver_username').val(username);
            $('#selected_user').text(username);
            window.location.href = 'chat.php?receiver_username=' + username;
        }

        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
            const modal = document.getElementById("myModal");
            if (event.target == modal) {
                closeModal();
            }
        };
    </script>

    <script>
    function validateMessage() {
        const messageContent = document.querySelector('textarea[name="message_content"]');
        const phoneRegex = /(?:\+?(\d{1,3}))?[-. (]?(\d{1,4})[-. )]?(\d{1,4})[-. ]?(\d{1,9})/g; // Regex untuk mendeteksi nomor telepon

        if (phoneRegex.test(messageContent.value)) {
            messageContent.value = "Tidak Di Perbolehkan"; // Ubah isi pesan jika ada nomor telepon
        }

        return true; // Mengizinkan form untuk dikirim
    }
</script>

    <script>
        function selectUser(username) {
            $('#receiver_username').val(username);
            $('#selected_user').text(username);
            window.location.href = 'chat.php?receiver_username=' + username;
        }

        $(document).ready(function() {
    // Scroll ke bawah saat pertama kali membuka chat
    $('#messages').scrollTop($('#messages')[0].scrollHeight);
});

    // Fetch new messages secara berkala
    function fetchNewMessages() {
        let receiver_username = $('#receiver_username').val();
        let messagesContainer = $('#messages');

        // Simpan posisi scroll sebelum mengambil pesan baru
        let scrollPosition = messagesContainer.scrollTop();
        let scrollHeight = messagesContainer[0].scrollHeight;
        let clientHeight = messagesContainer[0].clientHeight;

        $.ajax({
            url: 'api_chat.php',
            method: 'GET',
            data: { receiver_username: receiver_username },
            success: function(data) {
                let messages = JSON.parse(data);

                messages.forEach(function(message) {
                    let messageDiv = $('<div class="message ' + (message.sender_username === '<?php echo htmlspecialchars($username); ?>' ? 'sender' : 'receiver') + '"></div>');
                    messageDiv.append('<strong>' + message.sender_username + ':</strong>');
                    messageDiv.append('<p>' + message.message_content + '</p>');

                    if (message.image_path) {
                        messageDiv.append('<img src="' + message.image_path + '" alt="Image">');
                    }

                    messageDiv.append('<small>' + message.sent_at + '</small>');
                    messagesContainer.append(messageDiv);
                });

                // Hanya scroll otomatis jika pengguna berada di posisi bawah
                if (scrollPosition + clientHeight >= scrollHeight - 100) {
                    messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
                }
            }
        });
    }

    // Mengambil pesan baru setiap 3 detik
    setInterval(fetchNewMessages, 3000);

    </script>

</body>
</html>
