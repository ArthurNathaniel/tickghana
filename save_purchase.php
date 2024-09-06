<?php
include 'db.php'; // Make sure your database connection file is included

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
    exit;
}

$fullName = $data['fullName'];
$email = $data['email'];
$tickets = $data['tickets']; // Array of purchased tickets
$paystackRef = $data['paystackRef'];

try {
    // Start transaction
    $conn->begin_transaction();

    foreach ($tickets as $ticket) {
        $ticketId = $ticket['ticketId'];
        $reference = $ticket['ref'];

        // Insert ticket purchase details
        $stmt = $conn->prepare("INSERT INTO purchased_tickets (full_name, email, ticket_id, reference_number, purchase_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssis", $fullName, $email, $ticketId, $reference);

        if (!$stmt->execute()) {
            throw new Exception("Error saving ticket: " . $stmt->error);
        }
    }

    // Commit the transaction
    $conn->commit();
    
    echo json_encode(['status' => 'success', 'message' => 'Purchase saved successfully']);

} catch (Exception $e) {
    // Rollback transaction if any error occurs
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>
