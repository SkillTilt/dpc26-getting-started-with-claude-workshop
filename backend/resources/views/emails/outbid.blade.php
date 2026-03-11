<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #e53e3e;">You've been outbid!</h1>

    <p>Someone just placed a higher bid on <strong>{{ $itemTitle }}</strong>.</p>

    <p>The new highest bid is <strong>${{ $amount }}</strong>.</p>

    <p>
        <a href="{{ $itemUrl }}" style="display: inline-block; background-color: #3182ce; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px;">
            Place a new bid
        </a>
    </p>

    <p style="color: #718096; font-size: 14px;">If you no longer wish to bid on this item, you can ignore this email.</p>
</body>
</html>
