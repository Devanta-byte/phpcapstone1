<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = 'Find a place to call home';
$properties = $conn->query("SELECT properties.*, users.name AS seller_name FROM properties JOIN users ON properties.seller_id = users.id WHERE properties.status = 'Available' ORDER BY properties.created_at DESC");
require 'includes/header.php';
?>
<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">A simpler way to find your next place</p>
        <h1>Find a place<br>to call <em>home.</em></h1>
        <p>Explore homes and properties shared by people in your community.</p>
        <a class="button button-light" href="#listings">Explore properties <span aria-hidden="true">↓</span></a>
    </div>
    <div class="hero-note">
        <span class="note-number">01</span>
        <p>Browse a place.<br>Meet its next owner.</p>
    </div>
</section>

<section class="listings-section" id="listings">
    <div class="section-heading">
        <div>
            <p class="eyebrow">The latest listings</p>
            <h2>Places worth a closer look</h2>
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a class="text-link" href="add-property.php">List your property <span aria-hidden="true">↗</span></a>
        <?php endif; ?>
    </div>

    <?php if ($properties && $properties->num_rows > 0): ?>
        <div class="property-grid">
            <?php while ($property = $properties->fetch_assoc()): ?>
                <article class="property-card">
                    <a class="property-image-link" href="property.php?id=<?php echo (int) $property['id']; ?>">
                        <img class="property-image" src="uploads/<?php echo rawurlencode($property['image']); ?>" alt="Photo of <?php echo escapeHtml($property['title']); ?>">
                        <span class="image-label"><?php echo escapeHtml($property['property_type']); ?></span>
                    </a>
                    <div class="property-card-body">
                        <p class="property-location"><span aria-hidden="true">⌖</span> <?php echo escapeHtml($property['location']); ?></p>
                        <h3><a href="property.php?id=<?php echo (int) $property['id']; ?>"><?php echo escapeHtml($property['title']); ?></a></h3>
                        <div class="property-card-bottom">
                            <p class="price"><?php echo formatPrice($property['price']); ?></p>
                            <span>by <?php echo escapeHtml($property['seller_name']); ?></span>
                        </div>
                        <?php if ($property['bedrooms'] !== null || $property['bathrooms'] !== null): ?>
                            <p class="property-facts">
                                <?php if ($property['bedrooms'] !== null): ?><?php echo (int) $property['bedrooms']; ?> bed<?php echo (int) $property['bedrooms'] === 1 ? '' : 's'; ?><?php endif; ?>
                                <?php if ($property['bedrooms'] !== null && $property['bathrooms'] !== null): ?> <span>·</span> <?php endif; ?>
                                <?php if ($property['bathrooms'] !== null): ?><?php echo (int) $property['bathrooms']; ?> bath<?php echo (int) $property['bathrooms'] === 1 ? '' : 's'; ?><?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <span class="empty-icon" aria-hidden="true">⌂</span>
            <h3>The first listing is waiting to be shared.</h3>
            <p>Sign in and add a property to start building your neighborhood.</p>
            <a class="button button-dark" href="<?php echo isset($_SESSION['user_id']) ? 'add-property.php' : 'register.php'; ?>">Add a property</a>
        </div>
    <?php endif; ?>
</section>
<?php require 'includes/footer.php'; ?>
