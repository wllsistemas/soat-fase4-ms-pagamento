<?php

namespace App\Infrastructure\Service;

use Exception;
use DomainException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;

class MercadoPago
{
    public function pixCopiaCola(array $dadosOrdem): array
    {
        MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN', ''));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

        try {
            $requestOptions = new RequestOptions();
            $requestOptions->setCustomHeaders(["X-Idempotency-Key: " . uniqid()]);

            $client = new PaymentClient();

            $req = $client->create([
                "notification_url"   => env("MERCADO_PAGO_WEBHOOK_URL", ""),
                "transaction_amount" => floatval($dadosOrdem["total"]),
                "payment_method_id"  => "pix",
                "payer" => [
                    "first_name" => $dadosOrdem["cliente_primeiro_nome"],
                    "last_name"  => $dadosOrdem["cliente_sobrenome"],
                    "email"      => $dadosOrdem["cliente_email"],

                    "identification" => [
                        "type"   => "CPF",
                        "number" => $dadosOrdem["cliente_documento"]
                    ]
                ],
            ], $requestOptions);

            $pagamentoId = $req->id;
            $dadosTransacao = $req->point_of_interaction->transaction_data;
            $copiaCola = $dadosTransacao->qr_code;
            $qrCode = "data:image/jpeg;base64," . $dadosTransacao->qr_code_base64;

            $dados = [
                "qr_code"        => $qrCode,
                "copia_cola"     => $copiaCola,
                "transacao"      => $dadosTransacao,
                "pagamento_id"   => $pagamentoId
            ];
        } catch (DomainException $err) {
            $errPayload = [
                "err"     => $err->getCode(),
                "message" => $err->getMessage()
            ];

            logger()->debug("MercadoPago.pagar.catch.DomainException", $errPayload);

            $dados = $errPayload;
        } catch (MPApiException $err) {
            $errPayload = [
                "err"     => $err->getMessage(),
                "message" => $err->getApiResponse()->getContent()
            ];

            logger()->debug("MercadoPago.pagar.catch.MPApiException", $errPayload);

            $dados = $errPayload;
        } catch (Exception $err) {
            $errPayload = [
                "err"     => $err->getCode(),
                "message" => $err->getMessage()
            ];

            logger()->debug("MercadoPago.pagar.catch.Exception", $errPayload);

            $dados = $errPayload;
        }

        return $dados;
    }
}
