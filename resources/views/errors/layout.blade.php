<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') · BookFlow</title>

        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            :root {
                color-scheme: light dark;
                --bg: #f9fafb;
                --bg-card: #ffffff;
                --border: #e5e7eb;
                --text: #111827;
                --text-muted: #6b7280;
                --accent: #6366f1;
                --accent-hover: #4f46e5;
            }
            @media (prefers-color-scheme: dark) {
                :root {
                    --bg: #030712;
                    --bg-card: #111827;
                    --border: #1f2937;
                    --text: #f3f4f6;
                    --text-muted: #9ca3af;
                }
            }
            * { box-sizing: border-box; }
            body {
                margin: 0;
                min-height: 100vh;
                font-family: 'Figtree', system-ui, -apple-system, sans-serif;
                background: var(--bg);
                color: var(--text);
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
            }
            .card {
                width: 100%;
                max-width: 28rem;
                background: var(--bg-card);
                border: 1px solid var(--border);
                border-radius: 1.5rem;
                padding: 2.5rem 2rem;
                text-align: center;
                box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1);
            }
            .icon {
                display: grid;
                place-items: center;
                width: 4rem;
                height: 4rem;
                margin: 0 auto 1.5rem;
                border-radius: 999px;
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
                color: white;
            }
            .code {
                font-size: 0.75rem;
                font-weight: 600;
                letter-spacing: 0.1em;
                text-transform: uppercase;
                color: var(--text-muted);
                margin: 0 0 0.5rem;
            }
            h1 {
                font-size: 1.5rem;
                font-weight: 700;
                margin: 0 0 0.75rem;
                letter-spacing: -0.015em;
            }
            p {
                font-size: 0.9375rem;
                color: var(--text-muted);
                margin: 0 0 2rem;
                line-height: 1.6;
            }
            a.button {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.625rem 1.25rem;
                background: var(--accent);
                color: white;
                text-decoration: none;
                border-radius: 0.625rem;
                font-size: 0.875rem;
                font-weight: 600;
                transition: background 0.15s;
            }
            a.button:hover { background: var(--accent-hover); }
            .brand {
                margin-top: 2rem;
                font-size: 0.75rem;
                color: var(--text-muted);
                opacity: 0.7;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="icon">
                @yield('icon')
            </div>
            <p class="code">@yield('code') · @yield('title')</p>
            <h1>@yield('heading')</h1>
            <p>@yield('message')</p>
            <a href="/" class="button">
                @yield('action', 'Take me home')
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7" />
                </svg>
            </a>
            <p class="brand">BookFlow</p>
        </div>
    </body>
</html>
