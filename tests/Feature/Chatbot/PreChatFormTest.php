<?php

namespace Tests\Feature\Chatbot;

use App\Models\Chatbot\ChatbotFormField;
use App\Models\Chatbot\ChatbotLead;
use App\Models\Chatbot\ChatbotVisitor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreChatFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_api_returns_active_form_fields(): void
    {
        ChatbotFormField::create([
            'label' => 'Full Name',
            'field_key' => 'name',
            'field_type' => 'text',
            'is_required' => true,
            'sort_order' => 1,
        ]);
        ChatbotFormField::create([
            'label' => 'Email',
            'field_key' => 'email',
            'field_type' => 'email',
            'is_required' => false,
            'sort_order' => 2,
        ]);
        ChatbotFormField::create([
            'label' => 'Class',
            'field_key' => 'class',
            'field_type' => 'select',
            'options' => ['Nursery', 'KG', 'Class 1'],
            'is_required' => true,
            'sort_order' => 3,
        ]);
        ChatbotFormField::create([
            'label' => 'Inactive',
            'field_key' => 'hidden',
            'field_type' => 'text',
            'is_active' => false,
        ]);

        $response = $this->getJson(route('chatbot.widget.form-fields'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure([
            '*' => ['id', 'label', 'field_key', 'field_type', 'placeholder', 'options', 'is_required']
        ]);
        $response->assertJsonFragment(['field_key' => 'name', 'is_required' => true]);
        $response->assertJsonFragment(['field_key' => 'class', 'options' => ['Nursery', 'KG', 'Class 1']]);
        $response->assertJsonMissing(['field_key' => 'hidden']);
    }

    public function test_submit_lead_with_dynamic_form_data(): void
    {
        ChatbotFormField::create([
            'label' => 'Full Name',
            'field_key' => 'name',
            'field_type' => 'text',
            'is_required' => true,
            'sort_order' => 1,
        ]);
        ChatbotFormField::create([
            'label' => 'Phone',
            'field_key' => 'phone',
            'field_type' => 'tel',
            'is_required' => true,
            'sort_order' => 2,
        ]);
        ChatbotFormField::create([
            'label' => 'Message',
            'field_key' => 'message',
            'field_type' => 'textarea',
            'is_required' => false,
            'sort_order' => 3,
        ]);

        $response = $this->postJson(route('chatbot.widget.lead'), [
            'session_id' => 'dynamic_form_test',
            'form_data' => [
                'name' => 'Ravi Kumar',
                'phone' => '9876543210',
                'message' => 'Interested in admission',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('chatbot_leads', [
            'name' => 'Ravi Kumar',
            'phone' => '9876543210',
            'source' => 'chatbot',
        ]);

        $lead = ChatbotLead::where('name', 'Ravi Kumar')->first();
        $this->assertNotNull($lead->form_data);
        $this->assertEquals('Ravi Kumar', $lead->form_data['name']);
        $this->assertEquals('9876543210', $lead->form_data['phone']);
        $this->assertEquals('Interested in admission', $lead->form_data['message']);
    }

    public function test_submit_lead_rejects_missing_required_fields(): void
    {
        ChatbotFormField::create([
            'label' => 'Full Name',
            'field_key' => 'name',
            'field_type' => 'text',
            'is_required' => true,
            'sort_order' => 1,
        ]);
        ChatbotFormField::create([
            'label' => 'Email',
            'field_key' => 'email',
            'field_type' => 'email',
            'is_required' => true,
            'sort_order' => 2,
        ]);

        $response = $this->postJson(route('chatbot.widget.lead'), [
            'session_id' => 'missing_fields_test',
            'form_data' => [
                'name' => '',
                'email' => '',
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $response->assertJsonStructure(['errors' => ['name', 'email']]);
    }

    public function test_submit_lead_with_legacy_fields_still_works(): void
    {
        $response = $this->postJson(route('chatbot.widget.lead'), [
            'session_id' => 'legacy_test',
            'name' => 'Legacy User',
            'email' => 'legacy@example.com',
            'phone' => '1111111111',
            'class' => 'primary',
            'interest' => 'Admission Info',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('chatbot_leads', [
            'name' => 'Legacy User',
            'email' => 'legacy@example.com',
            'admission_class' => 'primary',
        ]);
    }

    public function test_admin_can_create_form_field(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.chatbot.form-fields.store'), [
                'label' => 'City',
                'field_key' => 'city',
                'field_type' => 'text',
                'placeholder' => 'Your city',
                'is_required' => '1',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('chatbot_form_fields', [
            'label' => 'City',
            'field_key' => 'city',
            'field_type' => 'text',
            'is_required' => true,
        ]);
    }

    public function test_admin_can_toggle_form_field_active(): void
    {
        $admin = User::factory()->create();
        $field = ChatbotFormField::create([
            'label' => 'Toggle Test',
            'field_key' => 'toggle_test',
            'field_type' => 'text',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.chatbot.form-fields.toggle', ['id' => $field->id]));

        $response->assertRedirect();
        $field->refresh();
        $this->assertFalse($field->is_active);
    }

    public function test_admin_can_delete_form_field(): void
    {
        $admin = User::factory()->create();
        $field = ChatbotFormField::create([
            'label' => 'Delete Me',
            'field_key' => 'delete_me',
            'field_type' => 'text',
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.chatbot.form-fields.destroy', ['id' => $field->id]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('chatbot_form_fields', ['id' => $field->id]);
    }

    public function test_admin_can_update_form_field(): void
    {
        $admin = User::factory()->create();
        $field = ChatbotFormField::create([
            'label' => 'Original',
            'field_key' => 'original_key',
            'field_type' => 'text',
            'is_required' => false,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.chatbot.form-fields.update', ['id' => $field->id]), [
                'label' => 'Updated Label',
                'field_key' => 'original_key',
                'field_type' => 'email',
                'is_required' => '1',
                'is_active' => '1',
            ]);

        $response->assertRedirect();
        $field->refresh();
        $this->assertEquals('Updated Label', $field->label);
        $this->assertEquals('email', $field->field_type);
        $this->assertTrue($field->is_required);
    }

    public function test_admin_can_reorder_form_fields(): void
    {
        $admin = User::factory()->create();
        $fieldA = ChatbotFormField::create(['label' => 'A', 'field_key' => 'a', 'field_type' => 'text', 'sort_order' => 0]);
        $fieldB = ChatbotFormField::create(['label' => 'B', 'field_key' => 'b', 'field_type' => 'text', 'sort_order' => 1]);
        $fieldC = ChatbotFormField::create(['label' => 'C', 'field_key' => 'c', 'field_type' => 'text', 'sort_order' => 2]);

        $response = $this->actingAs($admin)
            ->postJson(route('admin.chatbot.form-fields.reorder'), [
                'order' => [$fieldC->id, $fieldA->id, $fieldB->id],
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(0, $fieldC->fresh()->sort_order);
        $this->assertEquals(1, $fieldA->fresh()->sort_order);
        $this->assertEquals(2, $fieldB->fresh()->sort_order);
    }

    public function test_submissions_page_shows_form_data(): void
    {
        $admin = User::factory()->create();

        ChatbotFormField::create(['label' => 'Full Name', 'field_key' => 'name', 'field_type' => 'text', 'sort_order' => 1]);
        ChatbotFormField::create(['label' => 'City', 'field_key' => 'city', 'field_type' => 'text', 'sort_order' => 2]);

        $visitor = ChatbotVisitor::create(['session_id' => 'sub_test', 'name' => 'Test User']);
        ChatbotLead::create([
            'visitor_id' => $visitor->id,
            'name' => 'Test User',
            'source' => 'chatbot',
            'form_data' => ['name' => 'Test User', 'city' => 'Delhi'],
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.chatbot.form-fields.submissions'));

        $response->assertStatus(200);
        $response->assertSee('Test User');
        $response->assertSee('Delhi');
    }

    public function test_form_fields_page_shows_admin_interface(): void
    {
        $admin = User::factory()->create();

        ChatbotFormField::create(['label' => 'Test Field', 'field_key' => 'test', 'field_type' => 'text']);

        $response = $this->actingAs($admin)
            ->get(route('admin.chatbot.form-fields'));

        $response->assertStatus(200);
        $response->assertSee('Test Field');
        $response->assertSee('Pre-Chat Form Builder');
    }
}
