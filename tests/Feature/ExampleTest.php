<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_displays_the_public_workshop_home(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Grupo Los Jausis')
            ->assertSee('Reservar una cita');
    }
}
