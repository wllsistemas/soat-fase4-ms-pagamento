<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entity\Material;

use App\Domain\Entity\Material\Entidade;
use App\Domain\Entity\Material\Mapper;
use App\Models\MaterialModel;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    public function test_from_model_to_entity(): void
    {
        $model = new MaterialModel();
        $model->uuid = 'uuid-123';
        $model->nome = 'Material Teste';
        $model->gtin = '7891234567890';
        $model->sku = 'SKU-001';
        $model->descricao = 'Descricao do material';
        $model->preco = 10.50;
        $model->disponivel = 1;
        $model->saldo_atual = 10;
        $model->criado_em = '2024-01-01 10:00:00';
        $model->atualizado_em = '2024-01-02 10:00:00';
        $model->deletado_em = null;

        $mapper = new Mapper();
        $entidade = $mapper->fromModelToEntity($model);

        $this->assertInstanceOf(Entidade::class, $entidade);
        $this->assertEquals('uuid-123', $entidade->uuid);
        $this->assertEquals('Material Teste', $entidade->nome);
        $this->assertEquals('7891234567890', $entidade->gtin);
        $this->assertEquals('SKU-001', $entidade->sku);
        $this->assertEquals('Descricao do material', $entidade->descricao);
        $this->assertEquals(10.50, $entidade->preco);
        $this->assertEquals(1, $entidade->disponivel);
        $this->assertEquals(10, $entidade->saldo_atual);
        $this->assertNull($entidade->deletadoEm);
    }

    public function test_from_model_to_entity_com_deletado_em(): void
    {
        $model = new MaterialModel();
        $model->uuid = 'uuid-123';
        $model->nome = 'Material Teste';
        $model->gtin = '7891234567890';
        $model->sku = null;
        $model->descricao = 'Descricao';
        $model->preco = 10.50;
        $model->disponivel = 0;
        $model->saldo_atual = 0;
        $model->criado_em = '2024-01-01 10:00:00';
        $model->atualizado_em = '2024-01-02 10:00:00';
        $model->deletado_em = '2024-01-03 10:00:00';

        $mapper = new Mapper();
        $entidade = $mapper->fromModelToEntity($model);

        $this->assertNotNull($entidade->deletadoEm);
        $this->assertTrue($entidade->estaExcluido());
    }

    public function test_construtor_vazio(): void
    {
        $mapper = new Mapper();

        $this->assertInstanceOf(Mapper::class, $mapper);
    }
}
