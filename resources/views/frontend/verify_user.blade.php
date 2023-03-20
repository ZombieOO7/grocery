<!DOCTYPE html>
<html>
    <head>
        <title>IPSS-Engineering</title>
    </head>
    <body>
        @if (@$response) 
            <p style="color:green;">You have successfully verified your email, your profile is under review by admin.</p>
        @else 
            <p style="color:red;">You have already verified your email.</p>
        @endif
    </body>
</html> 