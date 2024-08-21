<?php
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_POST['post_id']) && isset($_POST['emoji'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $emoji = $_POST['emoji'];

    // Verifique se o usuário já reagiu a este post com o mesmo emoji
    $stmt = $pdo->prepare('SELECT * FROM reactions WHERE post_id = :post_id AND user_id = :user_id AND emoji = :emoji');
    $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id, 'emoji' => $emoji]);
    $reaction = $stmt->fetch();

    if (!$reaction) {
        // Adicione a nova reação
        $stmt = $pdo->prepare('INSERT INTO reactions (post_id, user_id, emoji) VALUES (:post_id, :user_id, :emoji)');
        $stmt->execute(['post_id' => $post_id, 'user_id' => $user_id, 'emoji' => $emoji]);
    } else {
        // Remova a reação existente (alternar reação)
        $stmt = $pdo->prepare('DELETE FROM reactions WHERE id = :id');
        $stmt->execute(['id' => $reaction['id']]);
    }

    // Retornar nova contagem de reações
    $reactionStmt = $pdo->prepare('SELECT emoji, COUNT(*) as count FROM reactions WHERE post_id = :post_id GROUP BY emoji');
    $reactionStmt->execute(['post_id' => $post_id]);
    $reactions = $reactionStmt->fetchAll();

    header('Content-Type: application/json');
    echo json_encode($reactions);
    exit();
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos']);
    exit();
}
?>
