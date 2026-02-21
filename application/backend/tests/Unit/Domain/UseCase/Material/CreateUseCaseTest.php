<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\RepositorioInterface;
use App\Domain\UseCase\Material\CreateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class CreateUseCaseTest extends TestCase
{
    private MaterialGateway $gateway;
    private RepositorioInterface $repositorio;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new MaterialGateway($this->repositorio);
    }

    public function test_cria_material_com_sucesso(): void
    {
        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn(null);
        $this->repositorio->method('criar')->willReturn([
            'uuid' => 'new-uuid',
            'nome' => 'Material Novo',
        ]);

        $useCase = new CreateUseCase(
            nome: 'Material Novo',
            gtin: '7891234567890',
            preco: 15.50,
            sku: 'SKU-001',
            descricao: 'Descricao teste',
            disponivel: 1,
            saldo_atual: 5,
        );

        $resultado = $useCase->exec($this->gateway);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('new-uuid', $resultado->uuid);
        $this->assertEquals('Material Novo', $resultado->nome);
    }

    public function test_lanca_excecao_nome_repetido(): void
    {
        $entidadeExistente = new Entidade(
            uuid: 'uuid-existente',
            nome: 'Material Existente',
            gtin: '1111111111',
            preco: 10.0,
            sku: null,
            descricao: 'desc',
            disponivel: 1,
            saldo_atual: 0,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturnCallback(function ($identificador, $nomeIdentificador) use ($entidadeExistente) {
                if ($nomeIdentificador === 'nome') {
                    return $entidadeExistente;
                }
                return null;
            });

        $useCase = new CreateUseCase(
            nome: 'Material Existente',
            gtin: '7891234567890',
            preco: 15.50,
            descricao: 'Descricao teste',
        );

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Material com nome repetido');

        $useCase->exec($this->gateway);
    }

    public function test_lanca_excecao_gtin_duplicado(): void
    {
        $entidadeExistente = new Entidade(
            uuid: 'uuid-existente',
            nome: 'Outro Material',
            gtin: '7891234567890',
            preco: 10.0,
            sku: null,
            descricao: 'desc',
            disponivel: 1,
            saldo_atual: 0,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturnCallback(function ($identificador, $nomeIdentificador) use ($entidadeExistente) {
                if ($nomeIdentificador === 'gtin') {
                    return $entidadeExistente;
                }
                return null;
            });

        $useCase = new CreateUseCase(
            nome: 'Material Novo',
            gtin: '7891234567890',
            preco: 15.50,
            descricao: 'Descricao teste',
        );

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('GTIN já cadastrado');

        $useCase->exec($this->gateway);
    }

    public function test_lanca_excecao_sku_duplicado(): void
    {
        $entidadeExistente = new Entidade(
            uuid: 'uuid-existente',
            nome: 'Outro Material',
            gtin: '1111111111',
            preco: 10.0,
            sku: 'SKU-DUP',
            descricao: 'desc',
            disponivel: 1,
            saldo_atual: 0,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')
            ->willReturnCallback(function ($identificador, $nomeIdentificador) use ($entidadeExistente) {
                if ($nomeIdentificador === 'sku') {
                    return $entidadeExistente;
                }
                return null;
            });

        $useCase = new CreateUseCase(
            nome: 'Material Novo',
            gtin: '7891234567890',
            preco: 15.50,
            sku: 'SKU-DUP',
            descricao: 'Descricao teste',
        );

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('SKU já cadastrado');

        $useCase->exec($this->gateway);
    }

    public function test_sku_null_nao_verifica_duplicidade(): void
    {
        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn(null);
        $this->repositorio->method('criar')->willReturn([
            'uuid' => 'new-uuid',
        ]);

        $useCase = new CreateUseCase(
            nome: 'Material Novo',
            gtin: '7891234567890',
            preco: 15.50,
            sku: null,
            descricao: 'Descricao teste',
        );

        $resultado = $useCase->exec($this->gateway);

        $this->assertInstanceOf(Entidade::class, $resultado);
    }

    public function test_valores_default_do_construtor(): void
    {
        $useCase = new CreateUseCase(
            nome: 'Material Novo',
            gtin: '7891234567890',
            preco: 15.50,
        );

        $this->assertNull($useCase->sku);
        $this->assertEquals(0, $useCase->estoque);
        $this->assertNull($useCase->descricao);
        $this->assertEquals(0, $useCase->disponivel);
        $this->assertEquals(0, $useCase->saldo_atual);
    }
}
