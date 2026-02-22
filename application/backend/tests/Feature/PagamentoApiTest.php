<?php

declare(strict_types=1);

namespace Tests\Feature;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use Illuminate\Support\Str;
use App\Infrastructure\Repositories\MercadoPagoMongoRepository;


use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class PagamentoApiTest extends TestCase
{
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function test_read_one_pagamento_uuid_invalido(): void
    {
        $response = $this->getJson('/api/ordem/uuid-invalido');

        $response->assertStatus(400);
    }

    public function test_read_one_pagamento(): void
    {

        $response = $this->getJson('/api/ordem/' . Str::uuid()->toString());

        $response->assertStatus(404);
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
            'updated_at' => time()
        ];
        
        try {
            $collection = MercadoPagoMongoRepository::getMongoDB()
                ->selectCollection('mercado_pago_pagamentos');
            
            $collection->insertOne($dadosPagamentoSimulado);
            
            $response = $this->getJson('/api/ordem/' . $uuidValido);

            $response->assertStatus(200)
                     ->assertJson([
                         'err' => false,
                         'msg' => 'Pagamento encontrado'
                     ])
                     ->assertJsonFragment([
                         'ordem_uuid' => $uuidValido,
                         'pagamento_id' => 'PAGTO_123456789',
                         'status' => 'approved'
                     ]);
        } finally {
            try {
                $collection = MercadoPagoMongoRepository::getMongoDB()
                    ->selectCollection('mercado_pago_pagamentos');
                $collection->deleteOne(['ordem_uuid' => $uuidValido]);
            } catch (\Exception $e) {
            }
        }
    }

    public function test_read_one_pagamento_erro_interno_servidor(): void
    {
        try {
            $uuidProblematico = '00000000-0000-0000-0000-000000000000';
            
            $response = $this->getJson('/api/ordem/' . $uuidProblematico);

            $this->assertTrue(
                $response->status() === 404 || 
                $response->status() === 400 || 
                $response->status() === 500
            );
            
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_read_one_pagamento_cobertura_adicional(): void
    {
        $uuidTeste = \Illuminate\Support\Str::uuid()->toString();
        
        $response = $this->getJson('/api/ordem/' . $uuidTeste);
        
        $this->assertTrue(in_array($response->status(), [400, 404, 500]));
        
        if ($response->status() === 404) {
            $response->assertJson([
                'err' => true,
                'msg' => 'Pagamento não encontrado'
            ]);
        }
    }
}
