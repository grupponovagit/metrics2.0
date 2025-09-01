@php
    use App\Services\ModuleAccessService;
    $accessibleModules = ModuleAccessService::getAccessibleModules();
    $currentRoute = request()->route()->getName();
@endphp

<div class="mb-4">
    <p class="text-sm font-semibold text-base-content/70 uppercase tracking-wide px-4 mb-2">
        Moduli Aziendali
    </p>
    
    @if(count($accessibleModules) > 0)
        @foreach($accessibleModules as $module)
            @php
                $isActive = str_starts_with($currentRoute, 'admin.' . $module['key']);
                $iconMap = [
                    'home' => 'house',
                    'hr' => 'users',
                    'amministrazione' => 'calculator',
                    'produzione' => 'industry',
                    'marketing' => 'bullhorn',
                    'ict' => 'desktop'
                ];
                $icon = $iconMap[$module['key']] ?? 'circle';
            @endphp
            
            <li>
                <a href="{{ $module['url'] }}" 
                   class="{{ $isActive ? 'active bg-primary text-primary-content' : '' }} flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-base-200 transition-colors">
                    <x-admin.fa-icon name="{{ $icon }}" class="h-4 w-4" />
                    <span class="font-medium">{{ $module['name'] }}</span>
                    
                    @if(count($module['permissions']) > 0)
                        <div class="badge badge-sm badge-outline ml-auto">
                            {{ count($module['permissions']) }}
                        </div>
                    @endif
                </a>
                
                {{-- Sottomenu per modulo attivo --}}
                @if($isActive && $module['key'] !== 'home')
                    <ul class="ml-6 mt-2 space-y-1">
                        @if(in_array('view', $module['permissions']))
                            <li>
                                <a href="{{ route('admin.' . $module['key'] . '.index') }}" 
                                   class="text-sm text-base-content/70 hover:text-base-content block py-1">
                                    Dashboard
                                </a>
                            </li>
                        @endif
                        
                        @switch($module['key'])
                            @case('hr')
                                @if(in_array('view', $module['permissions']))
                                    <li><a href="{{ route('admin.hr.employees') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Dipendenti</a></li>
                                @endif
                                @if(in_array('reports', $module['permissions']))
                                    <li><a href="{{ route('admin.hr.reports') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Report</a></li>
                                @endif
                                @break
                                
                            @case('amministrazione')
                                @if(in_array('view', $module['permissions']))
                                    <li><a href="{{ route('admin.amministrazione.invoices') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Fatture</a></li>
                                    <li><a href="{{ route('admin.amministrazione.budget') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Budget</a></li>
                                @endif
                                @if(in_array('reports', $module['permissions']))
                                    <li><a href="{{ route('admin.amministrazione.reports') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Report</a></li>
                                @endif
                                @break
                                
                            @case('produzione')
                                @if(in_array('view', $module['permissions']))
                                    <li><a href="{{ route('admin.produzione.orders') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Ordini</a></li>
                                    <li><a href="{{ route('admin.produzione.quality') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Qualità</a></li>
                                @endif
                                @if(in_array('reports', $module['permissions']))
                                    <li><a href="{{ route('admin.produzione.reports') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Report</a></li>
                                @endif
                                @break
                                
                            @case('marketing')
                                @if(in_array('view', $module['permissions']))
                                    <li><a href="{{ route('admin.marketing.campaigns') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Campagne</a></li>
                                    <li><a href="{{ route('admin.marketing.leads') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Lead</a></li>
                                @endif
                                @if(in_array('reports', $module['permissions']))
                                    <li><a href="{{ route('admin.marketing.reports') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Report</a></li>
                                @endif
                                @break
                                
                            @case('ict')
                                @if(in_array('view', $module['permissions']))
                                    <li><a href="{{ route('admin.ict.system') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Sistema</a></li>
                                    <li><a href="{{ route('admin.ict.tickets') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Ticket</a></li>
                                @endif
                                @if(in_array('admin', $module['permissions']))
                                    <li><a href="{{ route('admin.ict.users') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Utenti</a></li>
                                    <li><a href="{{ route('admin.ict.security') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Sicurezza</a></li>
                                @endif
                                @if(in_array('reports', $module['permissions']))
                                    <li><a href="{{ route('admin.ict.reports') }}" class="text-sm text-base-content/70 hover:text-base-content block py-1">Report</a></li>
                                @endif
                                @break
                        @endswitch
                    </ul>
                @endif
            </li>
        @endforeach
    @else
        <li class="px-4 py-2 text-sm text-base-content/50">
            Nessun modulo accessibile
        </li>
    @endif
</div>
