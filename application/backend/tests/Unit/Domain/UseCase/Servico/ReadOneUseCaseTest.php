<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\Entity\Servico\RepositorioInterface;
use App\Domain\UseCase\Servico\ReadOneUseCase;
use App\Infrastructure\Gateway\ServicoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ReadOneUseCaseTest extends TestCase
{
    private ServicoGateway $gateway;
    private RepositorioInterface $repositorio;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new ServicoGateway($this->repositorio);
    }

    public function test_retorna_servico_encontrado(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-456',
            nome: 'Servico Teste',
            valor: 5000,
            criadoEm: new DateTimeImmutable('2024-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2024-01-01 10:00:00'),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidade);

        $useCase = new ReadOneUseCase('uuid-456');
        $resultado = $useCase->exec($this->gateway);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-456', $resultado['uuid']);
        $this->assertEquals('Servico Teste', $resultado['nome']);
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
