<?php
// save_purchase.php

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

// Assume the data has been saved successfully
// Generate some dummy references
$references = array_map(function() {
    return 'ref_' . strtoupper(bin2hex(random_bytes(4)));
}, $data['tickets']);

$response = [
    'status' => 'success',
    'references' => json_encode($references)
];

echo json_encode($response);
