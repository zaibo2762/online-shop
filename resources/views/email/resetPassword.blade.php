<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password Email</title>
</head>
<body style="font-family : Arial, Helvatica,Sa-serif; font-size:16px; ">

    <p>Hello {{ $formData['user']->name }}</p>

    <h1>You have Requested to change password</h1>
    <p>Please Click The Link Given Below to change password</p>
    <a href="{{ route('front.resetPassword',$formData['token']) }}">Click Here</a>

    <p>Thanks</p>
</body>
</html>