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
        <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
        <button class="button button-dark button-wide" type="submit">Create account</button>
        <p class="form-switch">Already a member? <a href="login.php">Log in</a></p>
    </form>
</section>
<?php require 'includes/footer.php'; ?>
