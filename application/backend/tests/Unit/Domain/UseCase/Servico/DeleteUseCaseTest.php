<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\Entity\Servico\RepositorioInterface;
use App\Domain\UseCase\Servico\DeleteUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ServicoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DeleteUseCaseTest extends TestCase
{
    private ServicoGateway $gateway;
    private RepositorioInterface $repositorio;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new ServicoGateway($this->repositorio);
    }

    public function test_deleta_servico_com_sucesso(): void
    {
        $entidadeExistente = new Entidade(
            uuid: 'uuid-456',
            nome: 'Servico Teste',
            valor: 5000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidadeExistente);
        $this->repositorio->method('deletar')->willReturn(true);

        $useCase = new DeleteUseCase($this->gateway);
        $resultado = $useCase->exec('uuid-456');

        $this->assertTrue($resultado);
    }

    public function test_lanca_excecao_servico_nao_encontrado(): void
    {
        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn(null);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Serviço não encontrado');

        $useCase = new DeleteUseCase($this->gateway);
        $useCase->exec('uuid-inexistente');
    }

    public function test_retorna_false_quando_deletar_falha(): void
    {
        $entidadeExistente = new Entidade(
            uuid: 'uuid-456',
            nome: 'Servico Teste',
            valor: 5000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidadeExistente);
        $this->repositorio->method('deletar')->willReturn(false);

        $useCase = new DeleteUseCase($this->gateway);
        $resultado = $useCase->exec('uuid-456');

        $this->assertFalse($resultado);
    }
}
