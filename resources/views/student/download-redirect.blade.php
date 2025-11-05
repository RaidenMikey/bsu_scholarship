<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downloading...</title>
    <script>
        // Trigger download immediately
        window.location.href = '{{ $downloadUrl }}';
        
        // Redirect after a short delay (allowing download to start)
        setTimeout(function() {
            window.location.href = '{{ $redirectUrl }}';
        }, 2000);
    </script>
</head>
<body>
    <div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">
        <h2>Downloading your application form...</h2>
        <p>{{ $message }}</p>
        <p>If download doesn't start automatically, <a href="{{ $downloadUrl }}">click here</a></p>
        <p>Redirecting in a few seconds...</p>
    </div>
</body>
</html>

