{{-- VISTA GRAFICO (Chart.js) --}}
<div id="table-grafico" class="hidden">
    <div class="p-6">
        {{-- Controlli Grafico --}}
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h4 class="text-lg font-bold">Visualizzazione Grafica</h4>
                <p class="text-sm text-base-content/60">Analisi visuale dei dati sintetici per commessa e sede</p>
            </div>
            
            {{-- Tipo di Grafico --}}
            <div class="flex gap-2">
                <button onclick="changeChartType('bar')" id="chartBtn-bar" class="btn btn-sm btn-primary">
                    <x-ui.icon name="chart-bar" class="h-4 w-4" />
                    Barre
                </button>
                <button onclick="changeChartType('line')" id="chartBtn-line" class="btn btn-sm btn-outline">
                    <x-ui.icon name="chart-line" class="h-4 w-4" />
                    Linee
                </button>
                <button onclick="changeChartType('pie')" id="chartBtn-pie" class="btn btn-sm btn-outline">
                    <x-ui.icon name="chart-pie" class="h-4 w-4" />
                    Torta
                </button>
            </div>
        </div>
        
        {{-- Container Grafico --}}
        <div class="bg-base-100 rounded-lg border border-base-300 p-6" style="min-height: 500px;">
            <canvas id="produzione-chart"></canvas>
        </div>
        
        {{-- Legenda e Info --}}
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Totale Prodotto --}}
            <div class="stat bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                <div class="stat-title">Prodotto Totale</div>
                <div class="stat-value text-orange-600 dark:text-orange-400" id="chart-stat-prodotto">0</div>
                <div class="stat-desc">Somma di tutte le sedi</div>
            </div>
            
            {{-- Totale Inserito --}}
            <div class="stat bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                <div class="stat-title">Inserito Totale</div>
                <div class="stat-value text-green-600 dark:text-green-400" id="chart-stat-inserito">0</div>
                <div class="stat-desc">Somma di tutte le sedi</div>
            </div>
            
            {{-- Ore Totali --}}
            <div class="stat bg-cyan-50 dark:bg-cyan-900/20 rounded-lg border border-cyan-200 dark:border-cyan-800">
                <div class="stat-title">Ore Totali</div>
                <div class="stat-value text-cyan-600 dark:text-cyan-400" id="chart-stat-ore">0</div>
                <div class="stat-desc">Somma di tutte le sedi</div>
            </div>
        </div>
    </div>
</div>

{{-- Script Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let produzioneChart = null;
    let currentChartType = 'bar';
    
    // Dati per il grafico (estratti dalla vista sintetica)
    const chartData = {!! json_encode($datiSintetici->map(function($sediData, $cliente) {
        return $sediData->map(function($datiSede, $sede) {
            return [
                'cliente' => $datiSede['totale']['cliente_originale'] ?? $cliente,
                'sede' => $sede,
                'prodotto_pda' => $datiSede['totale']['prodotto_pda'] ?? 0,
                'inserito_pda' => $datiSede['totale']['inserito_pda'] ?? 0,
                'ko_pda' => $datiSede['totale']['ko_pda'] ?? 0,
                'backlog_pda' => $datiSede['totale']['backlog_pda'] ?? 0,
                'ore' => $datiSede['totale']['ore'] ?? 0,
                'resa_prodotto' => $datiSede['totale']['resa_prodotto'] ?? 0,
            ];
        })->values();
    })->flatten(1)) !!};
    
    function initChart() {
        const ctx = document.getElementById('produzione-chart');
        if (!ctx) return;
        
        // Prepara i dati
        const labels = chartData.map(d => `${d.cliente} - ${d.sede}`);
        const prodottoData = chartData.map(d => d.prodotto_pda);
        const inseritoData = chartData.map(d => d.inserito_pda);
        const koData = chartData.map(d => d.ko_pda);
        const oreData = chartData.map(d => d.ore);
        
        // Aggiorna statistiche
        document.getElementById('chart-stat-prodotto').textContent = prodottoData.reduce((a, b) => a + b, 0).toLocaleString();
        document.getElementById('chart-stat-inserito').textContent = inseritoData.reduce((a, b) => a + b, 0).toLocaleString();
        document.getElementById('chart-stat-ore').textContent = oreData.reduce((a, b) => a + b, 0).toFixed(2);
        
        // Distruggi grafico precedente se esiste
        if (produzioneChart) {
            produzioneChart.destroy();
        }
        
        // Configurazione in base al tipo
        let chartConfig = {
            type: currentChartType,
            data: {
                labels: labels,
                datasets: []
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'KPI Produzione per Sede e Commessa',
                        font: {
                            size: 16
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('it-IT').format(context.parsed.y || context.parsed);
                                return label;
                            }
                        }
                    }
                },
                scales: currentChartType !== 'pie' ? {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('it-IT').format(value);
                            }
                        }
                    }
                } : {}
            }
        };
        
        // Datasets in base al tipo di grafico
        if (currentChartType === 'pie') {
            // Per torta: mostra solo totali per cliente
            const clientiTotali = {};
            chartData.forEach(d => {
                if (!clientiTotali[d.cliente]) {
                    clientiTotali[d.cliente] = 0;
                }
                clientiTotali[d.cliente] += d.prodotto_pda;
            });
            
            chartConfig.data.labels = Object.keys(clientiTotali);
            chartConfig.data.datasets = [{
                label: 'Prodotto Totale per Commessa',
                data: Object.values(clientiTotali),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(199, 199, 199, 0.8)',
                ],
            }];
        } else {
            // Per barre e linee: mostra tutte le metriche
            chartConfig.data.datasets = [
                {
                    label: 'Prodotto',
                    data: prodottoData,
                    backgroundColor: 'rgba(249, 115, 22, 0.7)', // orange
                    borderColor: 'rgba(249, 115, 22, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Inserito',
                    data: inseritoData,
                    backgroundColor: 'rgba(34, 197, 94, 0.7)', // green
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 2
                },
                {
                    label: 'KO',
                    data: koData,
                    backgroundColor: 'rgba(239, 68, 68, 0.7)', // red
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 2
                }
            ];
        }
        
        // Crea il grafico
        produzioneChart = new Chart(ctx, chartConfig);
    }
    
    function changeChartType(type) {
        currentChartType = type;
        
        // Aggiorna pulsanti
        ['bar', 'line', 'pie'].forEach(t => {
            const btn = document.getElementById(`chartBtn-${t}`);
            if (t === type) {
                btn.classList.remove('btn-outline');
                btn.classList.add('btn-primary');
            } else {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline');
            }
        });
        
        // Ricrea il grafico
        initChart();
    }
    
    // Inizializza il grafico quando la vista viene caricata
    document.addEventListener('DOMContentLoaded', function() {
        // Il grafico verrà inizializzato solo quando si switcha alla vista grafico
    });
</script>

