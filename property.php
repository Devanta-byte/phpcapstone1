<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$propertyId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$property = null;

if ($propertyId) {
    $statement = $conn->prepare('SELECT properties.*, users.name AS seller_name, buyers.name AS buyer_name FROM properties JOIN users ON properties.seller_id = users.id LEFT JOIN users AS buyers ON properties.buyer_id = buyers.id WHERE properties.id = ?');
    $statement->bind_param('i', $propertyId);
    $statement->execute();
    $property = $statement->get_result()->fetch_assoc();
}

$pageTitle = $property ? $property['title'] : 'Property not found';
require 'includes/header.php';
?>
<?php if (!$property): ?>
    <section class="empty-state page-empty">
        <span class="empty-icon" aria-hidden="true">⌂</span>
        <h1>We couldn't find that property.</h1>
        <p>It may have been removed or the link may be incorrect.</p>
        <a class="button button-dark" href="index.php">Back to listings</a>
    </section>
<?php else: ?>
    <a class="back-link" href="index.php">← Back to all listings</a>
    <article class="property-detail">
        <div class="detail-image-wrap">
            <img class="detail-image" src="uploads/<?php echo rawurlencode($property['image']); ?>" alt="Photo of <?php echo escapeHtml($property['title']); ?>">
            <span class="image-label"><?php echo escapeHtml($property['property_type']); ?></span>
        </div>
        <div class="detail-content">
            <div class="detail-topline">
                <p class="property-location"><span aria-hidden="true">⌖</span> <?php echo escapeHtml($property['location']); ?></p>
                <span class="status-pill <?php echo $property['status'] === 'Sold' ? 'status-sold' : ''; ?>"><?php echo escapeHtml($property['status']); ?></span>
            </div>
            <h1><?php echo escapeHtml($property['title']); ?></h1>
            <p class="detail-price"><?php echo formatPrice($property['price']); ?></p>
            <?php if ($property['bedrooms'] !== null || $property['bathrooms'] !== null): ?>
                <div class="detail-facts">
                    <?php if ($property['bedrooms'] !== null): ?><span><strong><?php echo (int) $property['bedrooms']; ?></strong> bedrooms</span><?php endif; ?>
                    <?php if ($property['bathrooms'] !== null): ?><span><strong><?php echo (int) $property['bathrooms']; ?></strong> bathrooms</span><?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="detail-description">
                <h2>About this property</h2>
                <p><?php echo nl2br(escapeHtml($property['description'])); ?></p>
            </div>
            <p class="seller-note">Listed by <strong><?php echo escapeHtml($property['seller_name']); ?></strong></p>
            <?php if ($property['status'] === 'Available'): ?>
                <?php if (isset($_SESSION['user_id']) && (int) $_SESSION['user_id'] === (int) $property['seller_id']): ?>
                    <p class="message message-info">This is your listing. Buyers can try the simulated purchase flow.</p>
                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <a class="button button-dark" href="checkout.php?id=<?php echo (int) $property['id']; ?>">Continue to demo checkout <span aria-hidden="true">↗</span></a>
                <?php else: ?>
                    <a class="button button-dark" href="login.php">Log in to try a demo purchase</a>
                <?php endif; ?>
            <?php else: ?>
                <p class="message message-info">
                    <?php if (!empty($property['buyer_name'])): ?>
                        This property has been sold to <strong><?php echo escapeHtml($property['buyer_name']); ?></strong>.
                    <?php else: ?>
                        This property has already been marked as sold.
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </article>
<?php endif; ?>
<?php require 'includes/footer.php'; ?>
