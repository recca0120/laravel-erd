<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel Erd</title>
</head>
<body>
@php
    $svg = new \DOMDocument();
    $svg->load($path);
    $svg->documentElement->setAttribute("id", 'svg');
    echo $svg->saveXML($svg->documentElement);
@endphp
<script>
    function loadScript(src, remote, onError) {
        var script = document.createElement('script');
        script.onload = function () {
            init();
        }

        if (!onError) {
            script.onerror = function () {
                loadScript(remote, true);
            }
        }

        script.src = src
        document.body.appendChild(script);
    }

    function init() {
        var elem = document.getElementById('svg');
        var panzoom = Panzoom(elem, {canvas: true});
        var parent = elem.parentElement;

        // No function bind needed
        parent.addEventListener('wheel', panzoom.zoomWithWheel)
    }

    loadScript("{{ asset('vendor/laravel-erd/panzoom.min.js') }}", "https://cdn.jsdelivr.net/npm/@panzoom/panzoom@4.5.1/dist/panzoom.min.js");
</script>
</body>
</html>