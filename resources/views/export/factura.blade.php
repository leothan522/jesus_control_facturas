<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ViewPDF</title>
    <link rel="stylesheet" href="{{ public_path('css/invoice_style.css') }}" type="text/css" media="all" />
</head>

<body>
<div>
    <div class="py-4">
        <div class="px-14 py-6">
            <table class="w-full border-collapse border-spacing-0">
                <tbody>
                <tr>
                    <td class="w-full align-top">
                        <div>
                            <img src="{{ verImagen($empresa->image ?? '') }}" class="h-12"  alt="Logo"/>
                        </div>
                    </td>

                    <td class="align-top">
                        <div class="text-sm">
                            <table class="border-collapse border-spacing-0">
                                <tbody>
                                <tr>
                                    <td class="border-r pr-4">
                                        <div>
                                            <p class="whitespace-nowrap text-slate-400 text-right">Fecha de Emisión</p>
                                            <p class="whitespace-nowrap font-bold text-main text-right">{{--April 26, 2023--}}{{ $factura->fecha }}</p>
                                        </div>
                                    </td>
                                    <td class="pl-4">
                                        <div>
                                            <p class="whitespace-nowrap text-slate-400 text-right">Número</p>
                                            <p class="whitespace-nowrap font-bold text-main text-right">{{ $factura->numero }}</p>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-slate-100 px-14 py-6 text-sm">
            <table class="w-full border-collapse border-spacing-0">
                <tbody>
                <tr>
                    <td class="w-1/2 align-top">
                        <div class="text-sm text-neutral-600">
                            <p class="font-bold text-uppercase">{{ $empresa->nombre }}</p>
                            <p class="text-uppercase">{{ $empresa->rif }}</p>
                            <p class="text-uppercase">{{ $empresa->direccion }}</p>
                        </div>
                    </td>
                    <td class="w-1/2 align-top text-right">
                        <div class="text-sm text-neutral-600">
                            <p class="font-bold">Cliente</p>
                            <p class="text-uppercase">{{ $factura->cliente_nombre }}</p>
                            <p><span class="font-bold">RIF</span>: <span class="text-uppercase">{{ $factura->cliente_rif }}</span></p>
                            @if($factura->cliente_telefono)
                                <p><span class="font-bold">Teléfono</span>: <span class="text-uppercase">{{ $factura->cliente_telefono }}</span></p>
                            @endif
                            @if($factura->cliente_email)
                                <p><span class="font-bold">Email</span>: <span class="text-uppercase">{{ $factura->cliente_email }}</span></p>
                            @endif
                            @if($factura->cliente_direccion)
                                <p><span class="font-bold">Dirección</span>: <span class="text-uppercase">{{ $factura->cliente_direccion }}</span></p>
                            @endif
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="px-14 py-10 text-sm text-neutral-700">
            <table class="w-full border-collapse border-spacing-0">
                <thead>
                <tr>
                    <td colspan="4" class="border-main pb-3 pl-3 font-bold text-main text-center">NOTA DE ENTREGA</td>
                </tr>
                <tr>
                    <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">Cantidad</td>
                    <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Descripción</td>
                    <td class="border-b-2 border-main pb-3 pl-2 pr-3 text-right font-bold text-main">Precio</td>
                    <td class="border-b-2 border-main pb-3 pl-2 pr-3 text-right font-bold text-main">Total</td>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td class="border-b py-3 pl-3">{{ formatoMillares($item->cantidad, 0) }}</td>
                        <td class="border-b py-3 pl-2 text-uppercase">{{ $item->descripcion }}</td>
                        <td class="border-b py-3 pl-2 pr-3 text-right text-uppercase">{{ $item->moneda }} {{ formatoMillares($item->precio) }}</td>
                        <td class="border-b py-3 pl-2 pr-3 text-right"> {{ formatoMillares($item->cantidad * $item->precio) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4">
                        <table class="w-full border-collapse border-spacing-0">
                            <tbody>
                            <tr>
                                <td class="w-full"></td>
                                <td>
                                    <table class="w-full border-collapse border-spacing-0">
                                        <tbody>
                                        <tr>
                                            <td class="border-b p-3">
                                                <div class="whitespace-nowrap text-slate-400">Subtotal:</div>
                                            </td>
                                            <td class="border-b p-3 text-right">
                                                <div class="whitespace-nowrap font-bold text-main">{{ $factura->moneda }} {{ formatoMillares($factura->total) }}</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="p-3">
                                                <div class="whitespace-nowrap text-slate-400">I.V.A:</div>
                                            </td>
                                            <td class="p-3 text-right">
                                                <div class="whitespace-nowrap font-bold text-main">-</div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="bg-main p-3">
                                                <div class="whitespace-nowrap font-bold text-white">Total:</div>
                                            </td>
                                            <td class="bg-main p-3 text-right">
                                                <div class="whitespace-nowrap font-bold text-white">{{ $factura->moneda }} {{ formatoMillares($factura->total) }}</div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        {{--@if($pago)
            <div class="px-14 text-sm text-neutral-700">
                <p class="text-main font-bold">DETALLES DE PAGO</p>
                <p>Metodo: {{ $metodo }}</p>
                <p>Referencia: {{ $referencia }}</p>
                @if(!empty($banco))
                    <p>Banco: {{ $banco }}</p>
                @endif
                <p>Monto: {{ $monto }}</p>
                <p>Fecha Pago: {{ $fecha_pago }}</p>
            </div>
        @endif--}}

        <div class="px-14 py-6 text-sm">
            <table class="w-full border-collapse border-spacing-0">
                <tbody>
                <tr>
                    <td class="w-1/2 align-top">
                        <div class="text-sm text-neutral-600">
                            <p class="font-bold text-uppercase text-main">ESTREGADO POR</p>
                            <p class="border-b-2 border-main pb-3">Nombre:</p>
                            <p class="border-b-2 border-main pb-3">C.I:</p>
                            <p class="border-b-2 border-main pb-3">Firma:</p>
                        </div>
                    </td>
                    <td class="align-top text-right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td class="w-1/2 align-top">
                        <div class="text-sm text-neutral-600">
                            <p class="font-bold text-uppercase text-main">RECIBIDO POR</p>
                            <p class="border-b-2 border-main pb-3">Nombre:</p>
                            <p class="border-b-2 border-main pb-3">C.I:</p>
                            <p class="border-b-2 border-main pb-3">Firma:</p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        {{--@if(!empty($notas))
            <div class="px-14 py-10 text-sm text-neutral-700">
                <p class="text-main font-bold">Notas</p>
                <p class="italic">{{ $notas }}</p>
            </div>
        @endif--}}

        <footer class="fixed bottom-0 left-0 bg-slate-100 w-full text-neutral-600 text-center text-xs py-3 text-uppercase">
            {{ $empresa->nombre }}
            @if($empresa->email)
                <span class="text-slate-300 px-2">|</span>
                {{ $empresa->email }}
            @endif
            @if($empresa->telefono)
                <span class="text-slate-300 px-2">|</span>
                {{ $empresa->telefono }}
            @endif
        </footer>
    </div>
</div>
</body>

</html>
