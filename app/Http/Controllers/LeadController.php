<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with('assignee', 'customer');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%")
                    ->orWhere('company', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $leads = $query->latest()->paginate(10)->withQueryString();

        $pipeline = collect(Lead::STATUSES)->map(function ($label, $key) {
            $query = Lead::where('status', $key);

            return [
                'key' => $key,
                'label' => $label,
                'count' => (clone $query)->count(),
                'value' => (clone $query)->sum('value'),
            ];
        })->values();

        $totalValue = $pipeline->sum('value');
        $wonValue = $pipeline->firstWhere('key', 'won')['value'] ?? 0;

        $upcomingFollowUps = Lead::with('assignee')
            ->whereNotNull('last_contacted_at')
            ->where('status', 'not in', ['won', 'lost'])
            ->whereDate('last_contacted_at', '<=', now()->addDays(3))
            ->orderBy('last_contacted_at')
            ->limit(5)
            ->get();

        return view('leads.index', compact('leads', 'pipeline', 'totalValue', 'wonValue', 'upcomingFollowUps'));
    }

    public function create()
    {
        $users = User::where('role', '!=', 'admin')->get();

        return view('leads.form', ['lead' => new Lead, 'users' => $users]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:150',
            'source' => 'required|in:'.implode(',', array_keys(Lead::SOURCES)),
            'status' => 'required|in:'.implode(',', array_keys(Lead::STATUSES)),
            'value' => 'required|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $data['last_contacted_at'] = $request->filled('last_contacted_at') ? $request->last_contacted_at : ($data['status'] !== 'new' ? now()->format('Y-m-d') : null);

        $lead = Lead::create($data);

        return redirect()->route('leads.show', $lead)->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $lead->load('assignee', 'customer', 'activities.user');

        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $users = User::where('role', '!=', 'admin')->get();

        return view('leads.form', compact('lead', 'users'));
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:150',
            'source' => 'required|in:'.implode(',', array_keys(Lead::SOURCES)),
            'status' => 'required|in:'.implode(',', array_keys(Lead::STATUSES)),
            'value' => 'required|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $lead->update($data);

        return redirect()->route('leads.show', $lead)->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Lead::STATUSES)),
        ]);

        $lead->update([
            'status' => $data['status'],
            'last_contacted_at' => now()->format('Y-m-d'),
        ]);

        $message = 'Lead moved to "'.$lead->statusLabel().'".';
        if ($data['status'] === 'won') {
            $message .= ' Convert the lead to a customer when ready.';
        }

        return back()->with('success', $message);
    }

    public function addActivity(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'type' => 'required|in:'.implode(',', array_keys(LeadActivity::TYPES)),
            'note' => 'required|string',
            'next_follow_up' => 'nullable|date',
        ]);

        $lead->activities()->create([
            'user_id' => auth()->id(),
            'type' => $data['type'],
            'note' => $data['note'],
            'next_follow_up' => $data['next_follow_up'] ?? null,
        ]);

        $lead->update(['last_contacted_at' => now()->format('Y-m-d')]);

        return back()->with('success', 'Activity recorded.');
    }

    public function convert(Request $request, Lead $lead)
    {
        if (! $lead->isWon()) {
            return back()->with('error', 'Only won leads can be converted to customers.');
        }

        $customer = Customer::updateOrCreate(
            ['email' => $lead->email],
            [
                'name' => $lead->name,
                'phone' => $lead->phone,
                'company' => $lead->company,
                'notes' => $lead->notes.($lead->notes ? "\n" : '').'Converted from lead.',
            ]
        );

        $lead->update(['customer_id' => $customer->id]);

        return redirect()->route('customers.show', $customer)->with('success', 'Lead converted to customer successfully.');
    }
}
