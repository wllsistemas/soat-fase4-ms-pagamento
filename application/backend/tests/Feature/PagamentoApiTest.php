<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use Illuminate\Support\Str;
use App\Infrastructure\Repositories\MercadoPagoMongoRepository;
use Tests\TestCase;
use Mockery;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class PagamentoApiTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function mockMongoChain(mixed $findOneReturn): void
    {
        $collectionMock = Mockery::mock(\MongoDB\Collection::class);
        $collectionMock->shouldReceive('findOne')
            ->andReturn($findOneReturn);

        $dbMock = Mockery::mock(\MongoDB\Database::class);
        $dbMock->shouldReceive('selectCollection')
            ->with('mercado_pago_pagamentos')
            ->andReturn($collectionMock);

        $repoMock = Mockery::mock('overload:' . MercadoPagoMongoRepository::class);
        $repoMock->shouldReceive('getMongoDB')
            ->andReturn($dbMock);
    }

    public function test_read_one_pagamento_uuid_invalido(): void
    {
        $response = $this->getJson('/api/ordem/uuid-invalido');

        $response->assertStatus(400);
    }

    public function test_read_one_pagamento(): void
    {
        $this->mockMongoChain(null);

        $response = $this->getJson('/api/ordem/' . Str::uuid()->toString());

        $response->assertStatus(404)
            ->assertJson([
                'err' => true,
                'msg' => 'Pagamento não encontrado',
            ]);
    }

    public function test_read_one_pagamento_valido_com_mock(): void
    {
        $uuidValido = Str::uuid()->toString();

        $dadosPagamentoSimulado = [
            '_id' => '507f1f77bcf86cd799439011',
            'ordem_uuid' => $uuidValido,
            'pagamento_id' => 'PAGTO_123456789',
            'status' => 'approved',
            'transaction_amount' => 50.0,
            'payment_method_id' => 'pix',
            'qr_code' => 'data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
            'copia_cola' => '00020101021243650016COM.MERCADOLIBRE02013063638f1232fc-5f1d-4130-8b3e-b4',
            'created_at' => time(),
            'updated_at' => time(),
        ];

        $this->mockMongoChain($dadosPagamentoSimulado);

        $response = $this->getJson('/api/ordem/' . $uuidValido);

        $response->assertStatus(200)
            ->assertJson([
                'err' => false,
                'msg' => 'Pagamento encontrado',
            ])
            ->assertJsonFragment([
                'ordem_uuid' => $uuidValido,
                'pagamento_id' => 'PAGTO_123456789',
                'status' => 'approved',
            ]);
    }

    public function test_read_one_pagamento_erro_interno_servidor(): void
    {
        $repoMock = Mockery::mock('overload:' . MercadoPagoMongoRepository::class);
        $repoMock->shouldReceive('getMongoDB')
            ->andThrow(new \RuntimeException('Erro de conexão simulado'));

        $response = $this->getJson('/api/ordem/' . Str::uuid()->toString());

        $response->assertStatus(500)
            ->assertJson([
                'err' => true,
                'msg' => 'Erro de conexão simulado',
            ]);
    }

    public function test_read_one_pagamento_cobertura_adicional(): void
    {
        $this->mockMongoChain(null);

        $response = $this->getJson('/api/ordem/' . Str::uuid()->toString());

        $response->assertStatus(404)
            ->assertJson([
                'err' => true,
                'msg' => 'Pagamento não encontrado',
            ]);
    }
}
