<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0F6D8E">
    <title>HistoStains — Maintenance</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
            background: #F0F4F8;
            color: #1A2332;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background: #fff;
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            max-width: 20rem;
            width: 100%;
            text-align: center;
            box-shadow: 0 1px 3px rgb(0 0 0 / .08);
        }
        .icon {
            width: 4rem; height: 4rem;
            background: #EFF6FF;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .icon svg { width: 2rem; height: 2rem; color: #0F6D8E; stroke: currentColor; }
        h1 { font-size: 1.125rem; font-weight: 600; margin-bottom: .5rem; }
        p  { font-size: .875rem; color: #6B7A8D; line-height: 1.5; }
        .retry {
            display: inline-block;
            margin-top: 1.5rem;
            background: #0F6D8E;
            color: #fff;
            border-radius: .75rem;
            padding: .75rem 1.5rem;
            font-size: .875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
            </svg>
        </div>
        <h1>Scheduled Maintenance</h1>
        <p>HistoStains is being updated. Please check back shortly — this typically takes under two minutes.</p>
        <a href="/" class="retry" onclick="location.reload(); return false;">Retry</a>
    </div>
</body>
</html>
