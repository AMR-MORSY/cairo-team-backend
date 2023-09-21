<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email</title>
</head>
<body>
    <h1>You have requested Password Reset</h1>
    <h3>Please click the link to reset your password </h3>
    <a href="https://cairo-team.com/user/{{$token}}" style="text-decoration: solid"><strong>{{$token}}</strong></a>
    <p>Thank you.</p>
</body>
</html>