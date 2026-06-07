@props(['request', 'showAttachmentBadge' => true])

@php
    $td   = $request->type_data ?? [];
    $blocks = $td['blocks'] ?? null;
    $attachmentCount = $showAttachmentBadge ? $request->getMedia('requisition_attachments')->count() : 0;

    $specialStainLabels = \App\StainTypes\Types\SpecialStainType::options();
    $cytologyStainLabels = [
        'pap' => 'Pap', 'h_and_e' => 'H&E', 'diff_quik' => 'Diff-Quik',
        'mucicarmine' => 'Mucicarmine', 'pas' => 'PAS', 'other' => 'Other',
    ];
    $decalcMethodLabels = ['edta' => 'EDTA', 'acid' => 'Acid', 'other' => 'Other'];
    $ihcAntibodyLabels = \App\StainTypes\Types\IhcType::options();
@endphp

<div class="space-y-2 text-xs">

    {{-- Requisition attachments badge --}}
    @if($attachmentCount > 0)
        <div class="flex items-center gap-1.5 rounded-xl bg-blue-50 px-3 py-2 text-blue-700">
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/>
            </svg>
            {{ $attachmentCount }} requisition {{ Str::plural('file', $attachmentCount) }} attached
        </div>
    @endif

    {{-- ── Block-based types ──────────────────────────────────── --}}
    @if($blocks !== null)

        @foreach($blocks as $i => $block)
            @php
                $prefix = count($blocks) > 1 ? 'Block '.($i + 1) : 'Block';
                $prefix .= !empty($block['block_id']) ? ' · '.$block['block_id'] : '';

                // Build display rows for this block based on stain type
                $stainVal = $block['stain'] ?? '';
                $rows = [];

                switch ($request->type->value) {
                    case 'ihc':
                        $antibodyKeys = $block['antibodies'] ?? (isset($block['antibody']) && $block['antibody'] ? [$block['antibody']] : []);
                        $antibodyLabels = array_map(function ($k) use ($ihcAntibodyLabels, $block) {
                            return $k === 'other' && !empty($block['antibody_other'])
                                ? $block['antibody_other']
                                : ($ihcAntibodyLabels[$k] ?? $k);
                        }, $antibodyKeys);
                        $rows = array_filter([
                            'Antibody'   => $antibodyLabels ? implode(', ', $antibodyLabels) : null,
                            'Clone'      => $block['clone'] ?: null,
                            'Dilution'   => $block['dilution'] ?: null,
                            'Indication' => $block['clinical_indication'] ?? null,
                            'Sections'   => $block['section_count'] ?? null,
                            'Controls'   => ($block['controls_required'] ?? false) ? 'Required' : null,
                        ]);
                        break;

                    case 'special_stain':
                        // Support both new array 'stains' and legacy single 'stain'
                        $stainKeys = $block['stains'] ?? (isset($block['stain']) && $block['stain'] ? [$block['stain']] : []);
                        $stainLabels = array_map(function ($k) use ($specialStainLabels, $block) {
                            return $k === 'other' && !empty($block['stain_other'])
                                ? $block['stain_other']
                                : ($specialStainLabels[$k] ?? $k);
                        }, $stainKeys);
                        $rows = array_filter([
                            'Stain(s)'   => $stainLabels ? implode(', ', $stainLabels) : null,
                            'Indication' => $block['indication'] ?? null,
                            'Sections'   => $block['section_count'] ?? null,
                        ]);
                        break;

                    case 'recut':
                        $restainVal = ($block['restain_after'] ?? false)
                            ? ($block['restain_stain'] ?: 'Yes')
                            : null;
                        $rows = array_filter([
                            'Levels'    => $block['levels'] ?? null,
                            'Thickness' => !empty($block['thickness']) ? $block['thickness'].' µm' : null,
                            'Reason'    => $block['reason'] ?? null,
                            'Re-stain'  => $restainVal,
                        ]);
                        break;

                    case 're_embedding':
                        $rows = array_filter([
                            'Reason'      => $block['reason'] ?? null,
                            'Orientation' => $block['orientation_instructions'] ?: null,
                        ]);
                        break;

                    case 'decalcification':
                        $rows = array_filter([
                            'Tissue'     => $block['tissue_type'] ?? null,
                            'Method'     => $decalcMethodLabels[$block['method'] ?? ''] ?? ($block['method'] ?? null),
                            'Est. Time'  => $block['estimated_time'] ?: null,
                            'Indication' => $block['indication'] ?? null,
                        ]);
                        break;

                    case 'fish_molecular':
                        $rows = array_filter([
                            'Probe'        => $block['probe'] ?? null,
                            'Indication'   => $block['indication'] ?? null,
                            'Send-out Lab' => $block['send_out_lab'] ?: null,
                            'Instructions' => $block['special_instructions'] ?: null,
                        ]);
                        break;
                }
            @endphp

            <div class="rounded-xl bg-surface px-3 py-2.5">
                <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">
                    {{ $prefix }}
                </p>
                @foreach($rows as $label => $value)
                    <div class="flex gap-2 py-0.5 leading-relaxed">
                        <span class="w-24 shrink-0 text-ink-muted">{{ $label }}</span>
                        <span class="min-w-0 break-words font-medium text-ink">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        @endforeach

    {{-- ── Cytology (flat structure) ─────────────────────────── --}}
    @else
        @php
            $k = $td['stain'] ?? '';
            $stainDisplay = $k === 'other' && !empty($td['stain_other'])
                ? $td['stain_other']
                : ($cytologyStainLabels[$k] ?? $k);
            $rows = array_filter([
                'Slide ID'   => $td['slide_id'] ?? null,
                'Stain'      => $stainDisplay,
                'Indication' => $td['indication'] ?? null,
            ]);
        @endphp
        <div class="rounded-xl bg-surface px-3 py-2.5">
            <p class="mb-2 text-[11px] font-semibold uppercase tracking-wide text-ink-muted">Slide</p>
            @foreach($rows as $label => $value)
                <div class="flex gap-2 py-0.5 leading-relaxed">
                    <span class="w-24 shrink-0 text-ink-muted">{{ $label }}</span>
                    <span class="min-w-0 break-words font-medium text-ink">{{ $value }}</span>
                </div>
            @endforeach
        </div>
    @endif

</div>
