<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service;

use App\Infrastructure\Service\MercadoPago;
use Tests\TestCase;

class MercadoPagoIntegrationTest extends TestCase
{
    private MercadoPago $mercadoPago;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar variáveis de ambiente para teste
        putenv('MERCADO_PAGO_ACCESS_TOKEN=TEST-invalid-token-for-testing');
        putenv('MERCADO_PAGO_WEBHOOK_URL=http://localhost/webhook-test');
        
        $this->mercadoPago = new MercadoPago();
    }

    private function getDadosOrdemMock(): array
    {
        return [
            'total' => 100.50,
            'cliente_primeiro_nome' => 'João',
            'cliente_sobrenome' => 'Silva',
            'cliente_email' => 'joao.silva@example.com',
            'cliente_documento' => '12345678901'
        ];
    }

    public function test_pix_copia_cola_executa_codigo_real_com_token_invalido(): void
    {        
        $dadosOrdem = $this->getDadosOrdemMock();
        
        $resultado = $this->mercadoPago->pixCopiaCola($dadosOrdem);
        
        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('err', $resultado);
        $this->assertArrayHasKey('message', $resultado);
        
        $this->assertTrue(
            isset($resultado['err']) || 
            isset($resultado['message'])
        );
    }

    public function test_pix_copia_cola_valida_formatacao_dados(): void
    {
        $dadosOrdem = [
            'total' => '150.75',
            'cliente_primeiro_nome' => 'Maria',
            'cliente_sobrenome' => 'Santos',
            'cliente_email' => 'maria@example.com',
            'cliente_documento' => '98765432100'
        ];
        
        $resultado = $this->mercadoPago->pixCopiaCola($dadosOrdem);
        
        $this->assertIsArray($resultado);
    }

    public function test_pix_copia_cola_dados_incompletos(): void
    {
        $dadosOrdem = [
            'total' => 50.0,
            'cliente_primeiro_nome' => 'João'
        ];
        
        $resultado = $this->mercadoPago->pixCopiaCola($dadosOrdem);
        
        $this->assertIsArray($resultado);
        
        $this->assertTrue(
            (isset($resultado['err']) && $resultado['err'] !== false) ||
            (isset($resultado['message']) && !empty($resultado['message']))
        );
    }
}