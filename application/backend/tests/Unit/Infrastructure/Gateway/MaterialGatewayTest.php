<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\RepositorioInterface;
use App\Infrastructure\Gateway\MaterialGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class MaterialGatewayTest extends TestCase
{
    private RepositorioInterface $repositorio;
    private MaterialGateway $gateway;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new MaterialGateway($this->repositorio);
    }

    public function test_encontrar_por_identificador_unico_delega_ao_repositorio(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Material Teste',
            gtin: '7891234567890',
            preco: 10.50,
            sku: null,
            descricao: 'Desc',
            disponivel: 1,
            saldo_atual: 5,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->expects($this->once())
            ->method('encontrarPorIdentificadorUnico')
            ->with('uuid-123', 'uuid')
            ->willReturn($entidade);

        $resultado = $this->gateway->encontrarPorIdentificadorUnico('uuid-123', 'uuid');

        $this->assertSame($entidade, $resultado);
    }

    public function test_encontrar_retorna_null_quando_nao_existe(): void
    {
        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn(null);

        $resultado = $this->gateway->encontrarPorIdentificadorUnico('inexistente', 'uuid');

        $this->assertNull($resultado);
    }

    public function test_criar_delega_ao_repositorio(): void
    {
        $dados = ['nome' => 'Material', 'gtin' => '123'];

        $this->repositorio->expects($this->once())
            ->method('criar')
            ->with($dados)
            ->willReturn(['uuid' => 'new-uuid', ...$dados]);

        $resultado = $this->gateway->criar($dados);

        $this->assertEquals('new-uuid', $resultado['uuid']);
    }

    public function test_listar_delega_ao_repositorio(): void
    {
        $this->repositorio->expects($this->once())
            ->method('listar')
            ->willReturn([['uuid' => '1'], ['uuid' => '2']]);

        $resultado = $this->gateway->listar();

        $this->assertCount(2, $resultado);
    }

    public function test_listar_com_filtros(): void
    {
        $filtros = ['uuids' => 'uuid-1,uuid-2'];

        $this->repositorio->expects($this->once())
            ->method('listar')
            ->with(['*'], $filtros)
            ->willReturn([['uuid' => 'uuid-1']]);

        $resultado = $this->gateway->listar($filtros);

        $this->assertCount(1, $resultado);
    }

    public function test_deletar_delega_ao_repositorio(): void
    {
        $this->repositorio->expects($this->once())
            ->method('deletar')
            ->with('uuid-123')
            ->willReturn(true);

        $resultado = $this->gateway->deletar('uuid-123');

        $this->assertTrue($resultado);
    }

    public function test_atualizar_delega_ao_repositorio(): void
    {
        $novosDados = ['nome' => 'Novo Nome'];

        $this->repositorio->expects($this->once())
            ->method('atualizar')
            ->with('uuid-123', $novosDados)
            ->willReturn(['uuid' => 'uuid-123', 'nome' => 'Novo Nome']);

        $resultado = $this->gateway->atualizar('uuid-123', $novosDados);

        $this->assertEquals('Novo Nome', $resultado['nome']);
    }
}
