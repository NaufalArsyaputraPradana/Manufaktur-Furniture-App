<?php

namespace Tests\Unit\Components;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FileInputComponentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function component_renders_correctly()
    {
        $view = $this->blade(
            '<x-file-input name="profile_image" label="Profile Photo" />'
        );

        $this->assertStringContainsString('Profile Photo', $view);
        $this->assertStringContainsString('name="profile_image"', $view);
        $this->assertStringContainsString('type="file"', $view);
    }

    /** @test */
    public function component_supports_multiple_files()
    {
        $view = $this->blade(
            '<x-file-input name="images" multiple />'
        );

        $this->assertStringContainsString('multiple', $view);
    }

    /** @test */
    public function component_displays_accept_attribute()
    {
        $view = $this->blade(
            '<x-file-input name="image" accept="image/*" />'
        );

        $this->assertStringContainsString('accept="image/*"', $view);
    }

    /** @test */
    public function component_displays_error_message()
    {
        $view = $this->blade(
            '<x-file-input name="image" error="File is required" />'
        );

        $this->assertStringContainsString('File is required', $view);
        $this->assertStringContainsString('alert-danger', $view);
    }

    /** @test */
    public function component_displays_preview()
    {
        $view = $this->blade(
            '<x-file-input name="image" preview previewUrl="/images/test.jpg" />'
        );

        $this->assertStringContainsString('/images/test.jpg', $view);
        $this->assertStringContainsString('img-thumbnail', $view);
    }

    /** @test */
    public function component_displays_help_text()
    {
        $view = $this->blade(
            '<x-file-input name="image" helpText="Max 5MB" />'
        );

        $this->assertStringContainsString('Max 5MB', $view);
    }

    /** @test */
    public function component_shows_max_size_in_label()
    {
        $view = $this->blade(
            '<x-file-input name="image" label="Image" maxSize="5MB" />'
        );

        $this->assertStringContainsString('Max 5MB', $view);
    }

    /** @test */
    public function component_respects_disabled_attribute()
    {
        $view = $this->blade(
            '<x-file-input name="image" disabled />'
        );

        $this->assertStringContainsString('disabled', $view);
    }

    /** @test */
    public function component_includes_drag_drop_zone()
    {
        $view = $this->blade(
            '<x-file-input name="image" />'
        );

        $this->assertStringContainsString('border-dashed', $view);
        $this->assertStringContainsString('dropzone', $view);
    }

    /** @test */
    public function component_is_accessible()
    {
        $view = $this->blade(
            '<x-file-input name="image" label="Upload File" />'
        );

        // Check for label association
        $this->assertStringContainsString('<label', $view);
        $this->assertStringContainsString('for=', $view);
    }
}
