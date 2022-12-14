<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
        body {
            margin: 0;
            height: 100vh;
        }
    </style>
</head>
<body>
<erd-editor automatic-layout></erd-editor>
<script src="https://cdn.jsdelivr.net/npm/vuerd/dist/vuerd.min.js"></script>
<script>
    const editor = document.querySelector('erd-editor');
    editor.loadSQLDDL(atob('{{ $contents }}'));
</script>
</body>
</html>