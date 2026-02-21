<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Controller;

use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\RepositorioInterface as MaterialRepositorio;
use App\Domain\Entity\MaterialMovimentacoes\RepositorioInterface as EstoqueMovimentacoesRepositorio;
use App\Exception\DomainHttpException;
use App\Infrastructure\Controller\Material;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class MaterialControllerTest extends TestCase
{
    private MaterialRepositorio $materialRepo;
    private EstoqueMovimentacoesRepositorio $estoqueRepo;
    private Material $controller;

    protected function setUp(): void
    {
        $this->materialRepo = $this->createMock(MaterialRepositorio::class);
        $this->estoqueRepo = $this->createMock(EstoqueMovimentacoesRepositorio::class);
        $this->controller = new Material();
    }

    public function test_use_repositorio_retorna_self(): void
    {
        $result = $this->controller->useRepositorio($this->materialRepo);

        $this->assertSame($this->controller, $result);
    }

    public function test_use_estoque_movimentacoes_repository_retorna_self(): void
    {
        $result = $this->controller->useEstoqueMovimentacoesRepository($this->estoqueRepo);

        $this->assertSame($this->controller, $result);
    }

    public function test_criar_material_com_sucesso(): void
    {
        $this->materialRepo->method('encontrarPorIdentificadorUnico')->willReturn(null);
        $this->materialRepo->method('criar')->willReturn([
            'uuid' => 'new-uuid',
        ]);

        $resultado = $this->controller->useRepositorio($this->materialRepo)->criar(
            nome: 'Material Novo',
            gtin: '7891234567890',
            preco: 15.50,
            sku: 'SKU-001',
            descricao: 'Descricao teste',
            disponivel: 1,
            saldo_atual: 5,
        );

        $this->assertIsArray($resultado);
        $this->assertEquals('new-uuid', $resultado['uuid']);
    }

    public function test_listar_materiais(): void
    {
        $this->materialRepo->method('listar')->willReturn([
            [
                'uuid' => 'uuid-1',
                'nome' => 'Material 1',
                'gtin' => '111',
                'sku' => null,
                'descricao' => 'Desc',
                'preco' => 10.0,
                'saldo_atual' => 5,
                'disponivel' => 1,
                'criado_em' => '2024-01-01 10:00:00',
                'atualizado_em' => '2024-01-01 10:00:00',
            ],
        ]);

        $resultado = $this->controller->useRepositorio($this->materialRepo)->listar();

        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
    }

    public function test_obter_um_material(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Material',
            gtin: '111',
            preco: 10.0,
            sku: null,
            descricao: 'Desc',
            disponivel: 1,
            saldo_atual: 5,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->materialRepo->method('encontrarPorIdentificadorUnico')->willReturn($entidade);

        $resultado = $this->controller->useRepositorio($this->materialRepo)->obterUm('uuid-123');

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-123', $resultado['uuid']);
    }

    public function test_deletar_material(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Material',
            gtin: '111',
            preco: 10.0,
            sku: null,
            descricao: 'Desc',
            disponivel: 1,
            saldo_atual: 5,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->materialRepo->method('encontrarPorIdentificadorUnico')->willReturn($entidade);
        $this->materialRepo->method('deletar')->willReturn(true);

        $resultado = $this->controller->useRepositorio($this->materialRepo)->deletar('uuid-123');

        $this->assertTrue($resultado);
    }

    public function test_atualizar_material(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Material',
            gtin: '111',
            preco: 10.0,
            sku: null,
            descricao: 'Desc',
            disponivel: 1,
            saldo_atual: 5,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->materialRepo->method('encontrarPorIdentificadorUnico')->willReturn($entidade);
        $this->materialRepo->method('atualizar')->willReturn([
            'uuid' => 'uuid-123',
            'nome' => 'Material Atualizado',
            'gtin' => '111',
            'sku' => null,
            'descricao' => 'Desc',
            'preco' => 25.00,
            'disponivel' => 1,
            'saldo_atual' => 5,
            'criado_em' => '2024-01-01 10:00:00',
            'atualizado_em' => '2024-01-02 10:00:00',
            'deletado_em' => null,
        ]);

        $resultado = $this->controller->useRepositorio($this->materialRepo)->atualizar('uuid-123', ['nome' => 'Material Atualizado', 'preco' => 25.00]);

        $this->assertIsArray($resultado);
        $this->assertEquals('Material Atualizado', $resultado['nome']);
    }

    public function test_creditar_estoque(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Material',
            gtin: '111',
            preco: 10.0,
            sku: null,
            descricao: 'Desc',
            disponivel: 1,
            saldo_atual: 5,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->materialRepo->method('encontrarPorIdentificadorUnico')->willReturn($entidade);
        $this->estoqueRepo->method('criar')->willReturn([
            'uuid' => 'uuid-123',
            'nome' => 'Material',
            'gtin' => '111',
            'sku' => null,
            'descricao' => 'Desc',
            'preco' => 10.0,
            'disponivel' => 1,
            'saldo_atual' => 15,
            'criado_em' => '2024-01-01 10:00:00',
            'atualizado_em' => '2024-01-01 10:00:00',
        ]);

        $resultado = $this->controller
            ->useRepositorio($this->materialRepo)
            ->useEstoqueMovimentacoesRepository($this->estoqueRepo)
            ->creditar('uuid-123', 10);

        $this->assertIsArray($resultado);
        $this->assertEquals(15, $resultado['saldo_atual']);
    }

    public function test_debitar_estoque(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-123',
            nome: 'Material',
            gtin: '111',
            preco: 10.0,
            sku: null,
            descricao: 'Desc',
            disponivel: 1,
            saldo_atual: 10,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->materialRepo->method('encontrarPorIdentificadorUnico')->willReturn($entidade);
        $this->estoqueRepo->method('criar')->willReturn([
            'uuid' => 'uuid-123',
            'nome' => 'Material',
            'gtin' => '111',
            'sku' => null,
            'descricao' => 'Desc',
            'preco' => 10.0,
            'disponivel' => 1,
            'saldo_atual' => 5,
            'criado_em' => '2024-01-01 10:00:00',
            'atualizado_em' => '2024-01-01 10:00:00',
        ]);

        $resultado = $this->controller
            ->useRepositorio($this->materialRepo)
            ->useEstoqueMovimentacoesRepository($this->estoqueRepo)
            ->debitar('uuid-123', 5);

        $this->assertIsArray($resultado);
        $this->assertEquals(5, $resultado['saldo_atual']);
    }
}
