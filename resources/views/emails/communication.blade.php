<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Communication Email</title>
        <style>
            .content {
                margin-top: 10px !important;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <p>Dear {{ $profileName }},</p>
            <div class="content">
                {!! $body !!}
            </div>
        </div>
    </body>
</html>
