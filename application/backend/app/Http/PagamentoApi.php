<?php

namespace App\Http;

use App\Exception\DomainHttpException;
use App\Infrastructure\Repositories\MercadoPagoMongoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PagamentoApi
{
    public function __construct(public readonly MercadoPagoMongoRepository $repositorio) {}

    public function readOne(Request $req)
    {
        try {
            // validacao basica sem regras de negocio
            $validacao = Validator::make($req->merge(['uuid' => $req->route('uuid')])->only(['uuid']), [
                'uuid' => ['required', 'string', 'uuid'],
            ])->stopOnFirstFailure(true);

            if ($validacao->fails()) {
                throw new DomainHttpException($validacao->errors()->first(), Response::HTTP_BAD_REQUEST);
            }

            $dados = $validacao->validated();

            $res = MercadoPagoMongoRepository::getMongoDB()
                ->selectCollection('mercado_pago_pagamentos')
                ->findOne([
                    'ordem_uuid' => $dados['uuid']
                ]);
        } catch (DomainHttpException $err) {
            return response()->json([
                'err' => true,
                'msg' => $err->getMessage(),
            ], $err->getCode());
        } catch (Throwable $err) {
            return response()->json([
                'err' => true,
                'msg' => $err->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (is_null($res)) {
            return response()->json([
                'err' => true,
                'msg' => 'Pagamento não encontrado',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'err' => false,
            'msg' => 'Pagamento encontrado',
            'dados' => $res,
        ], Response::HTTP_OK);
    }
}
