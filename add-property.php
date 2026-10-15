<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
requireLogin();

$error = '';
$title = '';
$propertyType = 'House';
$price = '';
$location = '';
$description = '';
$bedrooms = '';
$bathrooms = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $propertyType = $_POST['property_type'] ?? '';
    $price = trim($_POST['price'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $bedrooms = trim($_POST['bedrooms'] ?? '');
    $bathrooms = trim($_POST['bathrooms'] ?? '');
    $allowedTypes = array('House', 'Condo', 'Land', 'Townhouse', 'Other');

    if ($title === '' || !in_array($propertyType, $allowedTypes, true) || !is_numeric($price) || (float) $price <= 0 || $location === '' || $description === '') {
        $error = 'Please complete the required fields and enter a price greater than zero.';
    } elseif (($bedrooms !== '' && (!ctype_digit($bedrooms) || (int) $bedrooms > 50)) || ($bathrooms !== '' && (!ctype_digit($bathrooms) || (int) $bathrooms > 50))) {
        $error = 'Bedroom and bathroom counts must be whole numbers from 0 to 50.';
    } elseif (!isset($_FILES['image'])) {
        $error = 'Please choose one photo to upload.';
    } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = $_FILES['image']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['image']['error'] === UPLOAD_ERR_FORM_SIZE
            ? 'The photo is too large. Choose a photo that is 2 MB or smaller.'
            : 'Please choose one photo to upload.';
    } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        $error = 'The photo must be 2 MB or smaller.';
    } else {
        $imageInfo = getimagesize($_FILES['image']['tmp_name']);
        $imageTypes = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp');
        $mimeType = $imageInfo ? $imageInfo['mime'] : '';

        if (!isset($imageTypes[$mimeType])) {
            $error = 'Choose a JPG, PNG, or WEBP image.';
        } else {
            $imageName = uniqid('property_', true) . '.' . $imageTypes[$mimeType];
            $uploadFolder = __DIR__ . '/uploads/';

            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0755, true);
            }

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFolder . $imageName)) {
                $error = 'The photo could not be saved. Check that uploads/ is writable.';
            } else {
                $bedroomValue = $bedrooms === '' ? null : (int) $bedrooms;
                $bathroomValue = $bathrooms === '' ? null : (int) $bathrooms;
                $sellerId = (int) $_SESSION['user_id'];
                $insert = $conn->prepare('INSERT INTO properties (seller_id, title, property_type, price, location, description, bedrooms, bathrooms, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $priceValue = (float) $price;
                $insert->bind_param('issdssiis', $sellerId, $title, $propertyType, $priceValue, $location, $description, $bedroomValue, $bathroomValue, $imageName);

                if ($insert->execute()) {
                    header('Location: my-listings.php?posted=1');
                    exit;
                }

                unlink($uploadFolder . $imageName);
                $error = 'The listing could not be saved. Please try again.';
            }
        }
    }
}

$pageTitle = 'Sell a property';
require 'includes/header.php';
?>
<section class="form-page-heading">
    <p class="eyebrow">Share a place</p>
    <h1>List your property</h1>
    <p>Give people the details they need to picture themselves there.</p>
</section>
<form class="form-card wide-form" method="post" action="add-property.php" enctype="multipart/form-data">
    <h2>Property details</h2>
    <p class="form-hint">Fields marked with * are required.</p>
    <?php if ($error !== ''): ?><p class="message message-error"><?php echo escapeHtml($error); ?></p><?php endif; ?>
    <div class="form-grid">
        <div class="form-field form-field-full">
            <label for="title">Listing title *</label>
            <input id="title" name="title" type="text" maxlength="150" value="<?php echo escapeHtml($title); ?>" placeholder="A bright home near the park" required>
        </div>
        <div class="form-field">
            <label for="property_type">Property type *</label>
            <select id="property_type" name="property_type" required>
                <?php foreach (array('House', 'Condo', 'Land', 'Townhouse', 'Other') as $type): ?>
                    <option value="<?php echo escapeHtml($type); ?>" <?php echo $propertyType === $type ? 'selected' : ''; ?>><?php echo escapeHtml($type); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-field">
            <label for="price">Price (PHP) *</label>
            <input id="price" name="price" type="number" min="1" step="0.01" value="<?php echo escapeHtml($price); ?>" placeholder="3500000" required>
        </div>
        <div class="form-field form-field-full">
            <label for="location">Location *</label>
            <input id="location" name="location" type="text" maxlength="150" value="<?php echo escapeHtml($location); ?>" placeholder="Quezon City, Metro Manila" required>
        </div>
        <div class="form-field">
            <label for="bedrooms">Bedrooms <span class="optional">(optional)</span></label>
            <input id="bedrooms" name="bedrooms" type="number" min="0" max="50" step="1" value="<?php echo escapeHtml($bedrooms); ?>">
        </div>
        <div class="form-field">
            <label for="bathrooms">Bathrooms <span class="optional">(optional)</span></label>
            <input id="bathrooms" name="bathrooms" type="number" min="0" max="50" step="1" value="<?php echo escapeHtml($bathrooms); ?>">
        </div>
        <div class="form-field form-field-full">
            <label for="description">Description *</label>
            <textarea id="description" name="description" rows="5" placeholder="Share a few helpful details about this place." required><?php echo escapeHtml($description); ?></textarea>
        </div>
        <div class="form-field form-field-full">
            <label for="image">Property photo * <span class="optional">JPG, PNG, or WEBP · up to 2 MB</span></label>
            <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" required>
        </div>
    </div>
    <button class="button button-dark" type="submit">Publish listing <span aria-hidden="true">↗</span></button>
</form>
<?php require 'includes/footer.php'; ?>
