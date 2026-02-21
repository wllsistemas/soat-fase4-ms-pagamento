<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Servico;

use App\Domain\Entity\Servico\RepositorioInterface;
use App\Domain\UseCase\Servico\ReadUseCase;
use App\Infrastructure\Gateway\ServicoGateway;
use PHPUnit\Framework\TestCase;

class ReadUseCaseTest extends TestCase
{
    private ServicoGateway $gateway;
    private RepositorioInterface $repositorio;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new ServicoGateway($this->repositorio);
    }

    public function test_lista_servicos_com_sucesso(): void
    {
        $this->repositorio->method('listar')->willReturn([
            [
                'uuid' => 'uuid-1',
                'nome' => 'Servico 1',
                'valor' => 5000,
                'criado_em' => '2024-01-01 10:00:00',
                'atualizado_em' => '2024-01-01 10:00:00',
            ],
            [
                'uuid' => 'uuid-2',
                'nome' => 'Servico 2',
                'valor' => 8000,
                'criado_em' => '2024-01-02 10:00:00',
                'atualizado_em' => '2024-01-02 10:00:00',
            ],
        ]);

        $useCase = new ReadUseCase();
        $resultado = $useCase->exec($this->gateway);

        $this->assertCount(2, $resultado);
        $this->assertEquals('uuid-1', $resultado[0]['uuid']);
        $this->assertEquals('uuid-2', $resultado[1]['uuid']);
        $this->assertEquals(50.00, $resultado[0]['valor']); // 5000 / 100
        $this->assertEquals(80.00, $resultado[1]['valor']); // 8000 / 100
    }

    public function test_lista_vazia(): void
    {
        $this->repositorio->method('listar')->willReturn([]);

        $useCase = new ReadUseCase();
        $resultado = $useCase->exec($this->gateway);

        $this->assertCount(0, $resultado);
        $this->assertIsArray($resultado);
    }
}
