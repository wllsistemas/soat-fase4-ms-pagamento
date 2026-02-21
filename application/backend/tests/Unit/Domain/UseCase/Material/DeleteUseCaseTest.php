<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\RepositorioInterface;
use App\Domain\UseCase\Material\DeleteUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DeleteUseCaseTest extends TestCase
{
    private MaterialGateway $gateway;
    private RepositorioInterface $repositorio;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new MaterialGateway($this->repositorio);
    }

    public function test_deleta_material_com_sucesso(): void
    {
        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            nome: 'Material Teste',
            gtin: '7891234567890',
            preco: 10.50,
            sku: null,
            descricao: 'Descricao',
            disponivel: 1,
            saldo_atual: 5,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidadeExistente);
        $this->repositorio->method('deletar')->willReturn(true);

        $useCase = new DeleteUseCase($this->gateway);
        $resultado = $useCase->exec('uuid-123');

        $this->assertTrue($resultado);
    }

    public function test_lanca_excecao_quando_material_nao_encontrado(): void
    {
        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn(null);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado com o identificador informado');

        $useCase = new DeleteUseCase($this->gateway);
        $useCase->exec('uuid-inexistente');
    }

    public function test_retorna_false_quando_deletar_falha(): void
    {
        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            nome: 'Material Teste',
            gtin: '7891234567890',
            preco: 10.50,
            sku: null,
            descricao: 'Descricao',
            disponivel: 1,
            saldo_atual: 5,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidadeExistente);
        $this->repositorio->method('deletar')->willReturn(false);

        $useCase = new DeleteUseCase($this->gateway);
        $resultado = $useCase->exec('uuid-123');

        $this->assertFalse($resultado);
    }
}
