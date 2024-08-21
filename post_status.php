<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];
    $image_path = NULL;
    $youtube_link = NULL;

    // Processar upload de imagem
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $image_path = $upload_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    // Processar link do YouTube
    if (isset($_POST['youtube_link']) && !empty($_POST['youtube_link'])) {
        $youtube_link = $_POST['youtube_link'];
    }

    $stmt = $pdo->prepare('INSERT INTO posts (user_id, content, image_path, youtube_link) VALUES (:user_id, :content, :image_path, :youtube_link)');
    $stmt->execute(['user_id' => $user_id, 'content' => $status, 'image_path' => $image_path, 'youtube_link' => $youtube_link]);

    header('Location: feed.php');
    exit();
}
?>
