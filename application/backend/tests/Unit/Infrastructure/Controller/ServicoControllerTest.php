<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Controller;

use App\Domain\Entity\Servico\Entidade;
use App\Domain\Entity\Servico\RepositorioInterface as ServicoRepositorio;
use App\Exception\DomainHttpException;
use App\Infrastructure\Controller\Servico;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ServicoControllerTest extends TestCase
{
    private ServicoRepositorio $repositorio;
    private Servico $controller;

    protected function setUp(): void
    {
        $this->repositorio = $this->createMock(ServicoRepositorio::class);
        $this->controller = new Servico();
    }

    public function test_use_repositorio_retorna_self(): void
    {
        $result = $this->controller->useRepositorio($this->repositorio);

        $this->assertSame($this->controller, $result);
    }

    public function test_criar_servico_com_sucesso(): void
    {
        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn(null);
        $this->repositorio->method('criar')->willReturn([
            'uuid' => 'new-uuid',
            'nome' => 'Servico Novo',
            'valor' => 5000,
        ]);

        $resultado = $this->controller->useRepositorio($this->repositorio)->criar('Servico Novo', 5000);

        $this->assertIsArray($resultado);
        $this->assertEquals('new-uuid', $resultado['uuid']);
    }

    public function test_listar_servicos(): void
    {
        $this->repositorio->method('listar')->willReturn([
            [
                'uuid' => 'uuid-1',
                'nome' => 'Servico 1',
                'valor' => 5000,
                'criado_em' => '2024-01-01 10:00:00',
                'atualizado_em' => '2024-01-01 10:00:00',
            ],
        ]);

        $resultado = $this->controller->useRepositorio($this->repositorio)->listar();

        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
    }

    public function test_obter_um_servico(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-456',
            nome: 'Servico',
            valor: 5000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidade);

        $resultado = $this->controller->useRepositorio($this->repositorio)->obterUm('uuid-456');

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-456', $resultado['uuid']);
    }

    public function test_deletar_servico(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-456',
            nome: 'Servico',
            valor: 5000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidade);
        $this->repositorio->method('deletar')->willReturn(true);

        $resultado = $this->controller->useRepositorio($this->repositorio)->deletar('uuid-456');

        $this->assertTrue($resultado);
    }

    public function test_atualizar_servico(): void
    {
        $entidade = new Entidade(
            uuid: 'uuid-456',
            nome: 'Servico Original',
            valor: 5000,
            criadoEm: new DateTimeImmutable(),
            atualizadoEm: new DateTimeImmutable(),
        );

        $this->repositorio->method('encontrarPorIdentificadorUnico')->willReturn($entidade);
        $this->repositorio->method('atualizar')->willReturn([
            'uuid' => 'uuid-456',
            'nome' => 'Servico Atualizado',
            'valor' => 7500,
            'criado_em' => '2024-01-01 10:00:00',
            'atualizado_em' => '2024-01-02 10:00:00',
            'deletado_em' => null,
        ]);

        $resultado = $this->controller->useRepositorio($this->repositorio)->atualizar('uuid-456', 'Servico Atualizado', 7500);

        $this->assertIsArray($resultado);
        $this->assertEquals('uuid-456', $resultado['uuid']);
    }
}
