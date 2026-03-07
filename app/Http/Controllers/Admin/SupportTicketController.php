<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Services\ActivityLogger;
use App\Services\UserNotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', '');
        $priority = $request->input('priority', '');
        $category = $request->input('category', '');

        $tickets = SupportTicket::query()
            ->with(['user:id,name,email'])
            ->when($search !== '', function ($query) use ($search) {
                $needle = mb_strtolower($search);
                $query->where(function ($q) use ($needle) {
                    $q->whereRaw('LOWER(subject) like ?', ['%'.$needle.'%'])
                        ->orWhereRaw('LOWER(category) like ?', ['%'.$needle.'%']);
                })
                    ->orWhereHas('user', function ($userQuery) use ($needle) {
                        $userQuery->whereRaw('LOWER(name) like ?', ['%'.$needle.'%'])
                            ->orWhereRaw('LOWER(email) like ?', ['%'.$needle.'%']);
                    });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($priority !== '', fn ($query) => $query->where('priority', $priority))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->orderByDesc('last_reply_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($ticket) => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'last_reply_at' => $ticket->last_reply_at?->toDateTimeString(),
                'created_at' => $ticket->created_at?->toDateString(),
                'user' => $ticket->user ? [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name,
                    'email' => $ticket->user->email,
                ] : null,
            ]);

        $categories = SupportTicket::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values();

        return Inertia::render('Admin/Support/Index', [
            'tickets' => $tickets,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'priority' => $priority,
                'category' => $category,
            ],
        ]);
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user:id,name,email', 'messages.user:id,name,email']);

        return Inertia::render('Admin/Support/Show', [
            'ticket' => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'last_reply_at' => $ticket->last_reply_at?->toDateTimeString(),
                'created_at' => $ticket->created_at?->toDateTimeString(),
                'closed_at' => $ticket->closed_at?->toDateTimeString(),
                'user' => $ticket->user ? [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name,
                    'email' => $ticket->user->email,
                ] : null,
                'messages' => $ticket->messages->map(fn ($message) => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'is_admin' => (bool) $message->is_admin,
                    'author' => $message->user ? [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'email' => $message->user->email,
                    ] : null,
                    'created_at' => $message->created_at?->toDateTimeString(),
                ]),
            ],
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket, ActivityLogger $activity, UserNotificationService $notifications)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'is_admin' => true,
            'message' => $data['message'],
        ]);

        $ticket->update([
            'status' => 'answered',
            'last_reply_at' => now(),
        ]);

        $activity->log('support.ticket_answered', [
            'user' => $ticket->user,
            'actor' => $request->user(),
            'subject' => $ticket,
            'description' => 'Respuesta de admin',
            'request' => $request,
        ]);

        if ($ticket->user) {
            $notifications->create(
                $ticket->user,
                'system',
                'Respuesta de soporte',
                'Tienes una respuesta a tu ticket.',
                '/member/support/'.$ticket->id
            );
        }

        return back()->with('success', 'Respuesta enviada.');
    }

    public function update(Request $request, SupportTicket $ticket, ActivityLogger $activity)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['open', 'pending', 'answered', 'closed'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $ticket->update([
            'status' => $data['status'],
            'priority' => $data['priority'] ?? $ticket->priority,
            'category' => $data['category'] ?? $ticket->category,
            'closed_at' => $data['status'] === 'closed' ? now() : null,
        ]);

        $activity->log('support.ticket_updated', [
            'user' => $ticket->user,
            'actor' => $request->user(),
            'subject' => $ticket,
            'description' => 'Ticket actualizado',
            'request' => $request,
        ]);

        return back()->with('success', 'El ticket fue actualizado.');
    }
}
