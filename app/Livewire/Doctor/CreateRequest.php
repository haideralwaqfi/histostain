<?php

namespace App\Livewire\Doctor;

use App\Enums\StainRequestPriority;
use App\Enums\StainRequestType;
use App\Services\StainRequestService;
use App\StainTypes\StainTypeRegistry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class CreateRequest extends Component
{
    use WithFileUploads;

    public string $step = 'type'; // 'type' | 'details' | 'review'

    // Shared fields
    public string $type = '';
    public string $priority = 'routine';
    public string $mrn = '';
    public string $labNumber = '';
    public string $caseNumber = '';
    public string $notes = '';

    // Type-specific structured data (initialised when type is selected)
    public array $typeData = [];

    // Optional file attachments
    public array $attachments = [];

    public function updatedType(string $value): void
    {
        if ($value) {
            $this->typeData = StainTypeRegistry::get($value)->defaultData();
        }
    }

    // ── Block helpers (block-based types only) ─────────────────

    public function addBlock(): void
    {
        $definition = StainTypeRegistry::get($this->type);
        if ($definition->supportsMultipleBlocks()) {
            $blank = $definition->defaultData()['blocks'][0];
            $this->typeData['blocks'][] = $blank;
        }
    }

    public function removeBlock(int $index): void
    {
        if (isset($this->typeData['blocks']) && count($this->typeData['blocks']) > 1) {
            array_splice($this->typeData['blocks'], $index, 1);
        }
    }

    // ── Step navigation ────────────────────────────────────────

    public function goToDetails(): void
    {
        $this->validate([
            'type' => 'required|in:' . implode(',', array_column(StainRequestType::cases(), 'value')),
        ]);
        $this->step = 'details';
    }

    public function goToReview(): void
    {
        $this->validateShared();
        $this->validateTypeData();
        $this->step = 'review';
    }

    public function backToType(): void
    {
        $this->step = 'type';
    }

    public function backToDetails(): void
    {
        $this->step = 'details';
    }

    // ── Submit ────────────────────────────────────────────────

    public function submit(StainRequestService $service): void
    {
        $this->validateShared();
        $this->validateTypeData();
        $this->validate([
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,gif',
        ]);

        $this->authorize('create', \App\Models\StainRequest::class);

        $request = $service->create(
            Auth::user(),
            [
                'type'       => $this->type,
                'priority'   => $this->priority,
                'mrn'        => $this->mrn ?: null,
                'lab_number' => $this->labNumber ?: null,
                'case_number' => $this->caseNumber,
                'notes'      => $this->notes ?: null,
                'type_data'  => $this->typeData,
            ],
            $this->attachments,
        );

        $this->redirect(route('doctor.requests.show', $request->ulid), navigate: true);
    }

    // ── Private validation helpers ─────────────────────────────

    private function validateShared(): void
    {
        $this->validate([
            'caseNumber' => 'required|string|max:100',
            'priority'   => 'required|in:' . implode(',', array_column(StainRequestPriority::cases(), 'value')),
            'mrn'        => 'nullable|string|max:50',
            'labNumber'  => 'nullable|string|max:100',
            'notes'      => 'nullable|string|max:1000',
        ]);
    }

    private function validateTypeData(): void
    {
        $rules = StainTypeRegistry::get($this->type)->rules();
        $this->validate($rules);
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.doctor.create-request', [
            'typeOptions'  => StainTypeRegistry::options(),
            'priorities'   => StainRequestPriority::cases(),
            'typeDefinition' => $this->type ? StainTypeRegistry::get($this->type) : null,
        ]);
    }
}
