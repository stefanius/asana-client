<?php

namespace TestMonitor\Asana\Tests;

use Mockery;
use TestMonitor\Asana\Client;
use PHPUnit\Framework\TestCase;
use Asana\Errors\NoAuthorizationError;
use TestMonitor\Asana\Resources\Workspace;
use TestMonitor\Asana\Exceptions\UnauthorizedException;

class WorkspacesTest extends TestCase
{
    protected $token;

    protected $workspace;

    protected $workspaces;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = Mockery::mock('\TestMonitor\Asana\Token');
        $this->token->shouldReceive('expired')->andReturnFalse();

        $this->workspace = (object) ['gid' => '1', 'name' => 'Workspace'];

        $this->workspaces = Mockery::mock('\Asana\Iterator\ItemIterator');
        $this->workspaces->shouldReceive('rewind')->andReturnNull();
        $this->workspaces->shouldReceive('next')->andReturnNull();
        $this->workspaces->shouldReceive('valid')->atMost()->times(1)->andReturnTrue();
        $this->workspaces->shouldReceive('valid')->andReturnFalse();
        $this->workspaces->shouldReceive('key')->andReturn(0);
        $this->workspaces->shouldReceive('current')->andReturn($this->workspace);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_return_a_list_of_workspaces()
    {
        // Given
        $asana = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], $this->token);

        $asana->setClient($service = Mockery::mock('\Asana\Client'));

        $service->workspaces = Mockery::mock('\Asana\Resources\Workspaces');
        $service->workspaces->shouldReceive('findAll')->once()->andReturn(
            $this->workspaces
        );

        // When
        $workspaces = $asana->workspaces();

        // Then
        $this->assertIsArray($workspaces);
        $this->assertCount(1, $workspaces);
        $this->assertInstanceOf(Workspace::class, $workspaces[0]);
        $this->assertEquals($this->workspace->gid, $workspaces[0]->gid);
    }

    /** @test */
    public function it_should_throw_an_exception_when_client_fails_to_get_a_list_of_projects()
    {
        // Given
        $asana = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], $this->token);

        $asana->setClient($service = Mockery::mock('\Asana\Client'));

        $service->workspaces = Mockery::mock('\Asana\Resources\Workspaces');
        $service->workspaces->shouldReceive('findAll')->once()->andThrow(new NoAuthorizationError([]));

        $this->expectException(UnauthorizedException::class);

        // When
        $workspaces = $asana->workspaces();
    }

    /** @test */
    public function it_should_return_a_single_project()
    {
        // Given
        $asana = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], $this->token);

        $asana->setClient($service = Mockery::mock('\Asana\Client'));

        $service->workspaces = Mockery::mock('\Asana\Resources\Workspaces');
        $service->workspaces->shouldReceive('findById')->once()->with($this->workspace->gid)->andReturn(
            $this->workspace
        );

        // When
        $workspace = $asana->workspace($this->workspace->gid);

        // Then
        $this->assertInstanceOf(Workspace::class, $workspace);
        $this->assertEquals($this->workspace->gid, $workspace->gid);
    }

    /** @test */
    public function it_should_throw_an_exception_when_client_fails_to_get_a_single_project()
    {
        // Given
        $asana = new Client(['clientId' => 1, 'clientSecret' => 'secret', 'redirectUrl' => 'none'], $this->token);

        $asana->setClient($service = Mockery::mock('\Asana\Client'));

        $service->workspaces = Mockery::mock('\Asana\Resources\Workspaces');
        $service->workspaces->shouldReceive('findById')->once()->with($this->workspace->gid)->andThrow(new NoAuthorizationError([]));

        $this->expectException(UnauthorizedException::class);

        // When
        $workspace = $asana->workspace($this->workspace->gid);
    }
}
