<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0">
    <title>Test file upload API</title>
</head>
<body>
<form action="{{url('file/upload')}}" enctype="multipart/form-data" method="post">
    <p><strong>Test file upload:</strong></p>
    <input type="file" value="Browse" name="file">
    Max downloads:
    <input type="text" value="0" name="max_downloads">
    <input type="submit">
</form>upload
</body>
</html>
