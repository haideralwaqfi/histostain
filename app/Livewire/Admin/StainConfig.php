<?php

namespace App\Livewire\Admin;

use App\Models\StainOption;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class StainConfig extends Component
{
    public string $tab = 'ihc';

    // Add form
    public string $newLabel = '';

    // Inline edit state
    public ?int $editingId = null;
    public string $editingLabel = '';

    // Inactivation reasons keyed by option ID — auto-saved on blur
    public array $reasons = [];

    // ── Lifecycle ─────────────────────────────────────────────────

    public function mount(): void
    {
        $this->loadReasons();
    }

    private function loadReasons(): void
    {
        StainOption::where('is_active', false)->get()
            ->each(fn($opt) => $this->reasons[$opt->id] = $opt->inactive_reason ?? '');
    }

    // ── Active / Inactive toggle ──────────────────────────────────

    public function toggleActive(int $id): void
    {
        $option    = StainOption::findOrFail($id);
        $newActive = ! $option->is_active;

        $option->update([
            'is_active'       => $newActive,
            'inactive_reason' => $newActive ? null : $option->inactive_reason,
        ]);

        if ($newActive) {
            unset($this->reasons[$id]);
        } else {
            $this->reasons[$id] = $option->inactive_reason ?? '';
        }

        StainOption::clearCache($option->type);
    }

    /** Auto-called by Livewire when any reasons.{id} changes (wire:model.blur). */
    public function updatedReasons(string $value, string $key): void
    {
        $option = StainOption::find((int) $key);
        if ($option && ! $option->is_active) {
            $option->update(['inactive_reason' => $value ?: null]);
            StainOption::clearCache($option->type);
        }
    }

    // ── Add ───────────────────────────────────────────────────────

    public function addOption(): void
    {
        $this->validate(['newLabel' => 'required|string|min:1|max:200']);

        $base = Str::slug(trim($this->newLabel), '_');
        $key  = $base ?: 'option';

        $suffix = 1;
        while (StainOption::where('type', $this->tab)->where('key', $key)->exists()) {
            $key = $base . '_' . $suffix++;
        }

        $maxOrder = StainOption::where('type', $this->tab)->max('sort_order') ?? -1;

        StainOption::create([
            'type'       => $this->tab,
            'key'        => $key,
            'label'      => trim($this->newLabel),
            'sort_order' => $maxOrder + 1,
            'is_active'  => true,
        ]);

        StainOption::clearCache($this->tab);
        $this->newLabel = '';
    }

    // ── Remove ────────────────────────────────────────────────────

    public function removeOption(int $id): void
    {
        $option = StainOption::findOrFail($id);
        $type   = $option->type;
        $option->delete();
        StainOption::clearCache($type);

        unset($this->reasons[$id]);

        if ($this->editingId === $id) {
            $this->editingId    = null;
            $this->editingLabel = '';
        }
    }

    // ── Edit label ────────────────────────────────────────────────

    public function startEdit(int $id): void
    {
        $option             = StainOption::findOrFail($id);
        $this->editingId    = $id;
        $this->editingLabel = $option->label;
    }

    public function saveEdit(): void
    {
        $this->validate(['editingLabel' => 'required|string|min:1|max:200']);

        $option = StainOption::findOrFail($this->editingId);
        $option->update(['label' => trim($this->editingLabel)]);
        StainOption::clearCache($option->type);

        $this->editingId    = null;
        $this->editingLabel = '';
    }

    public function cancelEdit(): void
    {
        $this->editingId    = null;
        $this->editingLabel = '';
    }

    // ── Reorder ───────────────────────────────────────────────────

    public function moveUp(int $id): void
    {
        $option = StainOption::findOrFail($id);
        $prev   = StainOption::where('type', $option->type)
            ->where('sort_order', '<', $option->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($prev) {
            [$option->sort_order, $prev->sort_order] = [$prev->sort_order, $option->sort_order];
            $option->save();
            $prev->save();
            StainOption::clearCache($option->type);
        }
    }

    public function moveDown(int $id): void
    {
        $option = StainOption::findOrFail($id);
        $next   = StainOption::where('type', $option->type)
            ->where('sort_order', '>', $option->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            [$option->sort_order, $next->sort_order] = [$next->sort_order, $option->sort_order];
            $option->save();
            $next->save();
            StainOption::clearCache($option->type);
        }
    }

    // ── Tab ───────────────────────────────────────────────────────

    public function switchTab(string $tab): void
    {
        $this->tab          = $tab;
        $this->newLabel     = '';
        $this->editingId    = null;
        $this->editingLabel = '';
    }

    // ── Render ────────────────────────────────────────────────────

    public function render()
    {
        $options = StainOption::where('type', $this->tab)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        $tabs = [
            'ihc'           => 'IHC Antibodies',
            'special_stain' => 'Special Stains',
            'if_stains'     => 'IF Stains',
        ];

        return view('livewire.admin.stain-config', compact('options', 'tabs'));
    }
}
