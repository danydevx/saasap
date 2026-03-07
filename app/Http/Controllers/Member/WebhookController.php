<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\WebhookDelivery;
use App\Models\WebhookEndpoint;
use App\Services\ActivityService;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $endpoints = WebhookEndpoint::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->get()
            ->map(fn ($endpoint) => [
                'id' => $endpoint->id,
                'name' => $endpoint->name,
                'url' => $endpoint->url,
                'is_active' => $endpoint->is_active,
                'events' => $endpoint->events ?? [],
                'last_used_at' => $endpoint->last_used_at?->toDateTimeString(),
                'failure_count' => $endpoint->failure_count,
                'created_at' => $endpoint->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Member/Webhooks/Index', [
            'endpoints' => $endpoints,
            'availableEvents' => WebhookService::EVENTS,
        ]);
    }

    public function store(Request $request, ActivityService $activity)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'url' => ['required', 'url', 'max:500'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string', 'in:'.implode(',', WebhookService::EVENTS)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $secret = $this->generateSecret();

        $endpoint = WebhookEndpoint::create([
            'user_id' => $request->user()->id,
            'name' => $data['name'],
            'url' => $data['url'],
            'events' => $data['events'],
            'secret' => $secret,
            'is_active' => $data['is_active'] ?? true,
        ]);

        $activity->log('webhook_created', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $endpoint,
            'description' => 'Webhook creado',
            'request' => $request,
        ]);

        return redirect()
            ->route('member.webhooks.index')
            ->with('success', 'Webhook creado correctamente.')
            ->with('webhook_secret', $secret);
    }

    public function update(Request $request, WebhookEndpoint $webhook, ActivityService $activity)
    {
        if ($webhook->user_id !== $request->user()->id) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'url' => ['required', 'url', 'max:500'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['string', 'in:'.implode(',', WebhookService::EVENTS)],
            'is_active' => ['required', 'boolean'],
        ]);

        $webhook->update($data);

        $activity->log('webhook_updated', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $webhook,
            'description' => 'Webhook actualizado',
            'request' => $request,
        ]);

        return back()->with('success', 'Webhook actualizado correctamente.');
    }

    public function destroy(Request $request, WebhookEndpoint $webhook, ActivityService $activity)
    {
        if ($webhook->user_id !== $request->user()->id) {
            abort(403);
        }

        $webhook->delete();

        $activity->log('webhook_deleted', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'description' => 'Webhook eliminado',
            'request' => $request,
        ]);

        return back()->with('success', 'Webhook eliminado correctamente.');
    }

    public function test(Request $request, WebhookEndpoint $webhook, WebhookService $service, ActivityService $activity)
    {
        if ($webhook->user_id !== $request->user()->id) {
            abort(403);
        }

        $service->sendTest($webhook);

        $activity->log('webhook_tested', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $webhook,
            'description' => 'Webhook probado',
            'request' => $request,
        ]);

        return back()->with('success', 'Evento de prueba enviado.');
    }

    public function regenerateSecret(Request $request, WebhookEndpoint $webhook, ActivityService $activity)
    {
        if ($webhook->user_id !== $request->user()->id) {
            abort(403);
        }

        $secret = $this->generateSecret();
        $webhook->update(['secret' => $secret]);

        $activity->log('webhook_secret_regenerated', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $webhook,
            'description' => 'Secreto regenerado',
            'request' => $request,
        ]);

        return back()
            ->with('success', 'Secreto regenerado correctamente. Copie el nuevo valor ahora.')
            ->with('webhook_secret', $secret);
    }

    public function deliveries(Request $request, WebhookEndpoint $webhook)
    {
        if ($webhook->user_id !== $request->user()->id) {
            abort(403);
        }

        $deliveries = WebhookDelivery::query()
            ->where('webhook_endpoint_id', $webhook->id)
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($delivery) => [
                'id' => $delivery->id,
                'event' => $delivery->event,
                'response_status' => $delivery->response_status,
                'error_message' => $delivery->error_message,
                'delivered_at' => $delivery->delivered_at?->toDateTimeString(),
                'failed_at' => $delivery->failed_at?->toDateTimeString(),
                'attempt_count' => $delivery->attempt_count,
                'created_at' => $delivery->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Member/Webhooks/Deliveries', [
            'webhook' => [
                'id' => $webhook->id,
                'name' => $webhook->name,
                'url' => $webhook->url,
            ],
            'deliveries' => $deliveries,
        ]);
    }

    public function retryDelivery(Request $request, WebhookDelivery $delivery, WebhookService $service, ActivityService $activity)
    {
        $delivery->load('endpoint');
        if (! $delivery->endpoint || $delivery->endpoint->user_id !== $request->user()->id) {
            abort(403);
        }

        $service->retryDelivery($delivery);

        $activity->log('webhook_delivery_retried', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'subject' => $delivery,
            'description' => 'Delivery reintentado',
            'request' => $request,
        ]);

        return back()->with('success', 'Entrega reenviada correctamente.');
    }

    private function generateSecret(): string
    {
        return 'whsec_'.Str::random(48);
    }
}
