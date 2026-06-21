<?php

namespace App\Livewire\Doctor;

use App\Enums\StainRequestPriority;
use App\Enums\StainRequestType;
use App\Services\StainRequestService;
use App\StainTypes\StainTypeRegistry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class CreateRequest extends Component
{
    use WithFileUploads;

    public string $step = 'type'; // 'type' | 'details' | 'review'

    // Shared fields
    public array $selectedTypes = [];
    public string $priority = 'routine';
    public string $mrn = '';
    public string $patientName = '';
    public string $caseNumber = '';
    public string $notes = '';

    // Per-type structured data keyed by StainRequestType value
    public array $typesData = [];

    // Shared optional file attachments
    public array $attachments = [];

    // ── Type selection ─────────────────────────────────────────

    public function toggleType(string $typeValue): void
    {
        if (in_array($typeValue, $this->selectedTypes, true)) {
            $this->selectedTypes = array_values(array_filter(
                $this->selectedTypes,
                fn($t) => $t !== $typeValue,
            ));
        } else {
            $this->selectedTypes[] = $typeValue;
            // Preserve data if the doctor de-selects and re-selects a type
            if (!isset($this->typesData[$typeValue])) {
                $this->typesData[$typeValue] = StainTypeRegistry::get($typeValue)->defaultData();
            }
        }
    }

    // ── Block helpers ──────────────────────────────────────────

    public function addBlock(string $typeKey): void
    {
        $definition = StainTypeRegistry::get($typeKey);
        if ($definition->supportsMultipleBlocks()) {
            $this->typesData[$typeKey]['blocks'][] = $definition->defaultData()['blocks'][0];
        }
    }

    public function removeBlock(string $typeKey, int $index): void
    {
        if (isset($this->typesData[$typeKey]['blocks']) && count($this->typesData[$typeKey]['blocks']) > 1) {
            array_splice($this->typesData[$typeKey]['blocks'], $index, 1);
        }
    }

    // ── Step navigation ────────────────────────────────────────

    public function goToDetails(): void
    {
        $this->validate(
            ['selectedTypes' => 'required|array|min:1'],
            ['selectedTypes.required' => 'Select at least one stain type.',
             'selectedTypes.min'      => 'Select at least one stain type.'],
        );
        $this->step = 'details';
    }

    public function goToReview(): void
    {
        $this->validateShared();
        $this->validateAllTypesData();
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
        $this->validateAllTypesData();
        $this->validate([
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png,gif',
        ]);

        $this->authorize('create', \App\Models\StainRequest::class);

        $shared = [
            'priority'    => $this->priority,
            'mrn'         => $this->mrn ?: null,
            'patient_name' => $this->patientName ?: null,
            'case_number' => $this->caseNumber,
            'notes'       => $this->notes ?: null,
        ];

        DB::transaction(function () use ($service, $shared) {
            foreach ($this->selectedTypes as $typeKey) {
                $service->create(
                    Auth::user(),
                    array_merge($shared, [
                        'type'      => $typeKey,
                        'type_data' => $this->typesData[$typeKey],
                    ]),
                    $this->attachments,
                );
            }
        });

        $this->redirect(route('doctor.requests'), navigate: true);
    }

    // ── Private validation helpers ─────────────────────────────

    private function validateShared(): void
    {
        $this->validate([
            'caseNumber' => 'required|string|max:100',
            'priority'   => 'required|in:routine,urgent',
            'mrn'         => 'nullable|string|max:50',
            'patientName' => 'nullable|string|max:200',
            'notes'      => 'nullable|string|max:1000',
        ]);
    }

    private function validateAllTypesData(): void
    {
        foreach ($this->selectedTypes as $typeKey) {
            $rules = StainTypeRegistry::get($typeKey)->rules();
            $renamedRules = [];
            foreach ($rules as $key => $rule) {
                // Rules use 'typeData.*' prefix — remap to 'typesData.{typeKey}.*'
                $renamedRules[preg_replace('/^typeData/', 'typesData.' . $typeKey, $key)] = $rule;
            }
            $this->validate($renamedRules);
        }
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.doctor.create-request', [
            'typeOptions' => StainTypeRegistry::options(),
            'priorities'  => array_filter(
                StainRequestPriority::cases(),
                fn($p) => $p->value !== 'stat'
            ),
        ]);
    }
}
