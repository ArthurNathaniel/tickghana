<?php
// view_references.php
if (!isset($_GET['data'])) {
    echo "No data found.";
    exit;
}

// Decode the data from the URL parameter
$data = urldecode($_GET['data']);
$ticketsInfo = json_decode($data, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error decoding data.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment References</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <section class="payment_successful">
        <h1>Payment Successful</h1>
        <p>Thank you for your purchase! Here are your ticket details:</p>
        <table>
            <thead>
                <tr>
                    <th>Ticket Name</th>
                    <th>Reference Number</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ticketsInfo as $ticket): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ticket['ticketName']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['ref']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</body>
</html>
