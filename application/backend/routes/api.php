<?php

use App\Http\PagamentoApi;
use Illuminate\Support\Facades\Route;

Route::get(
    "ping",
    fn() => response()->json([
        "err" => false,
        "msg" => "pong [pag-ms]",
    ]),
);

Route::fallback(
    fn() => response()->json([
        "err" => true,
        "msg" => "Recurso não encontrado",
    ]),
);


Route::get('pagamento/mercado-pago-wh', function () {
    logger()->debug('Mercadopago.webhook.call', [
        'headers' => request()->headers->all(),
        'body' => request()->all()
    ]);

    return response()->json([
        "err" => false,
        "msg" => "recebido"
    ]);
});

Route::get("/ordem/{uuid}", [PagamentoApi::class, "readOne"]);

Route::get("ping-message-broker", function () {
    try {
        $brokerConnection = new AMQPStreamConnection(
            config('queue.connections.rabbitmq.hosts.0.host'),
            config('queue.connections.rabbitmq.hosts.0.port'),
            config('queue.connections.rabbitmq.hosts.0.user'),
            config('queue.connections.rabbitmq.hosts.0.password')
        );;

        $connection = $brokerConnection->getConnection();

        dd(
            $connection
        );
    } catch (Throwable $err) {
        dd($err);
    }
});
