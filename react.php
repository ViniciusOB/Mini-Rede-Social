<?php
include 'config/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['post_id']) || !isset($_GET['type'])) {
    header('Location: feed.php');
    exit();
}

$post_id = $_GET['post_id'];
$reaction_type = $_GET['type'];
$user_id = $_SESSION['user_id'];

if (!in_array($reaction_type, ['like', 'love', 'haha', 'wow', 'sad', 'angry'])) {
    header('Location: feed.php');
    exit();
}

// Verificar se o usuário já reagiu ao post
$stmt = $pdo->prepare('SELECT id FROM reactions WHERE post_id = :post_id AND user_id = :user_id');
$stmt->execute(['post_id' => $post_id, 'user_id' => $user_id]);
$existing_reaction = $stmt->fetch();

if ($existing_reaction) {
    // Se já reagiu, removemos a reação
    $stmt = $pdo->prepare('DELETE FROM reactions WHERE post_id = :post_id AND user_id = :user_id');
    $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id]);
} else {
    // Adicionar nova reação
    $stmt = $pdo->prepare('INSERT INTO reactions (post_id, user_id, reaction_type) VALUES (:post_id, :user_id, :reaction_type)');
    $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id, 'reaction_type' => $reaction_type]);
}

header('Location: feed.php');
exit();
?>
