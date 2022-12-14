<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel Erd</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
        }
    </style>
</head>
<body>
<erd-editor automatic-layout></erd-editor>
<script>
    function loadScript(src, onError) {
        var script = document.createElement('script');
        script.onload = function () {
            var editor = document.querySelector('erd-editor');
            editor.loadSQLDDL(atob('{{ $contents }}'));
        }

        if (onError === false) {
            script.onerror = function () {
                loadScript("https://cdn.jsdelivr.net/npm/vuerd/dist/vuerd.min.js", true);
            }
        }

        script.src = src
        document.body.appendChild(script);
    }

    loadScript("{{ asset('vendor/laravel-erd/vuerd.min.js') }}", false);
</script>
</body>
</html>