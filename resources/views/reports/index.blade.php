<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reported Online Incidents Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --ink: #17233b;
            --ink-soft: #4b5c78;
            --paper: #f6f4ef;
            --surface: #ffffff;
            --line: #e2ddd0;
            --up: #a8391b;
            --down: #2c6e49;
            --accent: #8a5a1f;
            --radius: 4px;
        }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
            background: var(--paper);
            color: var(--ink);
            margin: 0;
            padding: 0 0 72px;
            font-variant-numeric: tabular-nums;
            line-height: 1.5;
        }
        h1, h2 { font-family: Georgia, "Times New Roman", serif; font-weight: 600; }
        a { transition: color .12s ease; }

        input:focus-visible, select:focus-visible, button:focus-visible, a:focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }
        input[type="checkbox"] { accent-color: var(--ink); cursor: pointer; }

        header {
            background: var(--ink);
            color: #f4f1ea;
            padding: 26px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            flex-wrap: wrap;
            box-shadow: 0 1px 0 rgba(0, 0, 0, .18);
        }
        header .eyebrow { font-size: 13px; color: #b9c3d6; margin: 0 0 6px; letter-spacing: .2px; }
        header h1 { margin: 0 0 10px; font-size: 25px; letter-spacing: .2px; }
        header .meta { font-size: 13.5px; color: #d7dde8; margin: 0; line-height: 1.7; }
        header .meta strong { color: #fff; }
        .header-auth { font-size: 13px; text-align: right; white-space: nowrap; padding-top: 4px; display: flex; align-items: center; gap: 12px; }
        .header-auth .whoami { color: #d7dde8; }
        .header-auth .whoami strong { color: #fff; }
        .header-auth form { display: inline; }
        .header-btn {
            padding: 8px 16px; border-radius: var(--radius); font-size: 13px; cursor: pointer;
            text-decoration: none; display: inline-flex; align-items: center; font-family: inherit;
            border: 1px solid rgba(244, 241, 234, .55); background: transparent; color: #f4f1ea;
            transition: background .12s ease, color .12s ease, border-color .12s ease;
        }
        .header-btn:hover { border-color: #f4f1ea; background: rgba(244, 241, 234, .08); }
        .header-btn.filled { background: #f4f1ea; color: var(--ink); border-color: #f4f1ea; }
        .header-btn.filled:hover { background: #fff; }
        .export-note { font-size: 12px; color: var(--ink-soft); margin-left: 2px; align-self: center; }

        .container { max-width: 980px; margin: 0 auto; padding: 0 40px; }

        section { padding: 32px 0; border-bottom: 1px solid var(--line); }
        .container > section:last-child { border-bottom: none; }
        section h2 { font-size: 16px; margin: 0 0 18px; color: var(--ink); }

        /* Filters */
        form.filters { display: flex; flex-wrap: wrap; gap: 18px 22px; align-items: end; }
        .field { display: flex; flex-direction: column; gap: 5px; min-width: 148px; }
        .field label { font-size: 12px; color: var(--ink-soft); }
        .field select, .field input {
            padding: 8px 10px; border: 1px solid var(--line); border-radius: var(--radius);
            font-size: 14px; background: var(--surface); color: var(--ink);
            transition: border-color .12s ease;
        }
        .field select:hover, .field input:hover { border-color: #c7c0af; }

        .actions { display: flex; gap: 10px; margin-left: auto; flex-wrap: wrap; align-items: center; }
        button, .btn {
            padding: 9px 18px; border-radius: var(--radius); border: 1px solid var(--ink);
            background: var(--ink); color: #fff; font-size: 14px; cursor: pointer;
            text-decoration: none; display: inline-flex; align-items: center;
            font-family: inherit; transition: background .12s ease, color .12s ease, border-color .12s ease;
        }
        button:hover, .btn:hover { background: #263659; border-color: #263659; }
        .btn.secondary { background: var(--surface); color: var(--ink); border-color: var(--line); }
        .btn.secondary:hover { background: var(--paper); border-color: var(--ink); }
        .error { color: var(--up); font-size: 12.5px; margin-top: 10px; }

        /* Nested sub-panels (custom date range, compare-mode options) */
        .sub-panel {
            border-left: 2px solid var(--line);
            padding-left: 16px;
            margin-top: 2px;
        }

        .toggles-row {
            flex-basis: 100%; display: flex; flex-direction: column; gap: 14px;
            margin-top: 6px; padding: 18px 20px; border-radius: var(--radius);
            background: rgba(23, 35, 59, 0.03); border: 1px solid var(--line);
        }
        .toggles-row > .toggles-label { font-size: 12px; color: var(--ink-soft); font-weight: 600; text-transform: none; }
        .toggles { display: flex; flex-wrap: wrap; gap: 10px 24px; }
        .toggles label { display: flex; align-items: center; gap: 7px; font-size: 13.5px; cursor: pointer; }

        /* Stat strip */
        .stat-strip { display: flex; flex-wrap: wrap; }
        .stat { flex: 1 1 180px; padding: 0 26px; border-left: 1px solid var(--line); }
        .stat:first-child { border-left: none; padding-left: 0; }
        .stat-strip.single-stat .stat { flex: 0 1 auto; padding-right: 0; }
        .stat .value { font-family: Georgia, "Times New Roman", serif; font-size: 30px; line-height: 1; }
        .stat .label { font-size: 12.5px; color: var(--ink-soft); margin-top: 8px; max-width: 220px; }
        .delta.up { color: var(--up); }
        .delta.down { color: var(--down); }

        /* Trend bars */
        .bar-row { display: flex; align-items: center; gap: 14px; margin-bottom: 10px; }
        .bar-row:last-child { margin-bottom: 0; }
        .bar-label { width: 190px; flex-shrink: 0; font-size: 13px; color: var(--ink-soft); }
        .bar-track { flex: 1; background: var(--surface); border: 1px solid var(--line); border-radius: 2px; height: 14px; overflow: hidden; }
        .bar-fill { background: var(--accent); height: 100%; transition: width .2s ease; }
        .bar-count { width: 36px; text-align: right; font-size: 13px; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        th, td { text-align: left; padding: 9px 4px; }
        thead th { border-bottom: 2px solid var(--ink); font-weight: 600; color: var(--ink); }
        tbody td { border-bottom: 1px solid var(--line); }
        tbody tr:nth-child(even) td { background: rgba(23, 35, 59, 0.025); }
        tbody tr:hover td { background: rgba(138, 90, 31, 0.06); }
        td.num, th.num { text-align: right; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0 40px; }
        .grid-2.single { grid-template-columns: 1fr; }
        .grid-2 > div:nth-child(even) { padding-left: 6px; border-left: 1px solid var(--line); }
        .grid-2 > div:nth-child(odd) { padding-right: 6px; }
        .grid-2.single > div { padding-left: 0; padding-right: 0; border-left: none; }
        @media (max-width: 760px) {
            .container { padding: 0 20px; }
            header { padding: 24px 20px; }
            .grid-2 { grid-template-columns: 1fr; }
            .grid-2 > div:nth-child(even) { border-left: none; padding-left: 0; }
            .actions { margin-left: 0; width: 100%; }
        }

        .empty-note {
            color: var(--ink-soft); font-size: 13.5px; background: var(--surface);
            border: 1px dashed var(--line); border-radius: var(--radius); padding: 16px 18px;
        }
    </style>
</head>
<body>
<header>
    <div>
        <p class="eyebrow">Periodic report</p>
        <h1>Reported Online Incidents</h1>
        <p class="meta">
            Period <strong>{{ $report['from']->format('d.m.Y') }} – {{ $report['to']->format('d.m.Y') }}</strong>
            @if (in_array('comparison', $sections))
                <br>
                Compared with <strong>{{ $report['previous_from']->format('d.m.Y') }} – {{ $report['previous_to']->format('d.m.Y') }}</strong>
                ({{ $report['comparison_is_auto'] ? 'previous period, automatic' : 'manually selected period' }})
            @endif
        </p>
    </div>
    <div class="header-auth">
        @auth
            <span class="whoami">Logged in as <strong>{{ auth()->user()->name }}</strong></span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="header-btn">Log out</button>
            </form>
        @else
            <a href="{{ route('register') }}" class="header-btn">Register</a>
            <a href="{{ route('login') }}" class="header-btn filled">Log in</a>
        @endauth
    </div>
</header>

<div class="container">
    <section>
        <h2>Filters</h2>
        <form class="filters" method="GET" action="{{ route('reports.index') }}">
            <input type="hidden" name="sections_submitted" value="1">

            <div class="field">
                <label for="period">Period</label>
                <select name="period" id="period" onchange="document.getElementById('custom-dates').style.display = this.value === 'custom' ? 'flex' : 'none'">
                    @foreach (['week' => 'This week', 'month' => 'This month', 'quarter' => 'This quarter', 'year' => 'This year', 'custom' => 'Custom period'] as $value => $label)
                        <option value="{{ $value }}" @selected($period === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div id="custom-dates" class="sub-panel" style="display: {{ $period === 'custom' ? 'flex' : 'none' }}; gap: 18px;">
                <div class="field">
                    <label for="from">From date</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}">
                </div>
                <div class="field">
                    <label for="to">To date</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}">
                </div>
            </div>

            <div class="field">
                <label for="type">Incident type</label>
                <select name="type" id="type">
                    <option value="">All types</option>
                    @foreach ($types as $type)
                        <option value="{{ $type }}" @selected(($filters['type'] ?? null) === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="platform">Platform</label>
                <select name="platform" id="platform">
                    <option value="">All platforms</option>
                    @foreach ($platforms as $platform)
                        <option value="{{ $platform }}" @selected(($filters['platform'] ?? null) === $platform)>{{ $platform }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="age_group">Age group</label>
                <select name="age_group" id="age_group">
                    <option value="">All age groups</option>
                    @foreach ($ageGroups as $ageGroup)
                        <option value="{{ $ageGroup }}" @selected(($filters['age_group'] ?? null) === $ageGroup)>{{ $ageGroup }}</option>
                    @endforeach
                </select>
            </div>

            <div class="actions">
                <button type="submit">Show report</button>
                <a class="btn secondary" href="{{ route('reports.export.pdf', request()->query()) }}">Download PDF</a>
                <a class="btn secondary" href="{{ route('reports.export.excel', request()->query()) }}">Download Excel</a>
                @guest
                    <span class="export-note">Login required to download</span>
                @endguest
            </div>

            <div class="toggles-row">
                <div class="toggles-label">Include in the report (you can select just some of them):</div>
                <div class="toggles">
                    @foreach (['type' => 'By type', 'age' => 'By age', 'platform' => 'By platform', 'status' => 'By status', 'trend' => 'Trend over time', 'comparison' => 'Comparison with another period'] as $value => $label)
                        <label>
                            <input type="checkbox" name="sections[]" value="{{ $value }}" @checked(in_array($value, $sections))
                                @if ($value === 'comparison') onchange="document.getElementById('compare-mode-row').style.display = this.checked ? 'flex' : 'none'" @endif>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>

                <div id="compare-mode-row" class="sub-panel" style="display: {{ in_array('comparison', $sections) ? 'flex' : 'none' }}; gap: 18px; align-items: end; flex-wrap: wrap;">
                    <div class="field">
                        <label for="compare_mode">Compare with</label>
                        <select name="compare_mode" id="compare_mode" onchange="document.getElementById('compare-dates').style.display = this.value === 'custom' ? 'flex' : 'none'">
                            <option value="auto" @selected($compareMode === 'auto')>Previous period (automatic, same length)</option>
                            <option value="custom" @selected($compareMode === 'custom')>Choose a period manually</option>
                        </select>
                    </div>
                    <div id="compare-dates" style="display: {{ $compareMode === 'custom' ? 'flex' : 'none' }}; gap: 18px;">
                        <div class="field">
                            <label for="compare_from">From</label>
                            <input type="date" name="compare_from" id="compare_from" value="{{ request('compare_from') }}">
                        </div>
                        <div class="field">
                            <label for="compare_to">To</label>
                            <input type="date" name="compare_to" id="compare_to" value="{{ request('compare_to') }}">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @error('to')
            <div class="error">{{ $message }}</div>
        @enderror
        @error('compare_to')
            <div class="error">{{ $message }}</div>
        @enderror
    </section>

    <section>
        <h2>Summary</h2>
        @php $withComparison = in_array('comparison', $sections); $change = $report['comparison']['total_change_percent']; @endphp
        <div class="stat-strip {{ $withComparison ? '' : 'single-stat' }}">
            <div class="stat">
                <div class="value">{{ $report['current']['total'] }}</div>
                <div class="label">Total reports in the period</div>
            </div>
            @if ($withComparison)
                <div class="stat">
                    <div class="value">{{ $report['previous']['total'] }}</div>
                    <div class="label">Reports in the comparison period ({{ $report['previous_from']->format('d.m.Y') }}–{{ $report['previous_to']->format('d.m.Y') }})</div>
                </div>
                <div class="stat">
                    <div class="value delta {{ $change === null ? '' : ($change > 0 ? 'up' : ($change < 0 ? 'down' : '')) }}">
                        {{ $change === null ? 'new' : ($change > 0 ? '▲ +' : ($change < 0 ? '▼ ' : '')).$change.'%' }}
                    </div>
                    <div class="label">Change compared to the comparison period</div>
                </div>
            @endif
        </div>
    </section>

    @if (in_array('trend', $sections))
        <section>
            <h2>Reports trend</h2>
            @php $maxCount = max(1, collect($report['current']['trend'])->max('count')); @endphp
            @foreach ($report['current']['trend'] as $bucket)
                <div class="bar-row">
                    <div class="bar-label">{{ $bucket['label'] }}</div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width: {{ $bucket['count'] === 0 ? 0 : max(3, round(($bucket['count'] / $maxCount) * 100)) }}%"></div>
                    </div>
                    <div class="bar-count">{{ $bucket['count'] }}</div>
                </div>
            @endforeach
        </section>
    @endif

    @php
        $showType = in_array('type', $sections);
        $showStatus = in_array('status', $sections);
        $showAge = in_array('age', $sections);
        $showPlatform = in_array('platform', $sections);
    @endphp

    @if ($showType || $showStatus)
        <section>
            <div class="grid-2 {{ ($showType && $showStatus) ? '' : 'single' }}">
                @if ($showType)
                    <div>
                        <h2>By incident type</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>Type</th><th class="num">Current</th>
                                    @if ($withComparison)<th class="num">Previous</th><th class="num">Change</th>@endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($report['current']['by_type'] as $type => $count)
                                    @php $c = $report['comparison']['by_type'][$type]['change_percent'] ?? null; @endphp
                                    <tr>
                                        <td>{{ $type }}</td>
                                        <td class="num">{{ $count }}</td>
                                        @if ($withComparison)
                                            <td class="num">{{ $report['comparison']['by_type'][$type]['previous'] ?? 0 }}</td>
                                            <td class="num delta {{ $c === null ? '' : ($c > 0 ? 'up' : ($c < 0 ? 'down' : '')) }}">
                                                {{ $c === null ? 'new' : ($c > 0 ? '+' : '').$c.'%' }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if ($showStatus)
                    <div>
                        <h2>By processing status</h2>
                        <table>
                            <thead>
                                <tr><th>Status</th><th class="num">Current</th>@if ($withComparison)<th class="num">Previous</th>@endif</tr>
                            </thead>
                            <tbody>
                                @foreach ($report['current']['by_status'] as $status => $count)
                                    <tr>
                                        <td>{{ ucfirst($status) }}</td>
                                        <td class="num">{{ $count }}</td>
                                        @if ($withComparison)<td class="num">{{ $report['previous']['by_status'][$status] ?? 0 }}</td>@endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if ($showAge || $showPlatform)
        <section>
            <div class="grid-2 {{ ($showAge && $showPlatform) ? '' : 'single' }}">
                @if ($showAge)
                    <div>
                        <h2>By age group</h2>
                        <table>
                            <thead>
                                <tr><th>Age group</th><th class="num">Reports</th><th class="num">Share</th></tr>
                            </thead>
                            <tbody>
                                @php $total = max(1, $report['current']['total']); @endphp
                                @foreach ($report['current']['by_age_group'] as $group => $count)
                                    <tr>
                                        <td>{{ $group }}</td>
                                        <td class="num">{{ $count }}</td>
                                        <td class="num">{{ round(($count / $total) * 100, 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if ($showPlatform)
                    <div>
                        <h2>By platform</h2>
                        <table>
                            <thead>
                                <tr><th>Platform</th><th class="num">Reports</th><th class="num">Share</th></tr>
                            </thead>
                            <tbody>
                                @php $total = max(1, $report['current']['total']); @endphp
                                @foreach ($report['current']['by_platform'] as $platform => $count)
                                    <tr>
                                        <td>{{ $platform }}</td>
                                        <td class="num">{{ $count }}</td>
                                        <td class="num">{{ round(($count / $total) * 100, 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @unless ($showType || $showStatus || $showAge || $showPlatform || in_array('trend', $sections))
        <section>
            <p class="empty-note">No section is selected to display. Select at least one checkbox above ("Include in the report") and click "Show report".</p>
        </section>
    @endunless
</div>
</body>
</html>
