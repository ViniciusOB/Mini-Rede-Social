<?php
include 'config/db.php';
include 'views/header.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];

// Verifique se o post pertence ao usuário logado
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = :id AND user_id = :user_id');
$stmt->execute(['id' => $post_id, 'user_id' => $user_id]);
$post = $stmt->fetch();

if (!$post) {
    echo 'Post não encontrado ou você não tem permissão para editá-lo.';
    include 'views/footer.php';
    exit();
}
?>

<div class="container">
    <h2>Editar Post</h2>
    <form action="update_post.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
        <div class="form-group">
            <label for="content">Conteúdo</label>
            <textarea class="form-control" name="content" rows="5"><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Imagem (deixe em branco para manter a imagem atual)</label>
            <input type="file" class="form-control-file" name="image">
            <?php if ($post['image_path']) { ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="delete_image" value="1">
                    <label class="form-check-label" for="delete_image">
                        Excluir imagem atual
                    </label>
                </div>
                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" class="img-fluid rounded mb-3" alt="Post Image">
            <?php } ?>
        </div>
        <div class="form-group">
            <label for="youtube_link">Link do YouTube</label>
            <input type="text" class="form-control" name="youtube_link" value="<?php echo htmlspecialchars($post['youtube_link']); ?>">
            <?php if ($post['youtube_link']) { ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="delete_youtube_link" value="1">
                    <label class="form-check-label" for="delete_youtube_link">
                        Excluir link do YouTube atual
                    </label>
                </div>
            <?php } ?>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    </form>
</div>

<?php include 'views/footer.php'; ?>
