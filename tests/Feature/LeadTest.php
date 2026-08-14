<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
    }

    public function test_index_page_renders(): void
    {
        Lead::factory()->create();

        $this->actingAs($this->user)
            ->get(route('leads.index'))
            ->assertOk();
    }

    public function test_create_page_renders(): void
    {
        $this->actingAs($this->user)
            ->get(route('leads.create'))
            ->assertOk();
    }

    public function test_lead_can_be_created(): void
    {
        $this->actingAs($this->user)
            ->post(route('leads.store'), [
                'name' => 'Acme Corp',
                'email' => 'buyer@acme.test',
                'phone' => '9876543210',
                'company' => 'Acme Corp Ltd',
                'source' => 'website',
                'status' => 'new',
                'value' => 25000,
                'expected_close_date' => now()->addDays(30)->format('Y-m-d'),
                'assigned_to' => $this->user->id,
                'notes' => 'Interested in bulk order',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('leads', ['email' => 'buyer@acme.test']);
    }

    public function test_show_page_renders_with_activities(): void
    {
        $lead = Lead::factory()->create();
        LeadActivity::factory()->create([
            'lead_id' => $lead->id,
            'user_id' => $this->user->id,
            'type' => 'call',
            'note' => 'Discussed pricing',
            'next_follow_up' => now()->addDays(2),
        ]);

        $this->actingAs($this->user)
            ->get(route('leads.show', $lead))
            ->assertOk()
            ->assertSee('Discussed pricing');
    }

    public function test_status_can_be_updated(): void
    {
        $lead = Lead::factory()->create(['status' => 'new']);

        $this->actingAs($this->user)
            ->post(route('leads.status', $lead), ['status' => 'won'])
            ->assertRedirect();

        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'won']);
    }

    public function test_activity_can_be_added(): void
    {
        $lead = Lead::factory()->create();

        $this->actingAs($this->user)
            ->post(route('leads.activities', $lead), [
                'type' => 'email',
                'note' => 'Sent quotation',
                'next_follow_up' => now()->addDays(5)->format('Y-m-d'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('lead_activities', ['lead_id' => $lead->id, 'note' => 'Sent quotation']);
    }

    public function test_only_won_lead_can_be_converted(): void
    {
        $lead = Lead::factory()->create(['status' => 'qualified']);

        $this->actingAs($this->user)
            ->post(route('leads.convert', $lead))
            ->assertSessionHas('error');
    }
}
