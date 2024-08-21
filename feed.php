<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ínicio</title>
    <link rel="stylesheet" href="feed.css">
</head>
<body>
<?php
include 'config/db.php';
include 'views/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->query('SELECT posts.id, users.username, users.profile_pic, posts.content, posts.image_path, posts.youtube_link, posts.created_at, posts.user_id FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC');
while ($row = $stmt->fetch()) {
    echo "<div class='card mb-3'>";
    echo "<div class='card-body'>";
    echo "<div class='media'>";
    if ($row['profile_pic']) {
        echo "<img src='" . htmlspecialchars($row['profile_pic']) . "' class='mr-3 rounded-circle' alt='Profile Picture' style='width: 50px; height: 50px;'>";
    } else {
        echo "<img src='default-profile.png' class='mr-3 rounded-circle' alt='Default Profile Picture' style='width: 50px; height: 50px;'>";
    }
    echo "<div class='media-body'>";
    echo "<h5 class='mt-0'>" . htmlspecialchars($row['username']) . "</h5>";
    echo "<p>" . htmlspecialchars($row['content']) . "</p>";
    if ($row['image_path']) {
        echo "<img src='" . htmlspecialchars($row['image_path']) . "' class='img-fluid rounded mb-3' alt='Post Image'>";
    }
    if ($row['youtube_link']) {
        preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $row['youtube_link'], $matches);
        if (isset($matches[1])) {
            $youtube_id = $matches[1];
            echo "<div class='embed-responsive embed-responsive-16by9 mb-3'>";
            echo "<iframe class='embed-responsive-item' src='https://www.youtube.com/embed/" . htmlspecialchars($youtube_id) . "' allowfullscreen></iframe>";
            echo "</div>";
        }
    }
    echo "<p class='text-muted'><small>" . htmlspecialchars($row['created_at']) . "</small></p>";

    // Contar reações
    $reactionStmt = $pdo->prepare('SELECT emoji, COUNT(*) as count FROM reactions WHERE post_id = :post_id GROUP BY emoji');
    $reactionStmt->execute(['post_id' => $row['id']]);
    $reactions = $reactionStmt->fetchAll();

    // Exibir reações
    if ($reactions) {
        echo "<div class='reactions-container mb-2'>";
        foreach ($reactions as $reaction) {
            echo "<span class='mr-2'>" . htmlspecialchars($reaction['emoji']) . " " . $reaction['count'] . "</span>";
        }
        echo "</div>";
    } else {
        echo "<div class='reactions-container mb-2'></div>";
    }

    // Botões de emoji
    echo "<form class='reaction-form d-inline'>";
    echo "<input type='hidden' name='post_id' value='" . $row['id'] . "'>";
    echo "<button type='button' name='emoji' value='💩' class='reaction-btn btn btn-light btn-sm'>💩</button>";
    echo "<button type='button' name='emoji' value='❤️' class='reaction-btn btn btn-light btn-sm'>❤️</button>";
    echo "<button type='button' name='emoji' value='👍' class='reaction-btn btn btn-light btn-sm'>👍</button>";
    echo "<button type='button' name='emoji' value='😮' class='reaction-btn btn btn-light btn-sm'>😮</button>";
    echo "<button type='button' name='emoji' value='😢' class='reaction-btn btn btn-light btn-sm'>😢</button>";
    echo "</form>";

    if ($row['user_id'] == $_SESSION['user_id']) {
        echo " <form action='edit_post.php' method='POST' style='display:inline;'>";
        echo "<input type='hidden' name='post_id' value='" . $row['id'] . "'>";
        echo "<button type='submit' class='btn btn-primary btn-sm'>Editar</button>";
        echo "</form> ";
        echo "<form action='delete_post.php' method='POST' style='display:inline;'>";
        echo "<input type='hidden' name='post_id' value='" . $row['id'] . "'>";
        echo "<button type='submit' class='btn btn-danger btn-sm'>Excluir</button>";
        echo "</form>";
    }
    echo "</div>"; // Fecha media-body
    echo "</div>"; // Fecha media
    echo "</div>"; // Fecha card-body
    echo "</div>"; // Fecha card
}
include 'views/footer.php';
?>

</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    document.querySelectorAll('.reaction-btn').forEach(button => {
        button.addEventListener('click', function() {
            const post_id = this.closest('form').querySelector('input[name="post_id"]').value;
            const emoji = this.value;
            const formData = new FormData();
            formData.append('post_id', post_id);
            formData.append('emoji', emoji);

            fetch('add_reaction.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Atualizar contagem de reações
                const reactionContainer = this.closest('.card').querySelector('.reactions-container');
                reactionContainer.innerHTML = '';
                data.forEach(reaction => {
                    reactionContainer.innerHTML += `<span class='mr-2'>${reaction.emoji} ${reaction.count}</span>`;
                });
            })
            .catch(error => console.error('Erro:', error));
        });
    });
</script>

<style>
/* Reset de estilo básico */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Estilos Globais */
body {
    font-family: 'Poppins', sans-serif;
    background-color: #e0f7fa; /* Azul claro */
    color: #004d40; /* Verde escuro */
    line-height: 1.6;
    font-size: 16px;
    transition: all 0.3s ease;
}

/* Links */
a {
    color: #00796b; /* Verde escuro */
    text-decoration: none;
    transition: color 0.3s ease;
}

a:hover {
    color: #004d40; /* Verde mais escuro */
}

/* Navbar */
.navbar {
    background-color: #004d40; /* Verde escuro */
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 0 0 10px 10px; /* Bordas arredondadas inferiores */
}

.navbar .navbar-brand {
    color: #ffffff; /* Branco */
    font-size: 1.75rem;
    font-weight: bold;
}

.navbar .navbar-nav {
    list-style: none;
    display: flex;
    gap: 1.5rem;
}

.navbar .nav-link {
    color: #ffffff; /* Branco */
    font-size: 1.1rem;
    font-weight: 500;
    transition: color 0.3s ease;
}

.navbar .nav-link:hover {
    color: #b2dfdb; /* Verde claro */
}

/* Container */
.container {
    max-width: 1100px;
    margin: 2rem auto;
    padding: 0 2rem;
}

/* Cartões */
.card {
    background-color: #ffffff; /* Branco */
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    overflow: hidden;
}

.card-body {
    padding: 1.5rem;
}

.card-body img,
.card-body iframe {
    border-radius: 10px;
    max-width: 100%;
    display: block;
    margin-top: 1rem;
}

/* Títulos e textos */
.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #004d40; /* Verde escuro */
}

.card-text {
    font-size: 16px;
    color: #666666; /* Cinza escuro */
}

/* Reações */
.reactions {
    display: flex;
    justify-content: flex-end;
    margin-top: 10px;
}

.reactions a {
    font-size: 14px;
    color: #004d40; /* Verde escuro */
    text-decoration: none;
    margin-left: 8px;
    height: 32px;
    line-height: 32px;
    display: flex;
    align-items: center;
    border: 1px solid #b2dfdb; /* Verde claro */
    border-radius: 5px;
    padding: 0 8px;
    transition: all 0.3s ease;
}

.reactions a:hover {
    color: #004d40; /* Verde mais escuro */
    border-color: #004d40; /* Verde mais escuro */
}

/* Ações de Postagem */
.post-actions {
    margin-top: 15px;
}

.post-actions .btn {
    margin-right: 10px;
    height: 32px;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    color: #ffffff; /* Branco */
}

.btn-primary {
    background-color: #00796b; /* Verde escuro */
}

.btn-primary:hover {
    background-color: #004d40; /* Verde mais escuro */
    transform: translateY(-2px);
}

.btn-danger {
    background-color: #d32f2f; /* Vermelho escuro */
}

.btn-danger:hover {
    background-color: #b71c1c; /* Vermelho mais escuro */
    transform: translateY(-2px);
}
</style>
</html>
