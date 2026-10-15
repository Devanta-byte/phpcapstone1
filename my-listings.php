<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
requireLogin();

$userId = (int) $_SESSION['user_id'];
$statement = $conn->prepare('SELECT * FROM properties WHERE seller_id = ? ORDER BY created_at DESC');
$statement->bind_param('i', $userId);
$statement->execute();
$properties = $statement->get_result();

$pageTitle = 'My listings';
require 'includes/header.php';
?>
<section class="listings-section my-listings-section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Your corner of Home Haven</p>
            <h1>My listings</h1>
            <p class="section-description">See the properties you've shared and their latest status.</p>
        </div>
        <a class="button button-dark" href="add-property.php">Add a property <span aria-hidden="true">+</span></a>
    </div>
    <?php if (isset($_GET['posted'])): ?><p class="message message-success">Your property has been listed.</p><?php endif; ?>
    <?php if ($properties->num_rows > 0): ?>
        <div class="property-grid">
            <?php while ($property = $properties->fetch_assoc()): ?>
                <article class="property-card">
                    <a class="property-image-link" href="property.php?id=<?php echo (int) $property['id']; ?>">
                        <img class="property-image" src="uploads/<?php echo rawurlencode($property['image']); ?>" alt="Photo of <?php echo escapeHtml($property['title']); ?>">
                        <span class="image-label"><?php echo escapeHtml($property['status']); ?></span>
                    </a>
                    <div class="property-card-body">
                        <p class="property-location"><?php echo escapeHtml($property['location']); ?></p>
                        <h2><a href="property.php?id=<?php echo (int) $property['id']; ?>"><?php echo escapeHtml($property['title']); ?></a></h2>
                        <p class="price"><?php echo formatPrice($property['price']); ?></p>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <span class="empty-icon" aria-hidden="true">⌂</span>
            <h2>You haven't shared a property yet.</h2>
            <p>Add your first listing and it will appear here.</p>
            <a class="button button-dark" href="add-property.php">List a property</a>
        </div>
    <?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>
