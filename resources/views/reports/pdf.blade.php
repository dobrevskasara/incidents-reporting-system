<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reported Online Incidents Report</title>
    <style>
        @page { margin: 0 0 30px; }
        body { font-family: "DejaVu Sans", sans-serif; color: #17233b; font-size: 11px; margin: 0; }
        h1, h2 { font-family: "DejaVu Serif", Georgia, serif; font-weight: bold; }

        .header-band { background: #17233b; color: #f4f1ea; padding: 20px 34px 18px; margin-bottom: 18px; }
        .header-band .eyebrow { font-size: 11px; color: #b9c3d6; margin: 0 0 4px; }
        .header-band h1 { font-size: 19px; margin: 0 0 6px; color: #fff; }
        .header-band .meta { font-size: 10px; color: #d7dde8; }

        .body-pad { padding: 0 34px; }

        h2 { font-size: 13px; margin: 20px 0 8px; padding-bottom: 5px; border-bottom: 2px solid #17233b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        th, td { border-bottom: 1px solid #e2ddd0; padding: 5px 6px; text-align: left; font-size: 10.5px; }
        thead th { border-bottom: 2px solid #17233b; font-weight: bold; }
        td.num, th.num { text-align: right; }
        tbody tr:nth-child(even) td { background: #f9f8f5; }

        .stats-table td { border: none; padding: 4px 24px 4px 0; }
        .stats-table .value { font-family: "DejaVu Serif", Georgia, serif; font-size: 18px; font-weight: bold; }
        .stats-table .label { font-size: 9px; color: #4b5c78; }
        .up { color: #a8391b; }
        .down { color: #2c6e49; }

        .cols td { width: 50%; vertical-align: top; padding: 0 10px 0 0; border: none; }
        .bar-track { background: #ffffff; border: 1px solid #e2ddd0; height: 9px; }
        .bar-fill { background: #8a5a1f; height: 9px; }

        .footer { font-size: 9px; color: #4b5c78; margin-top: 14px; }
    </style>
</head>
<body>
    @php
        $withComparison = in_array('comparison', $sections);
        $showType = in_array('type', $sections);
        $showStatus = in_array('status', $sections);
        $showAge = in_array('age', $sections);
        $showPlatform = in_array('platform', $sections);
        $showTrend = in_array('trend', $sections);
    @endphp

    <div class="header-band">
        <p class="eyebrow">Periodic report</p>
        <h1>Reported Online Incidents</h1>
        <div class="meta">
            Period {{ $report['from']->format('d.m.Y') }} &ndash; {{ $report['to']->format('d.m.Y') }}
            @if ($withComparison)
                <br>
                Compared with {{ $report['previous_from']->format('d.m.Y') }} &ndash; {{ $report['previous_to']->format('d.m.Y') }}
                ({{ $report['comparison_is_auto'] ? 'previous period, automatic' : 'manually selected period' }})
            @endif
            @if ($report['filters']['type'] || $report['filters']['platform'] || $report['filters']['age_group'])
                <br>
                Filters:
                @if ($report['filters']['type']) type: {{ $report['filters']['type'] }}; @endif
                @if ($report['filters']['platform']) platform: {{ $report['filters']['platform'] }}; @endif
                @if ($report['filters']['age_group']) age group: {{ $report['filters']['age_group'] }}; @endif
            @endif
        </div>
    </div>

    <div class="body-pad">
        <h2>Summary</h2>
        <table class="stats-table">
            <tr>
                <td>
                    <div class="value">{{ $report['current']['total'] }}</div>
                    <div class="label">Total reports in the period</div>
                </td>
                @if ($withComparison)
                    <td>
                        <div class="value">{{ $report['previous']['total'] }}</div>
                        <div class="label">Reports in the previous period</div>
                    </td>
                    <td>
                        @php $change = $report['comparison']['total_change_percent']; @endphp
                        <div class="value {{ $change === null ? '' : ($change > 0 ? 'up' : ($change < 0 ? 'down' : '')) }}">
                            {{ $change === null ? 'new' : ($change > 0 ? '+' : '').$change.'%' }}
                        </div>
                        <div class="label">Change vs. previous period</div>
                    </td>
                @endif
            </tr>
        </table>

        @if ($showTrend)
            <h2>Reports trend</h2>
            @php $maxCount = max(1, collect($report['current']['trend'])->max('count')); @endphp
            <table>
                <thead><tr><th style="width: 42%;">Period</th><th style="width: 38%;">Chart</th><th class="num">Count</th></tr></thead>
                <tbody>
                @foreach ($report['current']['trend'] as $bucket)
                    <tr>
                        <td>{{ $bucket['label'] }}</td>
                        <td>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: {{ $bucket['count'] === 0 ? 0 : max(3, round(($bucket['count'] / $maxCount) * 100)) }}%;"></div>
                            </div>
                        </td>
                        <td class="num">{{ $bucket['count'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        @if ($showType)
            <h2>By incident type</h2>
            <table>
                <thead>
                    <tr>
                        <th>Type</th><th class="num">Current period</th>
                        @if ($withComparison)<th class="num">Previous period</th><th class="num">Change</th>@endif
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
                            <td class="num {{ $c === null ? '' : ($c > 0 ? 'up' : ($c < 0 ? 'down' : '')) }}">
                                {{ $c === null ? 'new' : ($c > 0 ? '+' : '').$c.'%' }}
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        @if ($showAge || $showPlatform)
            <table class="cols">
                <tr>
                    @if ($showAge)
                        <td>
                            <h2>By age group</h2>
                            @php $total = max(1, $report['current']['total']); @endphp
                            <table>
                                <thead><tr><th>Age group</th><th class="num">Reports</th><th class="num">Share</th></tr></thead>
                                <tbody>
                                @foreach ($report['current']['by_age_group'] as $group => $count)
                                    <tr>
                                        <td>{{ $group }}</td>
                                        <td class="num">{{ $count }}</td>
                                        <td class="num">{{ round(($count / $total) * 100, 1) }}%</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                    @endif
                    @if ($showPlatform)
                        <td>
                            <h2>By platform</h2>
                            @php $total = max(1, $report['current']['total']); @endphp
                            <table>
                                <thead><tr><th>Platform</th><th class="num">Reports</th><th class="num">Share</th></tr></thead>
                                <tbody>
                                @foreach ($report['current']['by_platform'] as $platform => $count)
                                    <tr>
                                        <td>{{ $platform }}</td>
                                        <td class="num">{{ $count }}</td>
                                        <td class="num">{{ round(($count / $total) * 100, 1) }}%</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                    @endif
                </tr>
            </table>
        @endif

        @if ($showStatus)
            <h2>By processing status</h2>
            <table>
                <thead><tr><th>Status</th><th class="num">Current period</th>@if ($withComparison)<th class="num">Previous period</th>@endif</tr></thead>
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
        @endif

        <div class="footer">Generated on {{ now()->format('d.m.Y H:i') }}</div>
    </div>
</body>
</html>
