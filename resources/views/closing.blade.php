<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            width: 100%;
            line-height: 12px;
        }

        * {
            page-break-inside: avoid;
            page-break-before: avoid;
            page-break-after: avoid;
        }

        table, tr, td {
            page-break-inside: avoid !important;
        }

        hr {
            height: 2px;
            background-color: #000;
            border: none;
        }

        .header {
            width: 100%;
            margin-bottom: 5px;
            height: 70px;
        }

        .ticket {
            width: 100%;
            padding: 5px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        th{
            white-space: nowrap;
            padding: 0 5px;
            text-align: start;
        }

        td {
            padding: 0 5px;
            white-space: nowrap;
        }

        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 11px;
            height: 60px;
        }
    </style>
</head>
<body>
<div class="ticket">
    {{-- Cabecera --}}
    <table class="header">
        <tr>
            <td style="width: 50%; text-align: center;">

                <img src="{{ public_path('logo-bn.png') }}" width="70px" height="70px">
            </td>
            <td class="bold" style="width: 50%; text-align: center">
                <div>Educativa México</div>
                <div>RFC: EME101110K71</div>
                <div>Av. 31 #697-B x 80 y 86 Cd. Caucel</div>
            </td>
        </tr>
    </table>
    <hr>

    <p>Fecha de corte: {{$selectedDay}}</p>


    <p><b>Detalles de los tickets</b></p>
    <table>
        <thead>
        <tr>
            <th style="width: 15%">Folio</th>
            <th style="width: 20%">Alumno</th>
            <th style="width: 15%">Método de pago</th>
            <th>Conceptos</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($ticket_products as $ticket_product)
            <tr>
                <td>{{$ticket_product->folio_ticket}}</td>
                <td>{{$ticket_product->complete_name}}</td>
                <td>{{$ticket_product->payment_method}}</td>
                <td>
                    <ul>
                    @foreach($ticket_product->products as $product)
                        <li>{{$product->product_name}}</li>
                    @endforeach
                    </ul>
                </td>
                <td>${{$ticket_product->amount - $ticket_product->discount_amount}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <hr>

    Totales
    <table>
        <tr>
            <td class="right bold">Total:</td>
            <td class="right bold">
                ${{ number_format($ticket_products->reduce(fn ($acc, $it) => $acc + ($it->amount - $it->discount_amount), 0), 2) }}</td>
        </tr>
    </table>
    <hr>

    {{-- Footer --}}
    <div class="footer">
        Este comprobante es únicamente para control administrativo interno <br>
        Este comprobante fue emitido: {{date('d/m/Y h:m a')}}
    </div>
</div>
</body>
</html>
