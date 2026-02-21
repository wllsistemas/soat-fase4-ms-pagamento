<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\UseCase\Material;

use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\RepositorioInterface;
use App\Domain\UseCase\Material\UpdateUseCase;
use App\Exception\DomainHttpException;
use App\Infrastructure\Gateway\MaterialGateway;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UpdateUseCaseTest extends TestCase
{
    private MaterialGateway $gateway;
    private RepositorioInterface $repositorio;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(RepositorioInterface::class);
        $this->gateway = new MaterialGateway($this->repositorio);
    }

    public function test_atualiza_material_com_sucesso(): void
    {
        $entidadeExistente = new Entidade(
            uuid: 'uuid-123',
            nome: 'Material Original',
            gtin: '7891234567890',
            preco: 10.50,
            sku: 'SKU-001',
            descricao: 'Descricao original',
            disponivel: 1,
            saldo_atual: 5,
            criadoEm: new DateTimeImmutable('2024-01-01 10:00:00'),
            atualizadoEm: new DateTimeImmutable('2024-01-01 10:00:00'),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidadeExistente);
        $this->repositorio->method('atualizar')->willReturn([
            'uuid' => 'uuid-123',
            'nome' => 'Material Atualizado',
            'gtin' => '7891234567890',
            'sku' => 'SKU-001',
            'descricao' => 'Descricao original',
            'preco' => 25.00,
            'disponivel' => 1,
            'saldo_atual' => 5,
            'criado_em' => '2024-01-01 10:00:00',
            'atualizado_em' => '2024-01-02 10:00:00',
            'deletado_em' => null,
        ]);

        $useCase = new UpdateUseCase($this->gateway);
        $resultado = $useCase->exec('uuid-123', ['nome' => 'Material Atualizado', 'preco' => 25.00]);

        $this->assertInstanceOf(Entidade::class, $resultado);
        $this->assertEquals('Material Atualizado', $resultado->nome);
        $this->assertEquals(25.00, $resultado->preco);
    }

    public function test_lanca_excecao_uuid_vazio(): void
    {
        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('identificador único não informado');

        $useCase = new UpdateUseCase($this->gateway);
        $useCase->exec('', ['nome' => 'Novo Nome']);
    }

    public function test_lanca_excecao_material_nao_encontrado(): void
    {
        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn(null);

        $this->expectException(DomainHttpException::class);
        $this->expectExceptionMessage('Não encontrado(a)');

        $useCase = new UpdateUseCase($this->gateway);
        $useCase->exec('uuid-inexistente', ['nome' => 'Novo Nome']);
    }
}
