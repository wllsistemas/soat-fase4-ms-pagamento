<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\MaterialMovimentacoes;

use App\Domain\Entity\MaterialMovimentacoes\Entidade;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class EntidadeTest extends TestCase
{
    private function criarEntidadeValida(array $overrides = []): Entidade
    {
        $defaults = [
            'uuid' => 'mov-uuid-001',
            'tipo' => 'credito',
            'quantidade' => 10,
            'descricao' => 'Entrada de estoque',
            'criadoEm' => new DateTimeImmutable('2024-01-01 10:00:00'),
            'atualizadoEm' => new DateTimeImmutable('2024-01-01 10:00:00'),
            'deletadoEm' => null,
            'materialUuid' => null,
        ];

        $params = array_merge($defaults, $overrides);

        return new Entidade(
            uuid: $params['uuid'],
            tipo: $params['tipo'],
            quantidade: $params['quantidade'],
            descricao: $params['descricao'],
            criadoEm: $params['criadoEm'],
            atualizadoEm: $params['atualizadoEm'],
            deletadoEm: $params['deletadoEm'],
            materialUuid: $params['materialUuid'],
        );
    }

    public function test_cria_entidade_com_dados_validos(): void
    {
        $entidade = $this->criarEntidadeValida();

        $this->assertEquals('mov-uuid-001', $entidade->uuid);
        $this->assertEquals('credito', $entidade->tipo);
        $this->assertEquals(10, $entidade->quantidade);
        $this->assertEquals('Entrada de estoque', $entidade->descricao);
        $this->assertNull($entidade->deletadoEm);
        $this->assertNull($entidade->materialUuid);
    }

    public function test_cria_entidade_tipo_debito(): void
    {
        $entidade = $this->criarEntidadeValida(['tipo' => 'debito']);

        $this->assertEquals('debito', $entidade->tipo);
    }

    public function test_excluir_define_deletado_em(): void
    {
        $entidade = $this->criarEntidadeValida();

        $this->assertNull($entidade->deletadoEm);

        $entidade->excluir();

        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->deletadoEm);
        $this->assertInstanceOf(DateTimeImmutable::class, $entidade->atualizadoEm);
    }

    public function test_esta_excluido_retorna_false(): void
    {
        $entidade = $this->criarEntidadeValida();

        $this->assertFalse($entidade->estaExcluido());
    }

    public function test_esta_excluido_retorna_true(): void
    {
        $entidade = $this->criarEntidadeValida(['deletadoEm' => new DateTimeImmutable()]);

        $this->assertTrue($entidade->estaExcluido());
    }

    public function test_to_http_response(): void
    {
        $criadoEm = new DateTimeImmutable('2024-06-15 12:00:00');
        $atualizadoEm = new DateTimeImmutable('2024-06-16 14:00:00');

        $entidade = $this->criarEntidadeValida([
            'criadoEm' => $criadoEm,
            'atualizadoEm' => $atualizadoEm,
        ]);

        $response = $entidade->toHttpResponse();

        $this->assertEquals('mov-uuid-001', $response['uuid']);
        $this->assertEquals('Entrada de estoque', $response['descricao']);
        $this->assertEquals('15/06/2024 12:00', $response['criado_em']);
        $this->assertEquals('16/06/2024 14:00', $response['atualizado_em']);
    }

    public function test_to_create_data_array(): void
    {
        $entidade = $this->criarEntidadeValida();
        $data = $entidade->toCreateDataArray();

        $this->assertEquals('Entrada de estoque', $data['descricao']);
        $this->assertCount(1, $data);
    }

    public function test_atualizar_muda_descricao(): void
    {
        $entidade = $this->criarEntidadeValida();
        $originalAtualizadoEm = $entidade->atualizadoEm;

        $entidade->atualizar(['descricao' => 'Nova descricao']);

        $this->assertEquals('Nova descricao', $entidade->descricao);
        $this->assertNotEquals($originalAtualizadoEm, $entidade->atualizadoEm);
    }

    public function test_atualizar_sem_descricao_nao_altera(): void
    {
        $entidade = $this->criarEntidadeValida();
        $descricaoOriginal = $entidade->descricao;

        $entidade->atualizar([]);

        $this->assertEquals($descricaoOriginal, $entidade->descricao);
    }

    public function test_to_update_data_array(): void
    {
        $entidade = $this->criarEntidadeValida();
        $data = $entidade->toUpdateDataArray();

        $this->assertArrayHasKey('descricao', $data);
        $this->assertCount(1, $data);
    }
}
