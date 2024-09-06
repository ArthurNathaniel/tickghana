<?php
// view_references.php
if (!isset($_GET['references'])) {
    echo "No references found.";
    exit;
}

// Decode the references from the URL parameter
$references = urldecode($_GET['references']);
$referencesArray = json_decode($references, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error decoding references.";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment References</title>
    <link rel="stylesheet" href="./css/base.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <section>
        <h1>Payment Successful</h1>
        <p>Thank you for your purchase! Here are your ticket references:</p>
        <ul>
            <?php foreach ($referencesArray as $reference): ?>
                <li><?php echo htmlspecialchars($reference); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
</body>
</html>
