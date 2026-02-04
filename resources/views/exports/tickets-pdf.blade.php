<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tickets Report</title>
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
        .summary-table .month-label {
            text-align: center;
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
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MONTHLY HELPDESK REPORT</h1>
        <h2>REPORT FROM {{ $startDate->format('d/m/Y') }} TO {{ $endDate->format('d/m/Y') }}</h2>
    </div>

    <div class="section-title">2. TICKETS CREATED</div>

    <div class="date-range">
        {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
    </div>

    <div style="font-weight: bold; margin: 10px 0;">Number of Tickets Created by Month</div>

    <table class="summary-table">
        <thead>
            <tr>
                <th class="month-label">Month</th>
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
            @foreach($summary['monthly_data'] as $month)
                <tr>
                    <td class="month-label">{{ $month['month'] }}</td>
                    <td>{{ $month['closed'] }}</td>
                    <td>{{ $month['hold'] }}</td>
                    <td>{{ $month['open'] }}</td>
                    <td>{{ $month['all'] }}</td>
                </tr>
                @php
                    $totals['closed'] += $month['closed'];
                    $totals['hold'] += $month['hold'];
                    $totals['open'] += $month['open'];
                    $totals['all'] += $month['all'];
                @endphp
            @endforeach
            <tr class="totals-row">
                <td>All:</td>
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

    <table class="summary-table">
        <tbody>
            @foreach($summary['monthly_data'] as $month)
                <tr>
                    <td rowspan="3" class="month-label" style="font-weight: bold; vertical-align: middle; width: 100px">{{ $month['month'] }}</td>
                    <td style="text-align: center; width: 50px;">{{ $month['closed'] }}</td>
                    <td>
                        <div class="visual-bar">
                            <div class="bar bar-closed" style="width: {{ $month['closed_percent'] }}%;">
                                <span class="bar-percentage">{{ $month['closed_percent'] }}%</span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 50px;">{{ $month['hold'] }}</td>
                    <td>
                        <div class="visual-bar">
                            <div class="bar bar-hold" style="width: {{ $month['hold_percent'] }}%;">
                                <span class="bar-percentage">{{ $month['hold_percent'] }}%</span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center; width: 50px;">{{ $month['open'] }}</td>
                    <td>
                        <div class="visual-bar">
                            <div class="bar bar-open" style="width: {{ $month['open_percent'] }}%;">
                                <span class="bar-percentage">{{ $month['open_percent'] }}%</span>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

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

<!--    @#if($tickets->count() > 0)
        <div class="page-break"></div>

        <div class="section-title">TICKETS DETAILS</div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Department</th>
                    <th>Assigned To</th>
                    <th>Reported</th>
                </tr>
            </thead>
            <tbody>
                @#foreach($tickets as $ticket)
                    <tr>
                        <td>{#{ $ticket->id }}</td>
                        <td>{#{ Str::limit($ticket->subject, 40) }}</td>
                        <td>{#{ $ticket->status?->name ?? 'N/A' }}</td>
                        <td>{#{ $ticket->priority?->name ?? 'N/A' }}</td>
                        <td>{#{ $ticket->department?->name ?? 'N/A' }}</td>
                        <td>{#{ $ticket->assignedTo?->name ?? 'Unassigned' }}</td>
                        <td>{#{ $ticket->datetime_reported?->format('d/m/Y') ?? 'N/A' }}</td>
                    </tr>
                @#endforeach
            </tbody>
        </table>
    @#endif -->
</body>
</html>
