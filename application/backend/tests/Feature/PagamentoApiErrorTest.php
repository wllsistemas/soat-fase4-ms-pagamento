<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Infrastructure\Repositories\MercadoPagoMongoRepository;
use Illuminate\Support\Facades\Http;
use Mockery;

class PagamentoApiErrorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_read_one_pagamento_throwable_error(): void
    {
        $mongoRepositoryMock = Mockery::mock('overload:' . MercadoPagoMongoRepository::class);
        $mongoRepositoryMock->shouldReceive('getMongoDB')
            ->andThrow(new \Error('Simulated fatal error'));

        $uuidTeste = \Illuminate\Support\Str::uuid()->toString();

        $response = $this->get('/api/ordem/' . $uuidTeste);

        $response->assertStatus(500)
            ->assertJson([
                'err' => true,
                'msg' => 'Simulated fatal error'
            ]);
    }
}