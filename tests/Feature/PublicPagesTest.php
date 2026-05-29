<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_about_page_can_be_rendered(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
    }

    public function test_packages_page_can_be_rendered(): void
    {
        $response = $this->get('/packages');

        $response->assertStatus(200);
    }

    public function test_tours_page_can_be_rendered(): void
    {
        $response = $this->get('/tours');

        $response->assertStatus(200);
    }

    public function test_accommodation_page_can_be_rendered(): void
    {
        $response = $this->get('/accommodation');

        $response->assertStatus(200);
    }

    public function test_legacy_stay_url_redirects_to_accommodation(): void
    {
        $this->get('/stay')->assertRedirect('/accommodation');
    }

    public function test_wishlist_page_can_be_rendered(): void
    {
        $response = $this->get('/wishlist');

        $response->assertStatus(200);
    }

    public function test_news_page_can_be_rendered(): void
    {
        $response = $this->get('/news');

        $response->assertStatus(200);
    }

    public function test_contact_page_can_be_rendered(): void
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
    }
}
