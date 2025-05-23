#проверка прав
<?php
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$current_user = $_SESSION['user'];
?>
<?php
// includes/check_role.php
session_start();

function is_admin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function is_moderator() {
    return isset($_SESSION['user']) && ($_SESSION['user']['role'] === 'moderator' || is_admin());
}

function check_permission($required_role) {
    switch ($required_role) {
        case 'admin':
            if (!is_admin()) die("Доступ запрещен");
            break;
        case 'moderator':
            if (!is_moderator()) die("Доступ запрещен");
            break;
    }
}

function can_edit_services() {
    return is_admin() || is_moderator();
}

function can_manage_users() {
    return is_admin();
}

function can_view_requests() {
    return is_admin() || is_moderator();
}

function can_change_request_status() {
    return is_admin() || is_moderator();
}

function can_view_portfolio() {
    return true; // Доступно всем
}

function can_edit_portfolio() {
    return is_admin() || is_moderator();
}

function check_role($required_role) {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $required_role) {
        header('HTTP/1.0 403 Forbidden');
        die('Доступ запрещен');
    }
}


function checkRole($requiredRole) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
    
    global $pdo;
    
    // Проверяем, не забанен ли пользователь
    $stmt = $pdo->prepare("SELECT is_banned FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if ($user['is_banned'] == 1) {
        session_destroy();
        header('Location: /login.php?banned=1');
        exit;
    }
    
    // Проверяем роль
    if ($_SESSION['user_role'] !== $requiredRole) {
        header('HTTP/1.0 403 Forbidden');
        die('Доступ запрещен. Недостаточно прав.');
    }
}
?>

