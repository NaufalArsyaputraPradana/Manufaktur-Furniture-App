<?php

namespace Tests\Unit\Components;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchInputComponentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function component_renders_correctly()
    {
        $view = $this->blade(
            '<x-search-input name="search" />'
        );

        $this->assertStringContainsString('type="text"', $view);
        $this->assertStringContainsString('name="search"', $view);
        $this->assertStringContainsString('input-group', $view);
    }

    /** @test */
    public function component_displays_icon()
    {
        $view = $this->blade(
            '<x-search-input icon="bi-search" />'
        );

        $this->assertStringContainsString('bi-search', $view);
    }

    /** @test */
    public function component_supports_icon_positioning()
    {
        $viewLeft = $this->blade(
            '<x-search-input iconPosition="left" />'
        );

        $viewRight = $this->blade(
            '<x-search-input iconPosition="right" />'
        );

        // Both should render successfully
        $this->assertStringContainsString('input-group-text', $viewLeft);
        $this->assertStringContainsString('input-group-text', $viewRight);
    }

    /** @test */
    public function component_uses_custom_placeholder()
    {
        $view = $this->blade(
            '<x-search-input placeholder="Search products..." />'
        );

        $this->assertStringContainsString('placeholder="Search products..."', $view);
    }

    /** @test */
    public function component_respects_disabled_attribute()
    {
        $view = $this->blade(
            '<x-search-input disabled />'
        );

        $this->assertStringContainsString('disabled', $view);
    }

    /** @test */
    public function component_includes_clear_button()
    {
        $view = $this->blade(
            '<x-search-input clearable />'
        );

        $this->assertStringContainsString('bi-x-circle', $view);
        $this->assertStringContainsString('clearSearch', $view);
    }

    /** @test */
    public function component_supports_initial_value()
    {
        $view = $this->blade(
            '<x-search-input name="search" value="test query" />'
        );

        $this->assertStringContainsString('value="test query"', $view);
    }

    /** @test */
    public function component_includes_javascript()
    {
        $view = $this->blade(
            '<x-search-input />'
        );

        $this->assertStringContainsString('toggleClearButton', $view);
        $this->assertStringContainsString('clearSearch', $view);
    }

    /** @test */
    public function component_uses_bootstrap_styling()
    {
        $view = $this->blade(
            '<x-search-input />'
        );

        $this->assertStringContainsString('form-control', $view);
        $this->assertStringContainsString('input-group', $view);
    }
}
