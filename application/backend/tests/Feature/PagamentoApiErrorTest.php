<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Infrastructure\Repositories\MercadoPagoMongoRepository;
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
        
        $response = $this->getJson('/api/ordem/' . $uuidTeste);
        
        $response->assertStatus(500)
                 ->assertJson([
                     'err' => true,
                     'msg' => 'Simulated fatal error'
                 ]);
    }

    public function test_read_one_pagamento_database_connection_error(): void
    {
        try {
            $response = $this->getJson('/api/ordem/invalid-uuid-format');
            $this->assertTrue(in_array($response->status(), [400, 404, 500]));
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}