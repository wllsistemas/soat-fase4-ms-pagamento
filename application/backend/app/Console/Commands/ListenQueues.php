<?php

namespace App\Console\Commands;

use App\Infrastructure\Repositories\MercadoPagoMongoRepository;
use Illuminate\Console\Command;
use App\Infrastructure\Service\MercadoPago;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class ListenQueues extends Command
{
    protected $signature = 'app:saga';
    protected $description = '';

    protected readonly AMQPStreamConnection $connection;
    protected MercadoPago $gatewayPagamento;

    public string $queueExchange;

    public function __construct()
    {
        parent::__construct();

        // $this->connection = app("rabbitmq.connection");
        // $this->queueExchange = 'soat.exchange';
        // $this->gatewayPagamento = app(MercadoPago::class);
    }

    public function handle()
    {
        // $this->info('SAGA iniciada!');

        // logger()->info("SAGA iniciada.", ["class" => __CLASS__, "method" => __METHOD__, "file" => __FILE__]);

        // $channel = $this->connection->channel();

        // // declara a exchange

        // $channel->exchange_declare($this->queueExchange, "direct", false, true, false);

        // // declara as filas

        // $filaOrcamentoAprovado = "orcamento_aprovado";

        // $channel->queue_declare(
        //     queue: $filaOrcamentoAprovado,
        //     passive: false,
        //     durable: true,
        //     exclusive: false,
        //     auto_delete: false
        // );

        // $channel->queue_bind(
        //     queue: $filaOrcamentoAprovado,
        //     exchange: $this->queueExchange,
        //     routing_key: 'orcamento.aprovado'
        // );

        // $filaPagamentoGeradoSucesso = "pagamento_gerado_sucesso";

        // $channel->queue_declare(
        //     queue: $filaPagamentoGeradoSucesso,
        //     passive: false,
        //     durable: true,
        //     exclusive: false,
        //     auto_delete: false
        // );

        // $channel->queue_bind(
        //     queue: $filaPagamentoGeradoSucesso,
        //     exchange: $this->queueExchange,
        //     routing_key: 'pagamento.gerado_sucesso'
        // );

        // $filaGeradoErro = "pagamento_gerado_erro";

        // $channel->queue_declare(
        //     queue: $filaGeradoErro,
        //     passive: false,
        //     durable: true,
        //     exclusive: false,
        //     auto_delete: false
        // );

        // $channel->queue_bind(
        //     queue: $filaGeradoErro,
        //     exchange: $this->queueExchange,
        //     routing_key: 'pagamento.gerado_erro'
        // );

        // // consumo das filas

        // $channel->basic_consume(
        //     queue: $filaOrcamentoAprovado,
        //     consumer_tag: '',
        //     no_local: false,
        //     no_ack: false,   // no_ack (manual ACK (informa ao rabbit que só pode remover da fila quando eu confirmar manualmente usando $msg->ack())
        //     exclusive: false,
        //     nowait: false,
        //     callback: fn($message) => $this->orcamentoAprovado($message, $channel)
        // );

        // while ($channel->is_consuming()) {
        //     $channel->wait();
        // }

        // $channel->close();

        // $dados_pagamento = $mercadoPago->pagar([]);
    }

    public function orcamentoAprovado(AMQPMessage $message, AMQPChannel $channel)
    {
        // $payload = json_decode($message->getBody(), true);

        // $message->ack(); // confirma leitura da mensagem (tira da fila)

        // logger()->debug("Orcamento aprovado recebido", $payload);

        // try {
        //     logger()->info("Orcamento aprovado.", [
        //         "payload" => $payload
        //     ]);

        //     $pixCopiaCola = $this->gatewayPagamento->pixCopiaCola($payload);


        //     if (array_key_exists("err", $pixCopiaCola)) {
        //         logger()->info("Pix gerado com erro.", $pixCopiaCola);

        //         $channel->basic_publish(
        //             exchange: $this->queueExchange,
        //             routing_key: 'pagamento.gerado_erro',
        //             msg: new AMQPMessage(
        //                 body: json_encode([...$pixCopiaCola, ...$payload]),
        //                 properties: [
        //                     'content_type'  => 'application/json',
        //                     'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT // torna a mensagem persistente
        //                 ]
        //             )
        //         );

        //         return;
        //     }

        //     logger()->info("Pix gerado com sucesso.", $pixCopiaCola);

        //     $save = MercadoPagoMongoRepository::getMongoDB()
        //         ->selectCollection('mercado_pago_pagamentos')
        //         ->insertOne([...$payload, ...$pixCopiaCola]);


        //     $channel->basic_publish(
        //         exchange: $this->queueExchange,
        //         routing_key: 'pagamento.gerado_sucesso',
        //         msg: new AMQPMessage(
        //             body: json_encode([...$payload, ...$pixCopiaCola]),
        //             properties: [
        //                 'content_type'  => 'application/json',
        //                 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT // torna a mensagem persistente
        //             ]
        //         )
        //     );
        // } catch (Throwable $error) {
        //     logger()->error("[Exception] ListenQueues.orcamentoAprovado.catch.Throwable", [
        //         "message" => $error->getMessage()
        //     ]);

        //     $channel->basic_publish(
        //         exchange: $this->queueExchange,
        //         routing_key: 'pagamento.gerado_erro',
        //         msg: new AMQPMessage(
        //             body: json_encode([
        //                 'err'     => $error->getCode(),
        //                 'message' => $error->getMessage()
        //             ]),
        //             properties: [
        //                 'content_type'  => 'application/json',
        //                 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT // torna a mensagem persistente
        //             ]
        //         )
        //     );
        // }
    }
}
