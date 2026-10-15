<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $statement = $conn->prepare('SELECT id, name, password FROM users WHERE email = ?');
    $statement->bind_param('s', $email);
    $statement->execute();
    $user = $statement->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: index.php');
        exit;
    }
    $error = 'The email or password was not recognized.';
}

$pageTitle = 'Log in';
require 'includes/header.php';
?>
<section class="form-layout">
    <div class="form-intro">
        <p class="eyebrow">Welcome back</p>
        <h1>Good places are worth coming back to.</h1>
        <p>Log in to see your listings or pick up where you left off.</p>
    </div>
    <form class="form-card" method="post" action="login.php">
        <h2>Log in</h2>
        <p class="form-hint">Use the email linked to your account.</p>
        <?php if ($error !== ''): ?><p class="message message-error"><?php echo escapeHtml($error); ?></p><?php endif; ?>
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" value="<?php echo escapeHtml($email); ?>" autocomplete="email" required>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <button class="button button-dark button-wide" type="submit">Log in</button>
        <p class="form-switch">New here? <a href="register.php">Create an account</a></p>
    </form>
</section>
<?php require 'includes/footer.php'; ?>
