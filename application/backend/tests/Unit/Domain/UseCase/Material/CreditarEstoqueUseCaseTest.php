<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\RepositorioInterface as MaterialRepositorio;
use App\Domain\Entity\MaterialMovimentacoes\RepositorioInterface as MovimentacoesRepositorio;
use App\Domain\UseCase\Material\CreditarEstoqueUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialMovimentacoesGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CreditarEstoqueUseCaseTest extends TestCase
{
    private MaterialMovimentacoesGateway $gateway;
    private MovimentacoesRepositorio $movimentacoesRepo;
    private MaterialRepositorio $materialRepo;

    protected function setUp(): void
    {
        $this->movimentacoesRepo = $this->createMock(MovimentacoesRepositorio::class);
        $this->materialRepo = $this->createMock(MaterialRepositorio::class);
        $this->gateway = new MaterialMovimentacoesGateway($this->movimentacoesRepo, $this->materialRepo);
    }

    public function test_credita_estoque_com_sucesso(): void
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

        $this->materialRepo->method('encontrarPorIdentificadorUnico')->willReturn($entidadeExistente);
        $this->movimentacoesRepo->method('criar')->willReturn([
            'uuid' => 'uuid-123',
            'nome' => 'Material Teste',
            'gtin' => '7891234567890',
            'sku' => null,
            'descricao' => 'Descricao',
            'preco' => 10.50,
            'disponivel' => 1,
            'saldo_atual' => 15,
            'criado_em' => '2024-01-01 10:00:00',
            'atualizado_em' => '2024-01-01 10:00:00',
        ]);

        $useCase = new CreditarEstoqueUseCase($this->gateway);
        $resultado = $useCase->exec('uuid-123', 10, 'Entrada de mercadoria');

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals(15, $resultado->saldo_atual);
    }

    public function test_lanca_excecao_uuid_vazio(): void
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('identificador único do produto não informado');

        $useCase = new CreditarEstoqueUseCase($this->gateway);
        $useCase->exec('', 10);
    }

    public function test_lanca_excecao_produto_nao_encontrado(): void
    {
        $this->materialRepo->method('encontrarPorIdentificadorUnico')->willReturn(null);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Produto não encontrado(a)');

        $useCase = new CreditarEstoqueUseCase($this->gateway);
        $useCase->exec('uuid-inexistente', 10);
    }

    public function test_credita_sem_observacoes(): void
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

        $this->materialRepo->method('encontrarPorIdentificadorUnico')->willReturn($entidadeExistente);
        $this->movimentacoesRepo->method('criar')->willReturn([
            'uuid' => 'uuid-123',
            'nome' => 'Material Teste',
            'gtin' => '7891234567890',
            'sku' => null,
            'descricao' => 'Descricao',
            'preco' => 10.50,
            'disponivel' => 1,
            'saldo_atual' => 15,
            'criado_em' => '2024-01-01 10:00:00',
            'atualizado_em' => '2024-01-01 10:00:00',
        ]);

        $useCase = new CreditarEstoqueUseCase($this->gateway);
        $resultado = $useCase->exec('uuid-123', 10);

        $this->assertInstanceOf(Entidade::class, $resultado);
    }
}
