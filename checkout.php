<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
requireLogin();

$propertyId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $propertyId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

$property = null;
if ($propertyId) {
    $statement = $conn->prepare('SELECT id, seller_id, title, price, location, image, status FROM properties WHERE id = ?');
    $statement->bind_param('i', $propertyId);
    $statement->execute();
    $property = $statement->get_result()->fetch_assoc();
}

$error = '';
$complete = false;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $property) {
    if ((int) $property['seller_id'] === (int) $_SESSION['user_id']) {
        $error = 'You cannot buy your own property.';
    } elseif ($property['status'] !== 'Available') {
        $error = 'This property is no longer available.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$property) {
        $error = 'This property could not be found.';
    } elseif ((int) $property['seller_id'] === (int) $_SESSION['user_id']) {
        $error = 'You cannot buy your own property.';
    } elseif ($property['status'] !== 'Available') {
        $error = 'This property is no longer available.';
    } else {
        $buyerId = (int) $_SESSION['user_id'];
        $update = $conn->prepare("UPDATE properties SET status = 'Sold', buyer_id = ?, sold_at = CURRENT_TIMESTAMP WHERE id = ? AND status = 'Available' AND seller_id <> ?");
        $update->bind_param('iii', $buyerId, $propertyId, $buyerId);
        $update->execute();

        if ($update->affected_rows === 1) {
            $complete = true;
        } else {
            $error = 'This property is no longer available. Please return to the listings.';
        }
    }
}

$pageTitle = $complete ? 'Purchase complete' : 'Demo checkout';
require 'includes/header.php';
?>
<section class="checkout-layout">
    <?php if ($complete): ?>
        <div class="checkout-success">
            <span class="success-mark" aria-hidden="true">✓</span>
            <p class="eyebrow">Demo purchase complete</p>
            <h1>It's marked as sold.</h1>
            <p>This is only a classroom demonstration. No payment was taken.</p>
            <div class="checkout-summary compact-summary">
                <img src="uploads/<?php echo rawurlencode($property['image']); ?>" alt="">
                <div>
                    <p class="property-location"><?php echo escapeHtml($property['location']); ?></p>
                    <h2><?php echo escapeHtml($property['title']); ?></h2>
                    <p class="price"><?php echo formatPrice($property['price']); ?></p>
                </div>
            </div>
            <a class="button button-dark" href="index.php">Back to available properties</a>
        </div>
    <?php elseif (!$property): ?>
        <div class="empty-state page-empty">
            <h1>Property not found</h1>
            <p>Return to the listings and choose an available property.</p>
            <a class="button button-dark" href="index.php">Browse properties</a>
        </div>
    <?php else: ?>
        <div class="checkout-heading">
            <a class="back-link" href="property.php?id=<?php echo (int) $property['id']; ?>">← Back to property</a>
            <p class="eyebrow">A classroom demo</p>
            <h1>Review your purchase</h1>
            <p>No payment information is collected. Confirming will mark this property as sold.</p>
        </div>
        <div class="checkout-card">
            <div class="checkout-summary">
                <img src="uploads/<?php echo rawurlencode($property['image']); ?>" alt="">
                <div>
                    <p class="property-location"><?php echo escapeHtml($property['location']); ?></p>
                    <h2><?php echo escapeHtml($property['title']); ?></h2>
                    <p class="price"><?php echo formatPrice($property['price']); ?></p>
                </div>
            </div>
            <?php if ($error !== ''): ?><p class="message message-error"><?php echo escapeHtml($error); ?></p><?php endif; ?>
            <?php if ($property['status'] !== 'Available' || (int) $property['seller_id'] === (int) $_SESSION['user_id']): ?>
                <a class="button button-dark button-wide" href="property.php?id=<?php echo (int) $property['id']; ?>">Return to property</a>
            <?php else: ?>
                <div class="demo-notice"><strong>Simulated purchase</strong><span>No money changes hands in this capstone.</span></div>
                <form method="post" action="checkout.php">
                    <input type="hidden" name="id" value="<?php echo (int) $property['id']; ?>">
                    <button class="button button-dark button-wide" type="submit">Confirm demo purchase</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>
