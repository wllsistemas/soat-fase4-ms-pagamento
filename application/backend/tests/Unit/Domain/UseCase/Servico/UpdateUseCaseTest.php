<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\Entity\Servico\RepositorioInterface;
use App\Domain\UseCase\Servico\UpdateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ServicoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UpdateUseCaseTest extends TestCase
{
    private ServicoGateway $gateway;
    private RepositorioInterface $repositorio;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new ServicoGateway($this->repositorio);
    }

    public function test_atualiza_servico_com_sucesso(): void
    {
        $entidadeExistente = new Entidade(
            uuid: 'uuid-456',
            nome: 'Servico Original',
            valor: 5000,
            criadoEm: new DateTimeImmutable('2024-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2024-01-01 10:00:00'),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidadeExistente);
        $this->repositorio->method('atualizar')->willReturn([
            'uuid' => 'uuid-456',
            'nome' => 'Servico Atualizado',
            'valor' => 7500,
            'criado_em' => '2024-01-01 10:00:00',
            'atualizado_em' => '2024-01-02 10:00:00',
            'deletado_em' => null,
        ]);

        $useCase = new UpdateUseCase($this->gateway);
        $resultado = $useCase->exec('uuid-456', 'Servico Atualizado', 7500);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('Servico Atualizado', $resultado->nome);
        $this->assertEquals(7500, $resultado->valor);
    }

    public function test_lanca_excecao_uuid_vazio(): void
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('identificador único não informado');

        $useCase = new UpdateUseCase($this->gateway);
        $useCase->exec('', 'Novo Nome', 5000);
    }

    public function test_lanca_excecao_servico_nao_encontrado(): void
    {
        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn(null);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado(a)');

        $useCase = new UpdateUseCase($this->gateway);
        $useCase->exec('uuid-inexistente', 'Novo Nome', 5000);
    }
}
