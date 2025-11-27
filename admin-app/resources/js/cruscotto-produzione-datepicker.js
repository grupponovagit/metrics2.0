import Litepicker from 'litepicker';
import 'litepicker/dist/css/litepicker.css';
import '../css/litepicker-custom.css';

document.addEventListener('DOMContentLoaded', function() {
    const dateRangeInput = document.getElementById('dateRangePicker');
    
    if (!dateRangeInput) return;
    
    // Calcola le date per i preset
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    
    const startOfWeek = new Date(today);
    startOfWeek.setDate(today.getDate() - today.getDay() + (today.getDay() === 0 ? -6 : 1));
    
    const startOfLastWeek = new Date(startOfWeek);
    startOfLastWeek.setDate(startOfLastWeek.getDate() - 7);
    const endOfLastWeek = new Date(startOfWeek);
    endOfLastWeek.setDate(endOfLastWeek.getDate() - 1);
    
    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const startOfLastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
    const endOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0);
    
    const last7Days = new Date(today);
    last7Days.setDate(today.getDate() - 6);
    
    const last14Days = new Date(today);
    last14Days.setDate(today.getDate() - 13);
    
    const last30Days = new Date(today);
    last30Days.setDate(today.getDate() - 29);
    
    // Flag per evitare aggiornamenti durante l'inizializzazione
    let isInitializing = true;
    
    // Funzione per aggiornare gli input nascosti del form
    function updateHiddenInputs(startDate, endDate) {
        // Non aggiornare durante l'inizializzazione
        if (isInitializing) {
            console.log('⏭️ Skip update durante init');
            return;
        }
        
        const dataInizioInput = document.getElementById('data_inizio_hidden');
        const dataFineInput = document.getElementById('data_fine_hidden');
        
        if (dataInizioInput && dataFineInput && startDate && endDate) {
            const formattedStart = formatDateForBackend(startDate);
            const formattedEnd = formatDateForBackend(endDate);
            
            dataInizioInput.value = formattedStart;
            dataFineInput.value = formattedEnd;
            
            console.log('✍️ Input aggiornati:', { start: formattedStart, end: formattedEnd });
        }
    }
    
    // Funzione per formattare la data per il backend (YYYY-MM-DD)
    function formatDateForBackend(date) {
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    // Inizializza Litepicker
    const picker = new Litepicker({
        element: dateRangeInput,
        singleMode: false,
        numberOfMonths: 2,
        numberOfColumns: 2,
        format: 'DD/MM/YYYY',
        lang: 'it-IT',
        delimiter: ' – ',
        autoApply: false,
        showTooltip: false,
        mobileFriendly: true,
        buttonText: {
            apply: 'Applica',
            cancel: 'Annulla',
            previousMonth: '<',
            nextMonth: '>',
        },
        dropdowns: {
            minYear: 2020,
            maxYear: new Date().getFullYear() + 1,
            months: true,
            years: true
        },
        setup: (picker) => {
            // Event quando viene selezionato un range (aggiorna solo gli input nascosti)
            picker.on('selected', (date1, date2) => {
                updateHiddenInputs(date1, date2);
                
                // Quando selezioni manualmente una data, attiva "Personalizzato"
                const presetsContainer = picker.ui.querySelector('.litepicker-presets');
                if (presetsContainer) {
                    presetsContainer.querySelectorAll('.litepicker-preset-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    // Attiva il primo bottone (Personalizzato)
                    const customBtn = presetsContainer.querySelector('.litepicker-preset-btn');
                    if (customBtn) {
                        customBtn.classList.add('active');
                    }
                }
            });
            
            // Event per aggiungere i preset quando il picker viene mostrato
            picker.on('show', () => {
                const container = picker.ui.querySelector('.container__main');
                
                // Controlla se i preset esistono già
                if (container && !container.querySelector('.litepicker-presets')) {
                    addPresetsToCalendar(picker, container);
                }
            });
            
            // Event per ogni rendering - assicura che i preset siano sempre visibili
            picker.on('render', () => {
                const container = picker.ui.querySelector('.container__main');
                
                // Se i preset non ci sono, li ricrea
                if (container && !container.querySelector('.litepicker-presets')) {
                    addPresetsToCalendar(picker, container);
                }
                
                // Forza la visibilità
                const presetsContainer = picker.ui.querySelector('.litepicker-presets');
                if (presetsContainer) {
                    presetsContainer.style.display = 'flex';
                    presetsContainer.style.visibility = 'visible';
                    presetsContainer.style.opacity = '1';
                }
            });
        }
    });
    
    // Funzione per aggiungere i preset al calendario
    function addPresetsToCalendar(picker, container) {
        // Crea sidebar per i preset
        const presetsContainer = document.createElement('div');
        presetsContainer.className = 'litepicker-presets';
        
        const presets = [
            { label: 'Personalizzato', value: 'custom', divider: false },
            { label: 'Oggi', start: today, end: today },
            { label: 'Ieri', start: yesterday, end: yesterday },
            { label: 'Questa settimana', start: startOfWeek, end: today },
            { label: 'Ultimi 7 giorni', start: last7Days, end: today },
            { label: 'Settimana scorsa', start: startOfLastWeek, end: endOfLastWeek },
            { label: 'Ultimi 14 giorni', start: last14Days, end: today },
            { label: 'Questo mese', start: startOfMonth, end: today },
            { label: 'Ultimi 30 giorni', start: last30Days, end: today },
            { label: 'Mese scorso', start: startOfLastMonth, end: endOfLastMonth },
            { label: 'Dall\'inizio', start: new Date(2020, 0, 1), end: today }
        ];
        
        presets.forEach((preset, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'litepicker-preset-btn';
            button.textContent = preset.label;
            button.dataset.preset = preset.label;
            
            if (preset.start && preset.end) {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Rimuovi active da tutti i bottoni
                    presetsContainer.querySelectorAll('.litepicker-preset-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    
                    // Aggiungi active al bottone cliccato
                    button.classList.add('active');
                    
                    // Imposta il range
                    picker.setDateRange(preset.start, preset.end);
                    
                    // Aggiorna gli input nascosti
                    updateHiddenInputs(preset.start, preset.end);
                });
            } else if (preset.value === 'custom') {
                // Inizialmente "Personalizzato" è attivo
                button.classList.add('active');
            }
            
            presetsContainer.appendChild(button);
        });
        
        // Inserisci i preset come primo elemento del calendario
        container.style.display = 'flex';
        container.insertBefore(presetsContainer, container.firstChild);
    }
    
    // Imposta il range iniziale se presente nei parametri URL o dagli input nascosti
    const dataInizioInput = document.getElementById('data_inizio_hidden');
    const dataFineInput = document.getElementById('data_fine_hidden');
    
    let startDate = null;
    let endDate = null;
    
    console.log('🔍 DEBUG Datepicker Init:', {
        dataInizioValue: dataInizioInput?.value,
        dataFineValue: dataFineInput?.value
    });
    
    // Prova prima dai valori degli input nascosti (che vengono dal controller)
    if (dataInizioInput && dataFineInput && dataInizioInput.value && dataFineInput.value) {
        const [year1, month1, day1] = dataInizioInput.value.split('-');
        const [year2, month2, day2] = dataFineInput.value.split('-');
        startDate = new Date(year1, month1 - 1, day1);
        endDate = new Date(year2, month2 - 1, day2);
        console.log('✅ Date caricate dagli input nascosti');
    } else {
        // Fallback: usa questo mese come default
        startDate = startOfMonth;
        endDate = today;
        
        // Aggiorna anche gli input nascosti con i valori di default
        if (dataInizioInput && dataFineInput) {
            dataInizioInput.value = formatDateForBackend(startDate);
            dataFineInput.value = formatDateForBackend(endDate);
        }
        console.log('⚠️ Usati valori di default (questo mese)');
    }
    
    // Imposta il range nel picker
    picker.setDateRange(startDate, endDate);
    
    console.log('📅 Range impostato:', {
        start: formatDateForBackend(startDate),
        end: formatDateForBackend(endDate)
    });
    
    // Dopo l'inizializzazione, abilita gli aggiornamenti degli input
    setTimeout(() => {
        isInitializing = false;
        console.log('✅ Inizializzazione completata - aggiornamenti abilitati');
    }, 500);
});
