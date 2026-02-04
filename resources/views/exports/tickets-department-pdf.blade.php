<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tickets Report by Department</title>
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
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            font-weight: normal;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px 0;
        }
        .date-range {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th {
            background-color: #d3d3d3;
            padding: 8px;
            text-align: left;
            border: 1px solid #000;
            font-weight: bold;
        }
        table td {
            padding: 6px 8px;
            border: 1px solid #000;
        }
        .summary-table th, .summary-table td {
            text-align: center;
        }
        .summary-table .label-col {
            text-align: left;
        }
        .totals-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .visual-bar {
            position: relative;
            padding: 3px 0;
        }
        .bar {
            height: 20px;
            background-color: #666;
            position: relative;
        }
        .bar-percentage {
            position: absolute;
            top: 50%;
            left: 5px;
            transform: translateY(-50%);
            font-size: 10px;
            font-weight: bold;
            color: #000;
            z-index: 1;
        }
        .bar-closed {
            background-color: #90ee90;
        }
        .bar-hold {
            background-color: #add8e6;
        }
        .bar-open {
            background-color: #ffa07a;
        }
        .legend {
            margin: 15px 0;
            display: flex;
            justify-content: flex-end;
        }
        .legend-item {
            margin-left: 15px;
            display: flex;
            align-items: center;
        }
        .legend-box {
            width: 15px;
            height: 15px;
            margin-right: 5px;
            border: 1px solid #000;
        }
        .total-summary {
            margin: 20px 0;
            font-size: 14px;
        }
        .total-summary ul {
            list-style: none;
            padding: 0;
        }
        .total-summary li {
            margin: 5px 0;
        }
        .avoid-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HELPDESK REPORT BY DEPARTMENT</h1>
        <h2>REPORT FROM {{ $startDate->format('d/m/Y') }} TO {{ $endDate->format('d/m/Y') }}</h2>
    </div>

    <div class="section-title">1. TICKETS CREATED</div>

    <div class="date-range">
        {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
    </div>

    <div style="font-weight: bold; margin: 10px 0;">Number of Tickets Created by Department</div>

    <table class="summary-table">
        <thead>
            <tr>
                <th class="label-col">Department</th>
                <th>CLOSED</th>
                <th>HOLD</th>
                <th>OPEN</th>
                <th>All</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totals = [
                    'closed' => 0,
                    'hold' => 0,
                    'open' => 0,
                    'all' => 0
                ];
            @endphp
            @foreach($summary['department_data'] as $dept)
                <tr>
                    <td class="label-col">{{ $dept['name'] }}</td>
                    <td>{{ $dept['closed'] }}</td>
                    <td>{{ $dept['hold'] }}</td>
                    <td>{{ $dept['open'] }}</td>
                    <td>{{ $dept['all'] }}</td>
                </tr>
                @php
                    $totals['closed'] += $dept['closed'];
                    $totals['hold'] += $dept['hold'];
                    $totals['open'] += $dept['open'];
                    $totals['all'] += $dept['all'];
                @endphp
            @endforeach
            <tr class="totals-row">
                <td class="label-col">All:</td>
                <td>{{ $totals['closed'] }}</td>
                <td>{{ $totals['hold'] }}</td>
                <td>{{ $totals['open'] }}</td>
                <td>{{ $totals['all'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="legend">
        <div class="legend-item">
            <div class="legend-box bar-closed"></div>
            <span>CLOSED</span>
        </div>
        <div class="legend-item">
            <div class="legend-box bar-hold"></div>
            <span>HOLD</span>
        </div>
        <div class="legend-item">
            <div class="legend-box bar-open"></div>
            <span>OPEN</span>
        </div>
    </div>

    {{-- Render each department as its own table to prevent page-break issues with rowspan --}}
    @foreach($summary['department_data'] as $index => $dept)
        <div class="avoid-break">
            <table class="summary-table" style="margin: 0; border-top: {{ $index === 0 ? '1px solid #000' : 'none' }};">
                <tbody>
                    <tr>
                        <td rowspan="3" class="label-col" style="font-weight: bold; vertical-align: middle; width: 25%;">{{ $dept['name'] }}</td>
                        <td style="text-align: center; width: 10%;">{{ $dept['closed'] }}</td>
                        <td style="width: 65%;">
                            <div class="visual-bar">
                                <div class="bar bar-closed" style="width: {{ $dept['closed_percent'] }}%;">
                                    <span class="bar-percentage">{{ $dept['closed_percent'] }}%</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; width: 10%;">{{ $dept['hold'] }}</td>
                        <td style="width: 65%;">
                            <div class="visual-bar">
                                <div class="bar bar-hold" style="width: {{ $dept['hold_percent'] }}%;">
                                    <span class="bar-percentage">{{ $dept['hold_percent'] }}%</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; width: 10%;">{{ $dept['open'] }}</td>
                        <td style="width: 65%;">
                            <div class="visual-bar">
                                <div class="bar bar-open" style="width: {{ $dept['open_percent'] }}%;">
                                    <span class="bar-percentage">{{ $dept['open_percent'] }}%</span>
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

    <div style="margin: 20px 0; font-weight: bold;">
        Rows: {{ $summary['total_tickets'] }}
    </div>

    <div class="total-summary">
        <div style="font-size: 16px; font-weight: bold; margin-bottom: 10px;">
            TOTAL TICKETS CREATED: {{ $summary['total_tickets'] }}
        </div>
        <ul>
            <li>CLOSED/RESOLVED TICKETS: {{ $summary['closed_tickets'] }}</li>
            <li>OPEN/UNRESOLVED TICKETS: {{ $summary['open_tickets'] }}</li>
            <li>TICKETS ON HOLD: {{ $summary['hold_tickets'] }}</li>
        </ul>
    </div>
</body>
</html>
