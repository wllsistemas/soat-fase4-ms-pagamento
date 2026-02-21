<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Material;

use App\Domain\Entity\Material\RepositorioInterface;
use App\Domain\UseCase\Material\ReadUseCase;
use App\Infrastructure\Gateway\MaterialGateway;
use PHPUnit\Framework\TestCase;

class ReadUseCaseTest extends TestCase
{
    private MaterialGateway $gateway;
    private RepositorioInterface $repositorio;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new MaterialGateway($this->repositorio);
    }

    public function test_lista_materiais_com_sucesso(): void
    {
        $this->repositorio->method('listar')->willReturn([
            [
                'uuid' => 'uuid-1',
                'nome' => 'Material 1',
                'gtin' => '1111111111',
                'sku' => 'SKU-001',
                'descricao' => 'Desc 1',
                'preco' => 10.50,
                'saldo_atual' => 5,
                'disponivel' => 1,
                'criado_em' => '2024-01-01 10:00:00',
                'atualizado_em' => '2024-01-01 10:00:00',
            ],
            [
                'uuid' => 'uuid-2',
                'nome' => 'Material 2',
                'gtin' => '2222222222',
                'sku' => null,
                'descricao' => 'Desc 2',
                'preco' => 20.00,
                'saldo_atual' => 0,
                'disponivel' => 0,
                'criado_em' => '2024-01-02 10:00:00',
                'atualizado_em' => '2024-01-02 10:00:00',
            ],
        ]);

        $useCase = new ReadUseCase();
        $resultado = $useCase->exec($this->gateway);

        $this->assertCount(2, $resultado);
        $this->assertEquals('uuid-1', $resultado[0]['uuid']);
        $this->assertEquals('uuid-2', $resultado[1]['uuid']);
    }

    public function test_lista_vazia(): void
    {
        $this->repositorio->method('listar')->willReturn([]);

        $useCase = new ReadUseCase();
        $resultado = $useCase->exec($this->gateway);

        $this->assertCount(0, $resultado);
        $this->assertIsArray($resultado);
    }

    public function test_filtra_por_uuids(): void
    {
        $this->repositorio->method('listar')->willReturn([
            [
                'uuid' => 'uuid-1',
                'nome' => 'Material 1',
                'gtin' => '1111111111',
                'sku' => null,
                'descricao' => 'Desc',
                'preco' => 10.50,
                'saldo_atual' => 5,
                'disponivel' => 1,
                'criado_em' => '2024-01-01 10:00:00',
                'atualizado_em' => '2024-01-01 10:00:00',
            ],
        ]);

        $useCase = new ReadUseCase();
        $useCase->uuids = 'uuid-1,uuid-2';
        $resultado = $useCase->exec($this->gateway);

        $this->assertCount(1, $resultado);
    }

    public function test_retorno_contem_formato_http_response(): void
    {
        $this->repositorio->method('listar')->willReturn([
            [
                'uuid' => 'uuid-1',
                'nome' => 'Material 1',
                'gtin' => '1111111111',
                'sku' => 'SKU-001',
                'descricao' => 'Desc 1',
                'preco' => 10.50,
                'saldo_atual' => 5,
                'disponivel' => 1,
                'criado_em' => '2024-01-01 10:00:00',
                'atualizado_em' => '2024-01-01 10:00:00',
            ],
        ]);

        $useCase = new ReadUseCase();
        $resultado = $useCase->exec($this->gateway);

        $this->assertArrayHasKey('uuid', $resultado[0]);
        $this->assertArrayHasKey('nome', $resultado[0]);
        $this->assertArrayHasKey('gtin', $resultado[0]);
        $this->assertArrayHasKey('preco', $resultado[0]);
        $this->assertArrayHasKey('criado_em', $resultado[0]);
        $this->assertArrayHasKey('atualizado_em', $resultado[0]);
    }
}
