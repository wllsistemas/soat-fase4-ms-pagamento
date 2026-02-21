<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ServicoApiTest extends TestCase
{
    public function test_create_servico_com_sucesso(): void
    {
        $response = $this->postJson('/api/servico', [
            'nome' => 'Servico Feature Test',
            'valor' => 150.00,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'uuid', 'nome', 'valor',
        ]);
    }

    public function test_create_servico_sem_nome_falha(): void
    {
        $response = $this->postJson('/api/servico', [
            'valor' => 150.00,
        ]);

        $response->assertStatus(400);
        $response->assertJsonStructure(['err', 'msg']);
    }

    public function test_create_servico_sem_valor_falha(): void
    {
        $response = $this->postJson('/api/servico', [
            'nome' => 'Servico Sem Valor',
        ]);

        $response->assertStatus(400);
    }

    public function test_create_servico_nome_duplicado_retorna_erro(): void
    {
        $this->postJson('/api/servico', [
            'nome' => 'Servico Duplicado',
            'valor' => 100.00,
        ]);

        $response = $this->postJson('/api/servico', [
            'nome' => 'Servico Duplicado',
            'valor' => 200.00,
        ]);

        $response->assertJson(['err' => true]);
        $this->assertContains($response->status(), [400, 500]);
    }

    public function test_read_servicos(): void
    {
        $this->postJson('/api/servico', [
            'nome' => 'Servico Lista 1',
            'valor' => 100.00,
        ]);

        $response = $this->getJson('/api/servico');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_read_one_servico(): void
    {
        $created = $this->postJson('/api/servico', [
            'nome' => 'Servico ReadOne',
            'valor' => 100.00,
        ]);

        $uuid = $created->json('uuid');

        $response = $this->getJson("/api/servico/{$uuid}");

        $response->assertStatus(200);
        $response->assertJson(['uuid' => $uuid, 'nome' => 'Servico ReadOne']);
    }

    public function test_read_one_servico_uuid_invalido(): void
    {
        $response = $this->getJson('/api/servico/uuid-invalido');

        $response->assertStatus(400);
    }

    public function test_read_one_servico_nao_encontrado(): void
    {
        $response = $this->getJson('/api/servico/00000000-0000-0000-0000-000000000000');

        $response->assertStatus(404);
    }

    public function test_update_servico(): void
    {
        $created = $this->postJson('/api/servico', [
            'nome' => 'Servico Update',
            'valor' => 100.00,
        ]);

        $uuid = $created->json('uuid');

        $response = $this->patchJson("/api/servico/{$uuid}", [
            'nome' => 'Servico Atualizado',
            'valor' => 250.00,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['nome' => 'Servico Atualizado']);
    }

    public function test_update_servico_uuid_invalido(): void
    {
        $response = $this->patchJson('/api/servico/uuid-invalido', [
            'nome' => 'Novo Nome',
            'valor' => 100.00,
        ]);

        $response->assertStatus(400);
    }

    public function test_delete_servico(): void
    {
        $created = $this->postJson('/api/servico', [
            'nome' => 'Servico Delete',
            'valor' => 100.00,
        ]);

        $uuid = $created->json('uuid');

        $response = $this->deleteJson("/api/servico/{$uuid}");

        $response->assertStatus(204);
    }

    public function test_update_servico_sem_dados_falha(): void
    {
        $created = $this->postJson('/api/servico', [
            'nome' => 'Servico SemDados',
            'valor' => 100.00,
        ]);

        $uuid = $created->json('uuid');

        $response = $this->patchJson("/api/servico/{$uuid}", []);

        $response->assertStatus(400);
    }

    public function test_update_servico_nao_encontrado(): void
    {
        $response = $this->patchJson('/api/servico/00000000-0000-0000-0000-000000000000', [
            'nome' => 'Novo Nome',
            'valor' => 100.00,
        ]);

        // Pode retornar 400 (validacao do uuid existente) ou 404
        $this->assertContains($response->status(), [400, 404, 500]);
    }

    public function test_delete_servico_nao_encontrado(): void
    {
        $response = $this->deleteJson('/api/servico/00000000-0000-0000-0000-000000000000');

        // Pode retornar 400 ou 404
        $this->assertContains($response->status(), [400, 404]);
    }

    public function test_delete_servico_uuid_invalido(): void
    {
        $response = $this->deleteJson('/api/servico/uuid-invalido');

        $response->assertStatus(400);
    }
}
