<?php

namespace App\Livewire\Doctor;

use App\Enums\StainRequestPriority;
use App\Models\StainRequest;
use App\Services\StainRequestService;
use App\StainTypes\StainTypeRegistry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class EditRequest extends Component
{
    use WithFileUploads;

    public string $ulid;
    public string $typeKey;

    // Shared fields
    public string $caseNumber  = '';
    public string $mrn         = '';
    public string $patientName = '';
    public string $priority    = 'routine';
    public string $notes       = '';

    // Type-specific structured data
    public array $typeData = [];

    // New file attachments
    public array $attachments = [];

    public function mount(string $ulid): void
    {
        $request = StainRequest::where('ulid', $ulid)->firstOrFail();
        $this->authorize('update', $request);

        $this->ulid    = $ulid;
        $this->typeKey = $request->type->value;

        $this->caseNumber  = $request->case_number;
        $this->mrn         = $request->mrn ?? '';
        $this->patientName = $request->patient_name ?? '';
        $this->priority    = $request->priority->value;
        $this->notes       = $request->notes ?? '';

        // Merge existing data with defaults so any fields added after submission are populated
        $definition     = StainTypeRegistry::get($this->typeKey);
        $defaults       = $definition->defaultData();
        $existing       = $request->type_data ?? [];

        if (isset($defaults['blocks']) && isset($existing['blocks'])) {
            $this->typeData = $existing;
            foreach ($this->typeData['blocks'] as $i => $block) {
                $this->typeData['blocks'][$i] = array_merge($defaults['blocks'][0], $block);
            }
        } else {
            $this->typeData = array_merge($defaults, $existing);
        }
    }

    // ── Block helpers (match CreateRequest signature expected by partials) ──

    public function addBlock(string $typeKey): void
    {
        $definition = StainTypeRegistry::get($typeKey);
        if ($definition->supportsMultipleBlocks()) {
            $this->typeData['blocks'][] = $definition->defaultData()['blocks'][0];
        }
    }

    public function removeBlock(string $typeKey, int $index): void
    {
        if (isset($this->typeData['blocks']) && count($this->typeData['blocks']) > 1) {
            array_splice($this->typeData['blocks'], $index, 1);
        }
    }

    // ── Attachment removal ─────────────────────────────────────────

    public function removeAttachment(int $mediaId): void
    {
        $request = StainRequest::where('ulid', $this->ulid)->firstOrFail();
        $this->authorize('update', $request);

        $media = $request->getMedia('requisition_attachments')->firstWhere('id', $mediaId);
        if ($media) {
            $media->delete();
        }

        $this->dispatch('toast', message: 'Attachment removed.', type: 'success');
    }

    // ── Save ───────────────────────────────────────────────────────

    public function save(StainRequestService $service): void
    {
        $request = StainRequest::where('ulid', $this->ulid)->firstOrFail();
        $this->authorize('update', $request);

        $this->validate([
            'caseNumber'    => 'required|string|max:100',
            'priority'      => 'required|in:routine,urgent',
            'mrn'           => 'nullable|string|max:50',
            'patientName'   => 'nullable|string|max:200',
            'notes'         => 'nullable|string|max:1000',
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,gif',
        ]);

        $rules = StainTypeRegistry::get($this->typeKey)->rules();
        $this->validate($rules);

        $service->update($request, [
            'priority'     => $this->priority,
            'mrn'          => $this->mrn ?: null,
            'patient_name' => $this->patientName ?: null,
            'case_number'  => $this->caseNumber,
            'notes'        => $this->notes ?: null,
            'type_data'    => $this->typeData,
        ], $this->attachments);

        $this->dispatch('toast', message: 'Request updated successfully.', type: 'success');
        $this->redirect(route('doctor.requests.show', $this->ulid), navigate: true);
    }

    // ── Render ────────────────────────────────────────────────────

    public function render()
    {
        $request    = StainRequest::where('ulid', $this->ulid)
            ->with('media')
            ->firstOrFail();
        $definition = StainTypeRegistry::get($this->typeKey);

        return view('livewire.doctor.edit-request', [
            'request'    => $request,
            'definition' => $definition,
            'priorities' => array_filter(
                StainRequestPriority::cases(),
                fn($p) => $p->value !== 'stat'
            ),
        ]);
    }
}
