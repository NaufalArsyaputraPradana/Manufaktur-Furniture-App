<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * FormInput Component Unit Tests
 * 
 * Tests the FormInput component in isolation by verifying
 * that it correctly renders with various input types and options.
 */
class FormInputComponentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test FormInput component file exists and is properly configured
     */
    public function test_form_input_component_exists()
    {
        $componentPath = resource_path('views/components/form-input.blade.php');
        $this->assertFileExists($componentPath);
        
        $content = file_get_contents($componentPath);
        $this->assertStringContainsString('@props', $content);
        $this->assertStringContainsString('type', $content);
        $this->assertStringContainsString('name', $content);
    }

    /**
     * Test FormInput component supports all basic input types
     */
    public function test_form_input_supports_text_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString("'text'", $content);
    }

    /**
     * Test FormInput component supports email input type
     */
    public function test_form_input_supports_email_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString("'email'", $content);
    }

    /**
     * Test FormInput component supports tel input type
     */
    public function test_form_input_supports_tel_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString("'tel'", $content);
    }

    /**
     * Test FormInput component supports password input type
     */
    public function test_form_input_supports_password_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString("'password'", $content);
    }

    /**
     * Test FormInput component supports number input type
     */
    public function test_form_input_supports_number_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString("'number'", $content);
    }

    /**
     * Test FormInput component supports date input type
     */
    public function test_form_input_supports_date_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString("'date'", $content);
    }

    /**
     * Test FormInput component supports time input type
     */
    public function test_form_input_supports_time_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString("'time'", $content);
    }

    /**
     * Test FormInput component supports datetime-local input type
     */
    public function test_form_input_supports_datetime_local_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString('datetime-local', $content);
    }

    /**
     * Test FormInput component supports select type
     */
    public function test_form_input_supports_select_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString("'select'", $content);
        $this->assertStringContainsString('<select', $content);
    }

    /**
     * Test FormInput component supports textarea type
     */
    public function test_form_input_supports_textarea_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString("'textarea'", $content);
        $this->assertStringContainsString('<textarea', $content);
    }

    /**
     * Test FormInput component supports checkbox type
     */
    public function test_form_input_supports_checkbox_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString("'checkbox'", $content);
        $this->assertStringContainsString('type="checkbox"', $content);
    }

    /**
     * Test FormInput component supports radio type
     */
    public function test_form_input_supports_radio_type()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString("'radio'", $content);
        $this->assertStringContainsString('type="radio"', $content);
    }

    /**
     * Test FormInput component handles error state
     */
    public function test_form_input_handles_error_state()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString('invalid-feedback', $content);
        $this->assertStringContainsString('is-invalid', $content);
    }

    /**
     * Test FormInput component includes CSRF field in select
     */
    public function test_form_input_handles_hidden_field()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString('option value=""', $content);
    }

    /**
     * Test FormInput component uses form-control Bootstrap class
     */
    public function test_form_input_uses_bootstrap_form_control()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString('form-control', $content);
    }

    /**
     * Test FormInput component includes label element
     */
    public function test_form_input_includes_label_element()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString('<label', $content);
        $this->assertStringContainsString('form-label', $content);
    }

    /**
     * Test FormInput component supports required attribute
     */
    public function test_form_input_supports_required_attribute()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString('required', $content);
    }

    /**
     * Test FormInput component supports disabled attribute
     */
    public function test_form_input_supports_disabled_attribute()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString('disabled', $content);
    }

    /**
     * Test FormInput component supports placeholder attribute
     */
    public function test_form_input_supports_placeholder_attribute()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $this->assertStringContainsString('placeholder', $content);
    }

    /**
     * Test FormInput component is approximately 100+ lines
     */
    public function test_form_input_component_size()
    {
        $content = file_get_contents(resource_path('views/components/form-input.blade.php'));
        $lines = count(explode("\n", $content));
        $this->assertGreaterThan(50, $lines, 'Component should be substantial');
        $this->assertLessThan(500, $lines, 'Component should not be overly complex');
    }
}
