<?php
include 'config/db.php'; // Inicia a sessão

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $profile_pic = NULL;

    // Processar upload da foto de perfil
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'profile_pics/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $profile_pic = $upload_dir . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO users (username, password, profile_pic) VALUES (:username, :password, :profile_pic)');
        $stmt->execute(['username' => $username, 'password' => $password, 'profile_pic' => $profile_pic]);

        header('Location: login.php');
        exit();
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            $error = 'Nome de usuário já existe. Por favor, escolha outro.';
        } else {
            $error = 'Erro ao registrar usuário: ' . $e->getMessage();
        }
    }
}

include 'views/header.php';
?>

<div class="container mt-4">
    <h2>Registrar</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="register.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Nome de usuário:</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="profile_pic">Foto de perfil:</label>
            <input type="file" id="profile_pic" name="profile_pic" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Registrar</button>
    </form>
</div>

<?php include 'views/footer.php'; ?>
