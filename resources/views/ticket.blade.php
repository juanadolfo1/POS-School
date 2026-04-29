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
            border-collapse: collapse;
            margin-top: 5px;
        }

        td {
            padding: 2px 0;
        }

        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 11px;
        }
    </style>
</head>
<body>
<div class="ticket">
    {{-- Cabecera --}}
    <table style="width: 100%; margin-bottom: 5px">
        <tr>
            <td style="width: 30%; text-align: center;">

                <img src="{{ public_path('logo-bn.png') }}" width="70px" height="70px">
            </td>
            <td class="bold" style="width: 70%; text-align: start; font-size: 12px">
                <div>Educativa México</div>
                <div>RFC: EME101110K71</div>
                <div>Av. 31 #697-B x 80 y 86 <br> Cd. Caucel</div>
            </td>
        </tr>
    </table>
    <hr>
    <div>
        <div>
            <b>Alumno:</b> {{implode(' ', [$ticket->student->name, $ticket->student->first_lastname, $ticket->student->second_lastname])}}
        </div>
        <div><b>Nivel:</b> {{$ticket->student->academic_level}}</div>
        <div><b>Grado y grupo:</b> {{$ticket->student->group}}</div>
    </div>
    <hr>
    {{-- Info venta --}}
    <div>
        <div><b>Folio:</b> {{ $ticket->folio_ticket}}</div>
        <div><b>Fecha:</b> {{ $ticket->created_at->format('d/m/Y h:i a') }}</div>
        <hr>
    </div>

    Concepto
    <table>
        @foreach ($ticket->pay_concetps as $pay_concetp)
            <tr>
                <td>{{ $pay_concetp->quantity }} x {{ $pay_concetp->pay_concept_name }}</td>
                <td class="right">
                    ${{ number_format($pay_concetp->quantity * $pay_concetp->amount, 2) }}
                </td>
            </tr>
        @endforeach
    </table>
    <hr>

    Totales
    <table>
        <tr>
            <td class="right bold">Subtotal:</td>
            <td class="right">${{ number_format($ticket->amount, 2) }}</td>
        </tr>
        <tr>
            <td class="right bold">Descuento:</td>
            <td class="right">${{ number_format($ticket->discount_amount, 2) }}</td>
        </tr>
        <tr>
            <td class="right bold">Total:</td>
            <td class="right bold">${{ number_format($ticket->amount - $ticket->discount_amount, 2) }}</td>
        </tr>
    </table>
    <hr>

    <div>Ticket expedido: {{date('d/m/Y h:i:s a')}}</div>
    {{-- Footer --}}
    @if ($ticketCliente)
        <div class="footer">
            Este documento no tiene validez fiscal. <br>
            Uso exclusivo de la institución. <br>
            Gracias por su confianza.
        </div>
    @else
        <div class="footer">
            Este documento no tiene validez fiscal. <br>
            Uso exclusivo para auditoria <br>
            de la institución.
        </div>
    @endif
</div>
</body>
</html>
