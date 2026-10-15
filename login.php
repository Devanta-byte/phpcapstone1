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
        <div class="password-field">
            <input id="password" name="password" type="password" autocomplete="current-password" required>
            <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" title="Show password">
                <svg class="icon-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <svg class="icon-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18"></path><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a15.8 15.8 0 0 1-3.1 3.9"></path><path d="M6.2 6.2C3.5 8 2 12 2 12s3.5 7 10 7a10 10 0 0 0 4.1-.9"></path></svg>
            </button>
        </div>
        <button class="button button-dark button-wide" type="submit">Log in</button>
        <p class="form-switch">New here? <a href="register.php">Create an account</a></p>
    </form>
</section>
<?php require 'includes/footer.php'; ?>
