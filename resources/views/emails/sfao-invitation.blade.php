<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SFAO Admin Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #8B0000;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background-color: #8B0000;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #660000;
        }
        .details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 BSU Scholarship System</h1>
        <h2>SFAO Admin Invitation</h2>
    </div>
    
    <div class="content">
        <h3>Hello {{ $invitation->name }},</h3>
        
        <p>You have been invited to join the BSU Scholarship System as an SFAO (Student Financial Assistance Office) Administrator.</p>
        
        <div class="details">
            <h4>Invitation Details:</h4>
            <ul>
                <li><strong>Name:</strong> {{ $invitation->name }}</li>
                <li><strong>Email:</strong> {{ $invitation->email }}</li>
                <li><strong>Campus:</strong> {{ $invitation->campus->name }}</li>
                <li><strong>Invited by:</strong> {{ $invitation->inviter->name }}</li>
                <li><strong>Expires:</strong> {{ $invitation->expires_at->format('M d, Y h:i A') }}</li>
            </ul>
        </div>
        
        <p>To accept this invitation and set up your account, please click the button below:</p>
        
        <div style="text-align: center;">
            <a href="{{ route('invitation.show', $invitation->token) }}" class="button">
                Accept Invitation & Set Up Account
            </a>
        </div>
        
        <p><strong>Important:</strong></p>
        <ul>
            <li>This invitation will expire in 7 days</li>
            <li>You will need to set up a secure password</li>
            <li>Once accepted, you'll have access to manage scholarship applications for {{ $invitation->campus->name }}</li>
        </ul>
        
        <p>If you did not expect this invitation or have any questions, please contact the system administrator.</p>
        
        <p>Best regards,<br>
        BSU Scholarship System Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from the BSU Scholarship System.</p>
        <p>If you cannot click the button above, copy and paste this link into your browser:</p>
        <p>{{ route('invitation.show', $invitation->token) }}</p>
    </div>
</body>
</html>
