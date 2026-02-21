<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Servico;

use App\Domain\Entity\Servico\Entidade;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EntidadeTest extends TestCase
{
    private function criarEntidadeValida(array $overrides = []): Entidade
    {
        $defaults = [
            'uuid' => 'uuid-456',
            'nome' => 'Servico Teste',
            'valor' => 5000,
            'criadoEm' => new DateTimeImmutable('2024-01-01 10:00:00'),
            'atualizadoEm' => new DateTimeImmutable('2024-01-01 10:00:00'),
            'deletadoEm' => null,
        ];

        $params = array_merge($defaults, $overrides);

        return new Entidade(
            uuid: $params['uuid'],
            nome: $params['nome'],
            valor: $params['valor'],
            criadoEm: $params['criadoEm'],
            atualizadoEm: $params['atualizadoEm'],
            deletadoEm: $params['deletadoEm'],
        );
    }

    public function test_cria_entidade_com_dados_validos(): void
    {
        $entidade = $this->criarEntidadeValida();

        $this->assertEquals('uuid-456', $entidade->uuid);
        $this->assertEquals('Servico Teste', $entidade->nome);
        $this->assertEquals(5000, $entidade->valor);
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

    public function test_valida_valor_negativo(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Valor deve ser maior ou igual a zero');

        $this->criarEntidadeValida(['valor' => -1]);
    }

    public function test_valor_zero_aceito(): void
    {
        $entidade = $this->criarEntidadeValida(['valor' => 0]);

        $this->assertEquals(0, $entidade->valor);
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
        $criadoEm = new DateTimeImmutable('2024-03-20 08:00:00');
        $atualizadoEm = new DateTimeImmutable('2024-03-21 15:30:00');

        $entidade = $this->criarEntidadeValida([
            'valor' => 15000,
            'criadoEm' => $criadoEm,
            'atualizadoEm' => $atualizadoEm,
        ]);

        $response = $entidade->toHttpResponse();

        $this->assertEquals('uuid-456', $response['uuid']);
        $this->assertEquals('Servico Teste', $response['nome']);
        $this->assertEquals(150.00, $response['valor']); // 15000 / 100
        $this->assertEquals('20/03/2024 08:00', $response['criado_em']);
        $this->assertEquals('21/03/2024 15:30', $response['atualizado_em']);
    }

    public function test_to_create_data_array(): void
    {
        $entidade = $this->criarEntidadeValida();
        $data = $entidade->toCreateDataArray();

        $this->assertEquals('Servico Teste', $data['nome']);
        $this->assertEquals(5000, $data['valor']);
        $this->assertArrayNotHasKey('uuid', $data);
        $this->assertCount(2, $data);
    }
}
