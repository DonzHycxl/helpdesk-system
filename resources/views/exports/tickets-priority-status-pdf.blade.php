<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Priority vs Status Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 { margin: 0; font-size: 24px; font-weight: bold; }
        .header h2 { margin: 5px 0 0 0; font-size: 16px; font-weight: normal; }
        .section-title { font-size: 16px; font-weight: bold; margin: 20px 0 10px 0; }
        .date-range { font-size: 14px; font-weight: bold; margin: 15px 0; }

        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background-color: #d3d3d3; padding: 8px; text-align: center; border: 1px solid #000; font-weight: bold; }
        td { padding: 6px 8px; border: 1px solid #000; }

        .summary-table th, .summary-table td { text-align: center; }
        .summary-table .label-col { text-align: left; }
        .totals-row { font-weight: bold; background-color: #f0f0f0; }

        .visual-bar { position: relative; padding: 3px 0; }
        .bar { height: 20px; background-color: #666; position: relative; }
        .bar-percentage {
            position: absolute; top: 50%; left: 5px; transform: translateY(-50%);
            font-size: 10px; font-weight: bold; color: #000; z-index: 1;
        }

        /* Status Colors */
        .bar-closed { background-color: #90ee90; }
        .bar-hold { background-color: #add8e6; }
        .bar-open { background-color: #ffa07a; }

        .legend { margin: 15px 0; display: flex; justify-content: flex-end; }
        .legend-item { margin-left: 15px; display: flex; align-items: center; }
        .legend-box { width: 15px; height: 15px; margin-right: 5px; border: 1px solid #000; }

        .total-summary { margin: 20px 0; font-size: 14px; }
        .total-summary li { margin: 5px 0; }
        .avoid-break { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PRIORITY VS STATUS REPORT</h1>
        <h2>REPORT FROM {{ $startDate->format('d/m/Y') }} TO {{ $endDate->format('d/m/Y') }}</h2>
    </div>

    <div class="section-title">3. TICKETS CREATED</div>
    <div class="date-range">{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</div>

    <div style="font-weight: bold; margin: 10px 0;">Number of Tickets Created by Priority</div>

    <table class="summary-table">
        <thead>
            <tr>
                <th class="label-col">Priority</th>
                <th>CLOSED</th>
                <th>HOLD</th>
                <th>OPEN</th>
                <th>All</th>
                <th>%</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotals = [
                    'closed' => 0,
                    'hold' => 0,
                    'open' => 0,
                    'all' => 0
                ];
                $overallTotal = $summary['total_tickets'] > 0 ? $summary['total_tickets'] : 1;
            @endphp
            @foreach($summary['priorities'] as $priorityName)
                @php
                    // Extract counts from matrix for this priority
                    $closed = 0; $hold = 0; $open = 0;

                    // Iterate statuses to sum them up based on naming convention
                    foreach($summary['statuses'] as $statusName) {
                        $count = $summary['matrix'][$priorityName][$statusName] ?? 0;
                        if(str_contains($statusName, 'Closed')) $closed += $count;
                        elseif(str_contains($statusName, 'Hold')) $hold += $count;
                        elseif(str_contains($statusName, 'Open')) $open += $count;
                    }
                    $all = $summary['priority_totals'][$priorityName];

                    // Add to grand totals
                    $grandTotals['closed'] += $closed;
                    $grandTotals['hold'] += $hold;
                    $grandTotals['open'] += $open;
                    $grandTotals['all'] += $all;

                    $priorityLabel = match ($priorityName) {
                        'Critical' => '1',
                        'Semi-critical' => '2',
                        'Non-critical' => '3',
                        'General Enquiries' => '4',
                        'Change Request' => '5',
                        default => $priorityName,
                    };
                @endphp
                <tr>
                    <td class="label-col">{{ $priorityLabel }}</td>
                    <td>{{ $closed }}</td>
                    <td>{{ $hold }}</td>
                    <td>{{ $open }}</td>
                    <td>{{ $all }}</td>
                    <td>{{ round(($all / $overallTotal) * 100) }}%</td>
                </tr>
            @endforeach
            <tr class="totals-row">
                <td class="label-col">All:</td>
                <td>{{ $grandTotals['closed'] }}</td>
                <td>{{ $grandTotals['hold'] }}</td>
                <td>{{ $grandTotals['open'] }}</td>
                <td>{{ $grandTotals['all'] }}</td>
                <td>100%</td>
            </tr>
        </tbody>
    </table>

    <div class="legend">
        <div class="legend-item"><div class="legend-box bar-closed"></div> <span>CLOSED</span></div>
        <div class="legend-item"><div class="legend-box bar-hold"></div> <span>HOLD</span></div>
        <div class="legend-item"><div class="legend-box bar-open"></div> <span>OPEN</span></div>
    </div>

    {{-- Render each Priority as its own table block --}}
    @foreach($summary['priorities'] as $index => $priorityName)
        @php
            $all = $summary['priority_totals'][$priorityName];

            $closed = 0; $hold = 0; $open = 0;
            foreach($summary['statuses'] as $statusName) {
                $count = $summary['matrix'][$priorityName][$statusName] ?? 0;
                if(str_contains($statusName, 'Closed')) $closed += $count;
                elseif(str_contains($statusName, 'Hold')) $hold += $count;
                elseif(str_contains($statusName, 'Open')) $open += $count;
            }

            $closedPercent = $all > 0 ? round(($closed / $all) * 100) : 0;
            $holdPercent = $all > 0 ? round(($hold / $all) * 100) : 0;
            $openPercent = $all > 0 ? round(($open / $all) * 100) : 0;

            $priorityLabel = match ($priorityName) {
                'Critical' => 'Priority 1',
                'Semi-critical' => 'Priority 2',
                'Non-critical' => 'Priority 3',
                'General Enquiries' => 'Priority 4',
                'Change Request' => 'Priority 5',
                default => $priorityName,
            };
        @endphp

        <div class="avoid-break">
            <table class="summary-table" style="margin: 0; border-top: {{ $index === 0 ? '1px solid #000' : 'none' }};">
                <tbody>
                    <tr>
                        <td rowspan="3" class="label-col" style="font-weight: bold; vertical-align: middle; width: 25%;">{{ $priorityLabel }}</td>
                        <td style="text-align: center; width: 10%;">{{ $closed }}</td>
                        <td style="width: 65%;">
                            <div class="visual-bar">
                                <div class="bar bar-closed" style="width: {{ $closedPercent }}%;">
                                    <span class="bar-percentage">{{ $closedPercent }}%</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; width: 10%;">{{ $hold }}</td>
                        <td style="width: 65%;">
                            <div class="visual-bar">
                                <div class="bar bar-hold" style="width: {{ $holdPercent }}%;">
                                    <span class="bar-percentage">{{ $holdPercent }}%</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; width: 10%;">{{ $open }}</td>
                        <td style="width: 65%;">
                            <div class="visual-bar">
                                <div class="bar bar-open" style="width: {{ $openPercent }}%;">
                                    <span class="bar-percentage">{{ $openPercent }}%</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    <div style="margin: 20px 0; font-weight: bold;">
        Rows: {{ $summary['total_tickets'] }}
    </div>

    <div class="total-summary">
        <div style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">
            TOTAL TICKETS CREATED: {{ $summary['total_tickets'] }}
        </div>
        <ul>
            <li>CLOSED/RESOLVED TICKETS: {{ $grandTotals['closed'] }}</li>
            <li>OPEN/UNRESOLVED TICKETS: {{ $grandTotals['open'] }}</li>
            <li>TICKETS ON HOLD: {{ $grandTotals['hold'] }}</li>
        </ul>
    </div>
</body>
</html>
