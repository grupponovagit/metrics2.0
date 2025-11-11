{{-- VISTA GRAFICO (Chart.js) --}}
<div id="table-grafico" class="hidden">
    <div class="p-6">
        {{-- Controlli Grafico --}}
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h4 class="text-lg font-bold">Visualizzazione Grafica</h4>
                <p class="text-sm text-base-content/60">Analisi visuale dei dati per commessa e sede</p>
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
                <button onclick="changeChartType('daily')" id="chartBtn-daily" class="btn btn-sm btn-outline">
                    <x-ui.icon name="calendar" class="h-4 w-4" />
                    Giornaliero
                </button>
                <button onclick="changeChartType('pie')" id="chartBtn-pie" class="btn btn-sm btn-outline">
                    <x-ui.icon name="chart-pie" class="h-4 w-4" />
                    Torte per Sede
                </button>
            </div>
        </div>
        
        {{-- Container Grafico --}}
        <div class="bg-base-100 rounded-lg border border-base-300 p-6" style="min-height: 500px;">
            <canvas id="produzione-chart"></canvas>
        </div>
        
        {{-- Container per le 3 torte (visibile solo in modalità pie) --}}
        <div id="pie-charts-container" class="hidden mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-base-100 rounded-lg border border-base-300 p-4">
                <h5 class="text-center font-bold mb-2">Prodotto per Sede</h5>
                <canvas id="pie-prodotto"></canvas>
            </div>
            <div class="bg-base-100 rounded-lg border border-base-300 p-4">
                <h5 class="text-center font-bold mb-2">Inserito per Sede</h5>
                <canvas id="pie-inserito"></canvas>
            </div>
            <div class="bg-base-100 rounded-lg border border-base-300 p-4">
                <h5 class="text-center font-bold mb-2">KO per Sede</h5>
                <canvas id="pie-ko"></canvas>
            </div>
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
    let pieCharts = { inserito: null, ko: null, prodotto: null };
    let currentChartType = 'bar';
    
    // Dati per il grafico sintetico (estratti dalla vista sintetica)
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
    
    // Dati giornalieri per il grafico a linee per giorno
    const dailyData = {!! json_encode($datiGiornalieri->groupBy('data')->map(function($giornoData) {
        return [
            'data' => $giornoData[0]['data'],
            'inserito_pda' => $giornoData->sum('inserito_pda'),
            'prodotto_pda' => $giornoData->sum('prodotto_pda'),
            'ko_pda' => $giornoData->sum('ko_pda'),
        ];
    })->sortBy('data')->values()) !!};
    
    // Palette di colori per le torte
    const pieColors = [
        'rgba(255, 99, 132, 0.8)',   // rosso
        'rgba(54, 162, 235, 0.8)',   // blu
        'rgba(255, 206, 86, 0.8)',   // giallo
        'rgba(75, 192, 192, 0.8)',   // teal
        'rgba(153, 102, 255, 0.8)',  // viola
        'rgba(255, 159, 64, 0.8)',   // arancione
        'rgba(199, 199, 199, 0.8)',  // grigio
        'rgba(83, 102, 255, 0.8)',   // blu scuro
        'rgba(255, 99, 255, 0.8)',   // magenta
        'rgba(99, 255, 132, 0.8)',   // verde chiaro
    ];
    
    function initChart() {
        const ctx = document.getElementById('produzione-chart');
        if (!ctx) return;
        
        // Nascondi/Mostra le torte in base al tipo
        const pieChartsContainer = document.getElementById('pie-charts-container');
        const mainChartContainer = ctx.parentElement;
        
        if (currentChartType === 'pie') {
            mainChartContainer.style.display = 'none';
            pieChartsContainer.classList.remove('hidden');
            initPieCharts();
            updateStats();
            return;
        } else {
            mainChartContainer.style.display = 'block';
            pieChartsContainer.classList.add('hidden');
        }
        
        // Distruggi grafico precedente se esiste
        if (produzioneChart) {
            produzioneChart.destroy();
        }
        
        // Configurazione in base al tipo
        let chartConfig = {
            type: currentChartType === 'daily' ? 'line' : currentChartType,
            data: {
                labels: [],
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
                        text: 'KPI Produzione',
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
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('it-IT').format(value);
                            }
                        }
                    }
                }
            }
        };
        
        // Datasets in base al tipo di grafico
        if (currentChartType === 'daily') {
            // Grafico giornaliero: Inserito, Prodotto e KO per giorno
            chartConfig.options.plugins.title.text = 'Andamento Giornaliero: Inserito, Prodotto e KO';
            chartConfig.data.labels = dailyData.map(d => {
                const date = new Date(d.data);
                return date.toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit' });
            });
            chartConfig.data.datasets = [
                {
                    label: 'Inserito',
                    data: dailyData.map(d => d.inserito_pda),
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Prodotto',
                    data: dailyData.map(d => d.prodotto_pda),
                    backgroundColor: 'rgba(249, 115, 22, 0.2)',
                    borderColor: 'rgba(249, 115, 22, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'KO',
                    data: dailyData.map(d => d.ko_pda),
                    backgroundColor: 'rgba(239, 68, 68, 0.2)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }
            ];
        } else {
            // Per barre e linee: mostra tutte le metriche per sede
            chartConfig.options.plugins.title.text = 'KPI Produzione per Sede e Commessa';
            const labels = chartData.map(d => `${d.cliente} - ${d.sede}`);
            const prodottoData = chartData.map(d => d.prodotto_pda);
            const inseritoData = chartData.map(d => d.inserito_pda);
            const koData = chartData.map(d => d.ko_pda);
            
            chartConfig.data.labels = labels;
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
        updateStats();
    }
    
    function initPieCharts() {
        // Distruggi grafici precedenti
        Object.values(pieCharts).forEach(chart => {
            if (chart) chart.destroy();
        });
        
        // Prepara dati per sede
        const sediData = {};
        chartData.forEach(d => {
            const sede = d.sede;
            if (!sediData[sede]) {
                sediData[sede] = {
                    inserito: 0,
                    ko: 0,
                    prodotto: 0
                };
            }
            sediData[sede].inserito += d.inserito_pda;
            sediData[sede].ko += d.ko_pda;
            sediData[sede].prodotto += d.prodotto_pda;
        });
        
        const sedi = Object.keys(sediData);
        const colors = pieColors.slice(0, sedi.length);
        
        // Torta Prodotto (prima)
        pieCharts.prodotto = new Chart(document.getElementById('pie-prodotto'), {
            type: 'pie',
            data: {
                labels: sedi,
                datasets: [{
                    label: 'Prodotto',
                    data: sedi.map(s => sediData[s].prodotto),
                    backgroundColor: colors,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = new Intl.NumberFormat('it-IT').format(context.parsed);
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Torta Inserito (seconda)
        pieCharts.inserito = new Chart(document.getElementById('pie-inserito'), {
            type: 'pie',
            data: {
                labels: sedi,
                datasets: [{
                    label: 'Inserito',
                    data: sedi.map(s => sediData[s].inserito),
                    backgroundColor: colors,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = new Intl.NumberFormat('it-IT').format(context.parsed);
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Torta KO (terza)
        pieCharts.ko = new Chart(document.getElementById('pie-ko'), {
            type: 'pie',
            data: {
                labels: sedi,
                datasets: [{
                    label: 'KO',
                    data: sedi.map(s => sediData[s].ko),
                    backgroundColor: colors,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = new Intl.NumberFormat('it-IT').format(context.parsed);
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    function updateStats() {
        // Aggiorna statistiche
        const prodottoData = chartData.map(d => d.prodotto_pda);
        const inseritoData = chartData.map(d => d.inserito_pda);
        const oreData = chartData.map(d => d.ore);
        
        document.getElementById('chart-stat-prodotto').textContent = prodottoData.reduce((a, b) => a + b, 0).toLocaleString();
        document.getElementById('chart-stat-inserito').textContent = inseritoData.reduce((a, b) => a + b, 0).toLocaleString();
        document.getElementById('chart-stat-ore').textContent = oreData.reduce((a, b) => a + b, 0).toFixed(2);
    }
    
    function changeChartType(type) {
        currentChartType = type;
        
        // Aggiorna pulsanti
        ['bar', 'line', 'daily', 'pie'].forEach(t => {
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

