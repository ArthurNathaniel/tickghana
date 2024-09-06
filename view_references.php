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
    <link rel="stylesheet" href="./css/view_references.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
    <style>
        .download-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <section class="payment_successful">
        <h1>Payment Successful</h1>
        <p>Thank you for your purchase! Here are your ticket details:</p>
        <table id="ticketTable">
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
        <button class="download-btn" onclick="downloadPDF()">Download PDF</button>
    </section>

    <script>
        function downloadPDF() {
            const { pdfMake, vfs } = window;

            const tickets = Array.from(document.querySelectorAll('#ticketTable tbody tr')).map(row => {
                const cells = row.querySelectorAll('td');
                return [cells[0].textContent, cells[1].textContent];
            });

            const docDefinition = {
                content: [
                    { text: 'Payment References', style: 'header' },
                    {
                        table: {
                            headerRows: 1,
                            widths: [ '*', '*' ],
                            body: [
                                ['Ticket Name', 'Reference Number'],
                                ...tickets
                            ]
                        },
                        layout: 'lightHorizontalLines'
                    }
                ],
                styles: {
                    header: {
                        fontSize: 18,
                        bold: true,
                        margin: [0, 0, 0, 10]
                    }
                }
            };

            pdfMake.createPdf(docDefinition).download('payment_references.pdf');
        }
    </script>
</body>
</html>
