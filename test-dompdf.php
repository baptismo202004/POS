<?php
// Test if DomPDF works at all
require_once 'vendor/autoload.php';

try {
    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', false);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', false);
    
    $pdf = new \Dompdf\Dompdf($options);
    $pdf->setPaper('A4', 'landscape');
    
    // Simple test HTML
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Test PDF</title>
        <style>
            body { font-family: Arial; font-size: 12px; margin: 20px; }
            h1 { color: #333; }
        </style>
    </head>
    <body>
        <h1>PDF Test Document</h1>
        <p>This is a test to see if DomPDF works.</p>
        <p>Generated at: ' . date('Y-m-d H:i:s') . '</p>
    </body>
    </html>';
    
    $pdf->loadHtml($html);
    $pdfOutput = $pdf->output();
    
    if (strlen($pdfOutput) > 0) {
        echo "PDF generated successfully! Size: " . strlen($pdfOutput) . " bytes";
        
        // Save to file
        file_put_contents('test-output.pdf', $pdfOutput);
        echo "PDF saved as test-output.pdf";
    } else {
        echo "PDF generation failed - empty output";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
