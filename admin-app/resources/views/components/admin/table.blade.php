@props([
    'headers' => [],              // Array di header: ['label' => 'Nome', 'class' => '', 'sortable' => false]
    'rows' => [],                 // Array di array per le righe
    'minWidth' => '1200px',       // Larghezza minima tabella (default: 1200px)
    'maxHeight' => '60vh',        // Altezza massima per scroll verticale (default: 60vh)
    'striped' => true,            // Righe alternate
    'hover' => true,              // Hover effect
    'stickyHeader' => true,       // Header fisso durante scroll verticale
])

@php
/**
 * Scrollable Table Component
 * 
 * Features:
 * - Responsive: si adatta alla viewport disponibile
 * - Scroll orizzontale: attivato automaticamente se tabella > viewport
 * - Scroll verticale: configurabile con maxHeight
 * - Header sticky: rimane visibile durante scroll
 * - Touch-friendly: scroll ottimizzato per mobile
 */

$tableClass = collect([
    'table',
    'whitespace-nowrap',
    'w-full',
    $striped ? 'table-zebra' : '',
])->filter()->join(' ');

$maxHeightClass = "max-h-[{$maxHeight}]";
$minWidthClass = "min-w-[{$minWidth}]";
$headerStickyClass = $stickyHeader ? 'sticky top-0 z-10' : '';
@endphp

{{-- Wrapper con scroll responsive --}}
<div class="table-scroll-container {{ $maxHeightClass }}">
    <table class="{{ $tableClass }} {{ $minWidthClass }}">
        
        {{-- Header --}}
        <thead class="bg-gradient-to-r from-primary/10 to-secondary/10 {{ $headerStickyClass }}">
            <tr>
                @foreach($headers as $header)
                    <th class="py-4 px-6 font-bold text-sm uppercase tracking-wider text-base-content border-b-2 border-base-300 {{ $header['class'] ?? '' }}">
                        <div class="flex items-center gap-2 {{ str_contains($header['class'] ?? '', 'text-right') ? 'justify-end' : '' }}">
                            <span>{{ $header['label'] }}</span>
                            @if(isset($header['sortable']) && $header['sortable'])
                                <div class="flex flex-col opacity-50 hover:opacity-100 transition-opacity">
                                    <svg class="w-3 h-3 text-base-content" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </th>
                @endforeach
            </tr>
        </thead>

        {{-- Body --}}
        <tbody class="bg-base-100">
            @forelse($rows as $index => $row)
                <tr class="border-b border-base-200 {{ $hover ? 'hover:bg-base-200/50 transition-colors duration-150' : '' }}">
                    @foreach($row as $cell)
                        <td class="py-4 px-6 {{ is_array($cell) && isset($cell['class']) ? $cell['class'] : '' }}">
                            @if(is_array($cell) && isset($cell['content']))
                                {!! $cell['content'] !!}
                            @else
                                {{ $cell }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="py-16 text-center bg-base-100">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 bg-base-200 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-base-content/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-base-content mb-1">Nessun dato disponibile</h3>
                                <p class="text-sm text-base-content/60">Non ci sono record da visualizzare in questa tabella</p>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Hint scroll mobile --}}
@if(count($rows) > 0)
<div class="mt-2 text-center lg:hidden">
    <span class="text-xs text-base-content/50 inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
        </svg>
        Scorri per vedere tutte le colonne
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
        </svg>
    </span>
</div>
@endif

