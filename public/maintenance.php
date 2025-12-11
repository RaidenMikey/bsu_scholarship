<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Maintenance | Batangas State University</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            text-align: center;
            padding: 20px;
        }
        .container {
            background-color: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            border-top: 6px solid #d90000; /* BSU Red Approximate */
        }
        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 1.5rem;
        }
        h1 {
            color: #111827;
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 1rem;
            margin-top: 0;
        }
        p {
            color: #6b7280;
            font-size: 1.125rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #d90000;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .footer {
            margin-top: 2rem;
            font-size: 0.875rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="images/Batangas_State_Logo.png" alt="BSU Logo" class="logo" onerror="this.style.display='none'">
        
        <h1>Under Maintenance</h1>
        
        <p>
            We are currently updating our system to serve you better. 
            The website is temporarily unavailable while we perform these upgrades.
            <br><br>
            Please check back shortly.
        </p>

        <div class="spinner"></div>
    </div>

    <div class="footer">
        &copy; <?php echo date('Y'); ?> Batangas State University. The National Engineering University.
    </div>
</body>
</html>
