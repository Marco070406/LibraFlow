<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * La racine redirige les visiteurs non connectés vers /login.
     * Comportement voulu dans LibraFlow (pas de page publique sur /).
     */
    public function test_the_application_redirects_unauthenticated_users_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }
}
