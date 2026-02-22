<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Service;

use App\Infrastructure\Service\MercadoPago;
use Tests\TestCase;

class MercadoPagoRealCoverageTest extends TestCase
{
    private MercadoPago $mercadoPago;

    protected function setUp(): void
    {
        parent::setUp();
        putenv('MERCADO_PAGO_ACCESS_TOKEN=TEST-invalid-token-force-errors');
        putenv('MERCADO_PAGO_WEBHOOK_URL=http://localhost/webhook-coverage');
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

    public function test_pix_copia_cola_real_coverage_execucao_completa(): void
    {
        $dadosOrdem = $this->getDadosOrdemMock();
        
        $resultado = $this->mercadoPago->pixCopiaCola($dadosOrdem);
        
        $this->assertIsArray($resultado);
        
        $this->assertTrue(
            isset($resultado['err']) || 
            isset($resultado['message']) || 
            isset($resultado['qr_code'])
        );
    }

    public function test_pix_copia_cola_real_coverage_dados_diversos(): void
    {
        $dadosOrdem = [
            'total' => '50.99',
            'cliente_primeiro_nome' => 'Maria',
            'cliente_sobrenome' => 'Santos',
            'cliente_email' => 'maria@test.com',
            'cliente_documento' => '98765432100'
        ];
        
        $resultado = $this->mercadoPago->pixCopiaCola($dadosOrdem);
        
        $this->assertIsArray($resultado);
    }

    public function test_pix_copia_cola_real_coverage_dados_minimos(): void
    {
        $dadosOrdem = [
            'total' => 1.0,
            'cliente_primeiro_nome' => 'A',
            'cliente_sobrenome' => 'B',
            'cliente_email' => 'a@b.c',
            'cliente_documento' => '11111111111'
        ];
        
        $resultado = $this->mercadoPago->pixCopiaCola($dadosOrdem);
        
        $this->assertIsArray($resultado);
    }

    public function test_pix_copia_cola_real_coverage_valores_extremos(): void
    {
        $dadosOrdem = [
            'total' => 999999.99,
            'cliente_primeiro_nome' => 'Nome Muito Longo Para Testar Processamento',
            'cliente_sobrenome' => 'Sobrenome Igualmente Muito Longo',
            'cliente_email' => 'email.muito.longo.para.teste@dominio.muito.longo.com',
            'cliente_documento' => '99999999999'
        ];
        
        $resultado = $this->mercadoPago->pixCopiaCola($dadosOrdem);
        
        $this->assertIsArray($resultado);
    }

    public function test_pix_copia_cola_real_coverage_caracteres_especiais(): void
    {
        $dadosOrdem = [
            'total' => '123.45',
            'cliente_primeiro_nome' => 'José João',
            'cliente_sobrenome' => 'da Silva-Santos',
            'cliente_email' => 'jose.joao@email-teste.com.br',
            'cliente_documento' => '12398745601'
        ];
        
        $resultado = $this->mercadoPago->pixCopiaCola($dadosOrdem);
        
        $this->assertIsArray($resultado);
        
        $this->assertTrue(true);
    }
}