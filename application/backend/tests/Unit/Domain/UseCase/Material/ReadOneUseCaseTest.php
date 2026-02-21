<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\RepositorioInterface;
use App\Domain\UseCase\Material\ReadOneUseCase;
use App\Infrastructure\Gateway\MaterialGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ReadOneUseCaseTest extends TestCase
{
    private MaterialGateway $gateway;
    private RepositorioInterface $repositorio;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new MaterialGateway($this->repositorio);
    }

    public function test_retorna_material_encontrado(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Material Teste',
            gtin: '7891234567890',
            preco: 10.50,
            sku: 'SKU-001',
            descricao: 'Descricao',
            disponivel: 1,
            saldo_atual: 5,
            criadoEm: new DateTimeImmutable('2024-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2024-01-01 10:00:00'),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidade);

        $useCase = new ReadOneUseCase('uuid-123');
        $resultado = $useCase->exec($this->gateway);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
        $this->assertEquals('Material Teste', $resultado['nome']);
    }

    public function test_retorna_null_quando_nao_encontrado(): void
    {
        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn(null);

        $useCase = new ReadOneUseCase('uuid-inexistente');
        $resultado = $useCase->exec($this->gateway);

        $this->assertNull($resultado);
    }

    public function test_retorna_null_quando_uuid_vazio(): void
    {
        $useCase = new ReadOneUseCase('');
        $resultado = $useCase->exec($this->gateway);

        $this->assertNull($resultado);
    }
}
