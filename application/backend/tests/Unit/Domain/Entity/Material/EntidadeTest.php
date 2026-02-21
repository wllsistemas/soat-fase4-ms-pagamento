<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Material;

use App\Domain\Entity\Material\Entidade;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EntidadeTest extends TestCase
{
    private function criarEntidadeValida(array $overrides = []): Entidade
    {
        $defaults = [
            'uuid' => 'uuid-123',
            'nome' => 'Material Teste',
            'gtin' => '7891234567890',
            'preco' => 10.50,
            'sku' => 'SKU-001',
            'descricao' => 'Descricao do material',
            'disponivel' => 1,
            'saldo_atual' => 10,
            'criadoEm' => new DateTimeImmutable('2024-01-01 10:00:00'),
            'atualizadoEm' => new DateTimeImmutable('2024-01-01 10:00:00'),
            'deletadoEm' => null,
        ];

        $params = array_merge($defaults, $overrides);

        return new Entidade(
            uuid: $params['uuid'],
            nome: $params['nome'],
            gtin: $params['gtin'],
            preco: $params['preco'],
            sku: $params['sku'],
            descricao: $params['descricao'],
            disponivel: $params['disponivel'],
            saldo_atual: $params['saldo_atual'],
            criadoEm: $params['criadoEm'],
            atualizadoEm: $params['atualizadoEm'],
            deletadoEm: $params['deletadoEm'],
        );
    }

    public function test_cria_entidade_com_dados_validos(): void
    {
        $entidade = $this->criarEntidadeValida();

        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('Material Teste', $entidade->nome);
        $this->assertEquals('7891234567890', $entidade->gtin);
        $this->assertEquals(10.50, $entidade->preco);
        $this->assertEquals('SKU-001', $entidade->sku);
        $this->assertEquals('Descricao do material', $entidade->descricao);
        $this->assertEquals(1, $entidade->disponivel);
        $this->assertEquals(10, $entidade->saldo_atual);
        $this->assertNull($entidade->deletadoEm);
    }

    public function test_valida_nome_com_menos_de_3_caracteres(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter pelo menos 3 caracteres');

        $this->criarEntidadeValida(['nome' => 'AB']);
    }

    public function test_valida_nome_vazio(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter pelo menos 3 caracteres');

        $this->criarEntidadeValida(['nome' => '']);
    }

    public function test_valida_nome_com_espacos_apenas(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Nome deve ter pelo menos 3 caracteres');

        $this->criarEntidadeValida(['nome' => '   ']);
    }

    public function test_valida_saldo_negativo(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Saldo atual deve ser maior ou igual a zero');

        $this->criarEntidadeValida(['saldo_atual' => -1]);
    }

    public function test_valida_saldo_zero_aceito(): void
    {
        $entidade = $this->criarEntidadeValida(['saldo_atual' => 0]);

        $this->assertEquals(0, $entidade->saldo_atual);
    }

    public function test_valida_gtin_vazio(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('GTIN não pode ser vazio');

        $this->criarEntidadeValida(['gtin' => '']);
    }

    public function test_valida_sku_vazio_quando_informado(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Quando informado, SKU não pode ser vazio');

        $this->criarEntidadeValida(['sku' => '']);
    }

    public function test_sku_null_aceito(): void
    {
        $entidade = $this->criarEntidadeValida(['sku' => null]);

        $this->assertNull($entidade->sku);
    }

    public function test_valida_descricao_acima_de_255_caracteres(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Descrição deve ter no máximo 255 caracteres');

        $this->criarEntidadeValida(['descricao' => str_repeat('a', 256)]);
    }

    public function test_descricao_com_255_caracteres_aceita(): void
    {
        $entidade = $this->criarEntidadeValida(['descricao' => str_repeat('a', 255)]);

        $this->assertEquals(255, strlen($entidade->descricao));
    }

    public function test_excluir_define_deletado_em(): void
    {
        $entidade = $this->criarEntidadeValida();

        $this->assertNull($entidade->deletadoEm);

        $entidade->excluir();

        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->deletadoEm);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->atualizadoEm);
    }

    public function test_esta_excluido_retorna_false_quando_nao_excluido(): void
    {
        $entidade = $this->criarEntidadeValida();

        $this->assertFalse($entidade->estaExcluido());
    }

    public function test_esta_excluido_retorna_true_quando_excluido(): void
    {
        $entidade = $this->criarEntidadeValida(['deletadoEm' => new DateTimeImmutable()]);

        $this->assertTrue($entidade->estaExcluido());
    }

    public function test_to_http_response_retorna_array_correto(): void
    {
        $criadoEm = new DateTimeImmutable('2024-01-15 14:30:00');
        $atualizadoEm = new DateTimeImmutable('2024-01-16 09:00:00');

        $entidade = $this->criarEntidadeValida([
            'criadoEm' => $criadoEm,
            'atualizadoEm' => $atualizadoEm,
        ]);

        $response = $entidade->toHttpResponse();

        $this->assertEquals('uuid-123', $response['uuid']);
        $this->assertEquals('Material Teste', $response['nome']);
        $this->assertEquals('7891234567890', $response['gtin']);
        $this->assertEquals('SKU-001', $response['sku']);
        $this->assertEquals('Descricao do material', $response['descricao']);
        $this->assertEquals(10.50, $response['preco']);
        $this->assertEquals(10, $response['saldo_atual']);
        $this->assertEquals(1, $response['disponivel']);
        $this->assertEquals('15/01/2024 14:30', $response['criado_em']);
        $this->assertEquals('16/01/2024 09:00', $response['atualizado_em']);
    }

    public function test_to_create_data_array(): void
    {
        $entidade = $this->criarEntidadeValida();
        $data = $entidade->toCreateDataArray();

        $this->assertEquals('Material Teste', $data['nome']);
        $this->assertEquals('7891234567890', $data['gtin']);
        $this->assertEquals('SKU-001', $data['sku']);
        $this->assertEquals(10, $data['saldo_atual']);
        $this->assertEquals('Descricao do material', $data['descricao']);
        $this->assertEquals(10.50, $data['preco']);
        $this->assertEquals(1, $data['disponivel']);
        $this->assertArrayNotHasKey('uuid', $data);
    }

    public function test_atualizar_muda_campos_informados(): void
    {
        $entidade = $this->criarEntidadeValida();

        $entidade->atualizar([
            'nome' => 'Novo Nome Material',
            'preco' => 25.99,
        ]);

        $this->assertEquals('Novo Nome Material', $entidade->nome);
        $this->assertEquals(25.99, $entidade->preco);
        $this->assertEquals('7891234567890', $entidade->gtin); // nao mudou
    }

    public function test_atualizar_valida_novos_dados(): void
    {
        $entidade = $this->criarEntidadeValida();

        $this->expectException(InvalidArgumentException::class);

        $entidade->atualizar(['nome' => 'AB']); // nome muito curto
    }

    public function test_atualizar_atualiza_timestamp(): void
    {
        $original = new DateTimeImmutable('2024-01-01 10:00:00');
        $entidade = $this->criarEntidadeValida(['atualizadoEm' => $original]);

        $entidade->atualizar(['nome' => 'Nome Atualizado']);

        $this->assertNotEquals($original, $entidade->atualizadoEm);
    }

    public function test_to_update_data_array(): void
    {
        $entidade = $this->criarEntidadeValida();
        $data = $entidade->toUpdateDataArray();

        $this->assertArrayHasKey('nome', $data);
        $this->assertArrayHasKey('gtin', $data);
        $this->assertArrayHasKey('sku', $data);
        $this->assertArrayHasKey('descricao', $data);
        $this->assertArrayHasKey('preco', $data);
        $this->assertArrayHasKey('disponivel', $data);
        $this->assertArrayHasKey('saldo_atual', $data);
        $this->assertArrayHasKey('atualizado_em', $data);
        $this->assertArrayNotHasKey('uuid', $data);
    }
}
