<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Servico;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\Entity\Servico\RepositorioInterface;
use App\Domain\UseCase\Servico\CreateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\ServicoGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CreateUseCaseTest extends TestCase
{
    private ServicoGateway $gateway;
    private RepositorioInterface $repositorio;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new ServicoGateway($this->repositorio);
    }

    public function test_cria_servico_com_sucesso(): void
    {
        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn(null);
        $this->repositorio->method('criar')->willReturn([
            'uuid' => 'new-uuid',
            'nome' => 'Servico Novo',
            'valor' => 5000,
        ]);

        $useCase = new CreateUseCase(nome: 'Servico Novo', valor: 5000);
        $resultado = $useCase->exec($this->gateway);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('new-uuid', $resultado->uuid);
        $this->assertEquals('Servico Novo', $resultado->nome);
        $this->assertEquals(5000, $resultado->valor);
    }

    public function test_lanca_excecao_servico_duplicado(): void
    {
        $entidadeExistente = new Entidade(
            uuid: 'uuid-existente',
            nome: 'Servico Existente',
            valor: 3000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidadeExistente);

        $useCase = new CreateUseCase(nome: 'Servico Existente', valor: 5000);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Serviço já cadastrado');

        $useCase->exec($this->gateway);
    }
}
