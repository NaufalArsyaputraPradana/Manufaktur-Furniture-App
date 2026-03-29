<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .hdr { border-bottom: 2px solid #4e73df; padding-bottom: 8px; margin-bottom: 16px; }
        .muted { color: #666; font-size: 11px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.items th, table.items td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        table.items th { background: #f4f6fb; }
        .right { text-align: right; }
        .total { font-size: 14px; font-weight: bold; margin-top: 12px; }
    </style>
</head>
<body>
    @include('customer.invoices.partials.content')
</body>
</html>
