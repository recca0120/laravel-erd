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
<erd-editor automatic-layout system-dark-mode enable-theme-builder></erd-editor>
<script>
    const checkModuleSupport = () => 'supports' in HTMLScriptElement
        ? HTMLScriptElement.supports('module')
        : 'noModule' in document.createElement('script');

    const createScript = (src) => {
        return checkModuleSupport()
            ? import(src)
            : new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.onload = () => resolve();
                script.onerror = () => reject();
                script.src = src;
                document.body.appendChild(script);
            });
    };

    const loader = (src, fallback) => {
        return createScript(src).catch(() => createScript(fallback));
    };

    const sql = atob('{{ base64_encode(File::get($path)) }}');
    const editor = document.querySelector('erd-editor');

    if (checkModuleSupport()) {
        const scripts = [[
            '{{ asset('vendor/laravel-erd/erd-editor.esm.js') }}',
            'https://cdn.jsdelivr.net/npm/@dineug/erd-editor/+esm',
        ], [
            '{{ asset('vendor/laravel-erd/erd-editor-shiki-worker.esm.js') }}',
            'https://cdn.jsdelivr.net/npm/@dineug/erd-editor-shiki-worker/+esm',
        ]].map(([src, fallback]) => loader(src, fallback));

        Promise.all(scripts)
            .then(([{ setGetShikiServiceCallback }, { getShikiService }]) => setGetShikiServiceCallback(getShikiService))
            .then(() => editor.setSchemaSQL(sql));
    } else {
        const scripts = [[
            '{{ asset('vendor/laravel-erd/vuerd.min.js') }}',
            'https://cdn.jsdelivr.net/npm/vuerd/dist/vuerd.min.js',
        ]].map(([src, fallback]) => loader(src, fallback));

        Promise.all(scripts).then(() => editor.loadSQLDDL(sql));
    }
</script>
</body>
</html>
