<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Login') &middot; Online Incidents Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --ink: #17233b;
            --ink-soft: #4b5c78;
            --paper: #f6f4ef;
            --surface: #ffffff;
            --line: #e2ddd0;
            --up: #a8391b;
        }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
            background: var(--paper);
            color: var(--ink);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background: var(--ink);
            color: #f4f1ea;
            padding: 26px 40px;
        }
        header a { color: inherit; text-decoration: none; }
        header .eyebrow { font-size: 13px; color: #b9c3d6; margin: 0 0 4px; }
        header h1 { font-family: Georgia, "Times New Roman", serif; font-size: 20px; margin: 0; }

        main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .card {
            width: 100%; max-width: 380px; background: var(--surface);
            border: 1px solid var(--line); border-radius: 4px; padding: 32px 30px;
        }
        .card h2 { font-family: Georgia, "Times New Roman", serif; font-size: 20px; margin: 0 0 22px; }

        .field { display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px; }
        .field label { font-size: 12.5px; color: var(--ink-soft); }
        .field input {
            padding: 9px 11px; border: 1px solid var(--line); border-radius: 3px;
            font-size: 14px; background: var(--surface); color: var(--ink);
        }
        .field-error { font-size: 12px; color: var(--up); margin-top: 4px; }

        .checkbox-row { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 13.5px; color: var(--ink-soft); }

        button.submit {
            width: 100%; padding: 10px; border-radius: 3px; border: 1px solid var(--ink);
            background: var(--ink); color: #fff; font-size: 14.5px; cursor: pointer; font-family: inherit;
        }

        .switch-link { margin-top: 18px; font-size: 13.5px; color: var(--ink-soft); text-align: center; }
        .switch-link a { color: var(--ink); font-weight: 600; }

        .status { background: #eef4ee; border: 1px solid #cfe3cf; color: #2c6e49; font-size: 13px; padding: 10px 12px; border-radius: 3px; margin-bottom: 18px; }
    </style>
</head>
<body>
<header>
    <a href="{{ route('reports.index') }}">
        <p class="eyebrow">Periodic report</p>
        <h1>Reported Online Incidents</h1>
    </a>
</header>

<main>
    <div class="card">
        @yield('content')
    </div>
</main>
</body>
</html>
