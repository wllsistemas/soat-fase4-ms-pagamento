<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Domain\Entity\Material\RepositorioInterface as MaterialRepositorio;
use App\Domain\Entity\MaterialMovimentacoes\RepositorioInterface as MovimentacoesRepositorio;
use App\Infrastructure\Gateway\MaterialMovimentacoesGateway;
use PHPUnit\Framework\TestCase;

class MaterialMovimentacoesGatewayTest extends TestCase
{
    private MovimentacoesRepositorio $movimentacoesRepo;
    private MaterialRepositorio $materialRepo;
    private MaterialMovimentacoesGateway $gateway;

    protected function setUp(): void
    {
        $this->movimentacoesRepo = $this->createMock(MovimentacoesRepositorio::class);
        $this->materialRepo = $this->createMock(MaterialRepositorio::class);
        $this->gateway = new MaterialMovimentacoesGateway($this->movimentacoesRepo, $this->materialRepo);
    }

    public function test_criar_delega_ao_repositorio_com_dados_corretos(): void
    {
        $this->movimentacoesRepo->expects($this->once())
            ->method('criar')
            ->with([
                'produto_uuid' => 'uuid-123',
                'quantidade' => 10,
                'observacoes' => 'Entrada de estoque',
                'tipo' => 'credito',
            ])
            ->willReturn([
                'uuid' => 'uuid-123',
                'saldo_atual' => 15,
            ]);

        $resultado = $this->gateway->criar('uuid-123', 10, 'Entrada de estoque', 'credito');

        $this->assertEquals('uuid-123', $resultado['uuid']);
    }

    public function test_criar_com_observacoes_null(): void
    {
        $this->movimentacoesRepo->expects($this->once())
            ->method('criar')
            ->with([
                'produto_uuid' => 'uuid-123',
                'quantidade' => 5,
                'observacoes' => null,
                'tipo' => 'debito',
            ])
            ->willReturn(['uuid' => 'uuid-123']);

        $resultado = $this->gateway->criar('uuid-123', 5, null, 'debito');

        $this->assertIsArray($resultado);
    }

    public function test_listar_delega_ao_repositorio(): void
    {
        $this->movimentacoesRepo->expects($this->once())
            ->method('listar')
            ->with(['*'])
            ->willReturn([['uuid' => '1'], ['uuid' => '2']]);

        $resultado = $this->gateway->listar();

        $this->assertCount(2, $resultado);
    }

    public function test_repositorios_acessiveis_como_propriedades(): void
    {
        $this->assertSame($this->movimentacoesRepo, $this->gateway->repositorio);
        $this->assertSame($this->materialRepo, $this->gateway->materialRepositorio);
    }
}
