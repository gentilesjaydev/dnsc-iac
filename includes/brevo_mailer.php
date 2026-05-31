<?php
/**
 * Brevo Mailer Utility
 * Uses Brevo REST API v3 to send transactional emails.
 */

// Replace this with your actual Brevo API Key from: https://app.brevo.com/settings/keys/api
define('BREVO_API_KEY', 'YOUR_BREVO_API_KEY_HERE');

function sendCovenantEmail($recipient_email, $recipient_name, $pdf_path)
{

    $url = "https://api.brevo.com/v3/smtp/email";

    // Read the PDF content and encode it to base64 for attachment
    $pdf_content = base64_encode(file_get_contents($pdf_path));
    $pdf_filename = basename($pdf_path);

    $data = [
        "sender" => [
            "name" => "DNSC IC IAC",
            "email" => "dnsciac@gmail.com" 
        ],
        "to" => [
            ["email" => $recipient_email, "name" => $recipient_name]
        ],
        "subject" => "Signed Covenant of Commitment - DNSC IC IAC",
        "htmlContent" => "
            <div style='font-family: \"Segoe UI\", Helvetica, Arial, sans-serif; background-color: #f8fafc; padding: 40px 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);'>
                    <!-- Header -->
                    <div style='background-color: #0f172a; padding: 30px; text-align: center;'>
                        <h1 style='color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px;'>DNSC IC <span style='color: #7c3aed;'>IAC</span></h1>
                        <p style='color: #94a3b8; margin: 10px 0 0 0; font-size: 14px;'>Industry Advisory Council - Institute of Computing</p>
                    </div>
                    
                    <!-- Body -->
                    <div style='padding: 40px 30px; color: #1e293b;'>
                        <h2 style='color: #7c3aed; font-size: 22px; margin-top: 0;'>Congratulations, $recipient_name!</h2>
                        <p style='font-size: 16px; line-height: 1.6;'>You have successfully signed <strong>The Covenant of Commitment</strong>. We are honored to have you join the Industry Advisory Council (IAC) dedicated to bridging the gap between academic preparation and industry excellence through SPRINT-IT.</p>
                        
                        <div style='margin: 30px 0; padding: 20px; background-color: #f1f5f9; border-left: 4px solid #7c3aed; border-radius: 4px;'>
                            <p style='margin: 0; font-size: 15px; color: #475569;'>Your signed copy of <strong>The Covenant of Commitment</strong> has been generated and is attached to this email for your records.</p>
                        </div>
                        
                        <p style='font-size: 16px; line-height: 1.6;'>Thank you for your commitment to building the future of IT education and workforce development.</p>
                        
                        <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;'>
                        
                        <p style='margin: 0; font-size: 14px; color: #64748b;'>Best Regards,</p>
                        <p style='margin: 5px 0 0 0; font-size: 16px; font-weight: 700; color: #0f172a;'>The DNSC IC IAC Team</p>
                    </div>
                    
                    <!-- Footer -->
                    <div style='background-color: #f8fafc; padding: 20px; text-align: center; border-top: 1px solid #e2e8f0;'>
                        <p style='margin: 0; font-size: 12px; color: #94a3b8;'>&copy; 2026 DNSC Institute of Computing. All rights reserved.</p>
                        <p style='margin: 5px 0 0 0; font-size: 12px; color: #94a3b8;'>DNSC GAD Conference Room and Via MS Teams</p>
                    </div>
                </div>
            </div>
        ",
        "attachment" => [
            [
                "content" => $pdf_content,
                "name" => $pdf_filename
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . BREVO_API_KEY,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        file_put_contents(__DIR__ . '/../debug_submit.log', date('Y-m-d H:i:s') . " - Email Sent Successfully to $recipient_email\n", FILE_APPEND);
        return true;
    } else {
        $error_info = "Brevo API Error (Code $httpCode): " . $response;
        error_log($error_info);
        file_put_contents(__DIR__ . '/../debug_submit.log', date('Y-m-d H:i:s') . " - EMAIL FAILURE: $error_info\n", FILE_APPEND);
        return false;
    }
}
?>

