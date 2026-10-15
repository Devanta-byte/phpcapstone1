<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $error = 'Enter your name, a valid email, and a password with at least 8 characters.';
    } else {
        $check = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $check->bind_param('s', $email);
        $check->execute();
        $existingUser = $check->get_result();

        if ($existingUser->num_rows > 0) {
            $error = 'An account with that email already exists.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $insert->bind_param('sss', $name, $email, $hashedPassword);

            if ($insert->execute()) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $insert->insert_id;
                $_SESSION['user_name'] = $name;
                header('Location: index.php');
                exit;
            }
            $error = 'We could not create the account. Please try again.';
        }
    }
}

$pageTitle = 'Create account';
require 'includes/header.php';
?>
<section class="form-layout">
    <div class="form-intro">
        <p class="eyebrow">Join the neighborhood</p>
        <h1>A place to find your next place.</h1>
        <p>Create one account to browse, buy, or share a property of your own.</p>
    </div>
    <form class="form-card" method="post" action="register.php">
        <h2>Create an account</h2>
        <p class="form-hint">It only takes a minute to get started.</p>
        <?php if ($error !== ''): ?><p class="message message-error"><?php echo escapeHtml($error); ?></p><?php endif; ?>
        <label for="name">Full name</label>
        <input id="name" name="name" type="text" maxlength="100" value="<?php echo escapeHtml($name); ?>" autocomplete="name" required>
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" maxlength="150" value="<?php echo escapeHtml($email); ?>" autocomplete="email" required>
        <label for="password">Password</label>
        <div class="password-field">
            <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
            <button class="password-toggle" type="button" aria-label="Show password" aria-pressed="false" title="Show password">
                <svg class="icon-eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <svg class="icon-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18"></path><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a15.8 15.8 0 0 1-3.1 3.9"></path><path d="M6.2 6.2C3.5 8 2 12 2 12s3.5 7 10 7a10 10 0 0 0 4.1-.9"></path></svg>
            </button>
        </div>
        <button class="button button-dark button-wide" type="submit">Create account</button>
        <p class="form-switch">Already a member? <a href="login.php">Log in</a></p>
    </form>
</section>
<?php require 'includes/footer.php'; ?>
