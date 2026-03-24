<?php if (count(get_included_files()) <= 1) die('Acceso denegado'); ?>
<!-- Accessible Modal para Nuevo Cliente -->
<div id="addLeadModal" role="dialog" aria-modal="true" aria-labelledby="add-modal-title" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-zinc-900 border border-zinc-800 w-full max-w-2xl rounded-xl shadow-2xl p-8 transform transition-all animate-in zoom-in duration-200" id="addLeadModalContent" tabindex="-1">
        <form action="insert" method="POST" id="addLeadForm">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="flex justify-between items-start mb-8 pb-6 border-b border-zinc-800">
                <div>
                    <h2 id="add-modal-title" class="text-2xl font-bold text-zinc-100 tracking-tight">Nuevo Registro de Cliente</h2>
                    <p class="text-[14px] text-zinc-400 mt-1">Añadir un nuevo cliente a la base de datos.</p>
                </div>
                <button type="button" onclick="toggleModal()" aria-label="Cerrar ventana" class="p-2 text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800 rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                    <i data-lucide="x" class="w-5 h-5" aria-hidden="true"></i>
                </button>
            </div>

            <div class="space-y-8">
                <fieldset class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <legend class="sr-only">Identidad</legend>
                    <div class="space-y-2">
                        <label for="name" class="block text-[13px] font-semibold text-zinc-300">Nombre del Cliente *</label>
                        <div class="relative">
                            <i data-lucide="user" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                            <input type="text" name="name" id="name" required class="w-full pl-9 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-600 shadow-inner" placeholder="Ej. Juan Pérez">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="company" class="block text-[13px] font-semibold text-zinc-300">Empresa</label>
                        <div class="relative">
                            <i data-lucide="building" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                            <input type="text" name="company" id="company" class="w-full pl-9 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-600 shadow-inner" placeholder="Opcional">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <legend class="block text-[13px] font-semibold text-zinc-300 mb-2">Datos de Contacto</legend>
                    <div class="col-span-1 md:col-span-2 space-y-2">
                        <label for="phone" class="block text-[13px] font-semibold text-zinc-300 sr-only">Número de Teléfono *</label>
                        <div class="relative">
                            <i data-lucide="phone" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                            <input type="tel" name="phone" id="phone" required class="w-full pl-9 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-600 shadow-inner" placeholder="Teléfono preferido">
                        </div>
                    </div>
                    <div class="col-span-1 md:col-span-2 space-y-2 mt-4">
                        <label for="email" class="block text-[13px] font-semibold text-zinc-300 sr-only">Correo Electrónico</label>
                        <div class="relative">
                            <i data-lucide="mail" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                            <input type="email" name="email" id="email" class="w-full pl-9 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] text-zinc-100 placeholder:text-zinc-600 shadow-inner" placeholder="Ej. correo@ejemplo.com">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <legend class="block text-[13px] font-semibold text-zinc-300 mb-2">Información de la Propuesta</legend>
                    <div class="col-span-1 space-y-2">
                        <label for="proposal_price" class="sr-only">Valor (€)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500 text-[14px]" aria-hidden="true">€</span>
                            <input type="number" step="0.01" name="proposal_price" id="proposal_price" class="w-full pl-8 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] text-zinc-100 font-medium shadow-inner" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-span-1 space-y-2">
                        <label for="status" class="sr-only">Estado Inicial</label>
                        <div class="relative">
                            <select name="status" id="status" class="w-full pl-3 pr-8 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-colors text-[14px] text-zinc-100 appearance-none shadow-inner">
                                <option value="nuevo">Nuevo</option>
                                <option value="enviar_propuesta">Enviar Propuesta</option>
                                <option value="ganado">Ganado</option>
                            </select>
                            <i data-lucide="chevron-down" class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 pointer-events-none" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="col-span-1 space-y-2">
                        <span class="block text-[13px] font-semibold text-zinc-300 mb-2">Fuente de Adquisición</span>
                        <div class="flex gap-2" role="radiogroup" aria-label="Fuente de Adquisición">
                            <label class="flex-1 relative cursor-pointer group">
                                <input type="radio" name="source" value="pago" class="sr-only peer" checked>
                                <span class="flex items-center justify-center p-2.5 text-[13px] font-medium rounded-md border border-zinc-800 bg-zinc-950 text-zinc-400 peer-checked:bg-zinc-100 peer-checked:text-zinc-950 peer-checked:border-zinc-100 transition-colors peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500">Pago</span>
                            </label>
                            <label class="flex-1 relative cursor-pointer group">
                                <input type="radio" name="source" value="organico" class="sr-only peer">
                                <span class="flex items-center justify-center p-2.5 text-[13px] font-medium rounded-md border border-zinc-800 bg-zinc-950 text-zinc-400 peer-checked:bg-zinc-100 peer-checked:text-zinc-950 peer-checked:border-zinc-100 transition-colors peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500">Orgánico</span>
                            </label>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="text-[15px] font-bold text-zinc-100 border-b border-zinc-800 pb-2 w-full mb-4">Nota de Voz (Opcional)</legend>
                    <div class="flex items-center gap-4 bg-zinc-950 p-4 rounded-lg border border-zinc-800">
                        <button type="button" id="recordBtn" aria-label="Iniciar grabación" class="flex items-center justify-center w-12 h-12 bg-zinc-800 border border-zinc-700 hover:bg-indigo-600 hover:text-white rounded-full text-zinc-400 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500" onclick="toggleRecording()">
                            <i data-lucide="mic" class="w-5 h-5" id="mic-icon" aria-hidden="true"></i>
                        </button>
                        <div class="flex-1">
                            <div class="h-2 bg-zinc-800 rounded-full overflow-hidden" aria-hidden="true">
                                <div id="recordingProgress" class="h-full bg-indigo-500 w-0 transition-all duration-100"></div>
                            </div>
                            <p id="audio-status" class="text-[12px] text-zinc-500 font-medium mt-2" aria-live="polite">Listo para grabar</p>
                        </div>
                        <input type="hidden" name="audio_data" id="audioData">
                        <input type="hidden" name="audio_duration" id="audioDuration">
                    </div>
                </fieldset>
            </div>

            <div class="mt-8 pt-6 border-t border-zinc-800 flex justify-end gap-3">
                <button type="button" onclick="toggleModal()" class="px-5 py-2.5 text-zinc-400 text-[14px] font-semibold rounded-md hover:text-zinc-100 hover:bg-zinc-800 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-zinc-500">
                    Cancelar
                </button>
                <button type="submit" id="submitBtn" class="px-6 py-2.5 bg-zinc-100 text-zinc-950 text-[14px] font-bold rounded-md hover:bg-zinc-300 transition-colors flex items-center justify-center gap-2 min-w-[140px] focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                    Crear Registro
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let mediaRecorder;
    let audioChunks = [];
    let startTime;
    let timerInterval;
    let isRecording = false;
    let seconds = 0;
    let recordingInterval;

    let triggerBtn = null; // To restore focus when modal closes

    function toggleModal() {
        const modal = document.getElementById('addLeadModal');
        const isHidden = modal.classList.contains('hidden');
        
        if (isHidden) {
            triggerBtn = document.activeElement;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
            setTimeout(() => { document.getElementById('name').focus(); }, 50);
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            if (triggerBtn) triggerBtn.focus();
        }
    }

    const recordBtn = document.getElementById('recordBtn');
    const micIcon = document.getElementById('mic-icon');
    const audioStatus = document.getElementById('audio-status');
    const recordingProgress = document.getElementById('recordingProgress');
    // Removed audioDataInput and audioDurationInput from global scope as they are not used directly anymore

    async function toggleRecording() {
        if (!isRecording) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                mediaRecorder.ondataavailable = event => {
                    audioChunks.push(event.data);
                };
                // mediaRecorder.onstop logic is now handled in the form submit
                mediaRecorder.start();
                isRecording = true;
                
                recordBtn.classList.remove('bg-zinc-800', 'border-zinc-700', 'hover:bg-indigo-600', 'hover:text-white', 'text-zinc-400');
                recordBtn.classList.add('bg-red-500', 'text-white', 'border-red-500', 'animate-pulse');
                micIcon.setAttribute('data-lucide', 'square');
                recordBtn.setAttribute('aria-label', 'Detener grabación');
                lucide.createIcons();
                
                audioStatus.textContent = 'Grabando...';
                seconds = 0;
                recordingProgress.style.width = '0%';
                
                recordingInterval = setInterval(() => {
                    seconds++;
                    const progressWidth = Math.min(100, (seconds / 60) * 100); // Max 60 seconds
                    recordingProgress.style.width = `${progressWidth}%`;
                    if (seconds >= 60) {
                        toggleRecording(); // Stop recording after 60 seconds
                    }
                }, 1000);
            } catch (err) {
                alert('Acceso al micrófono denegado o no disponible.');
            }
        } else {
            mediaRecorder.stop();
            isRecording = false;
            clearInterval(recordingInterval);
            
            recordBtn.classList.remove('bg-red-500', 'text-white', 'border-red-500', 'animate-pulse');
            recordBtn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
            micIcon.setAttribute('data-lucide', 'check');
            recordBtn.setAttribute('aria-label', 'Grabado');
            lucide.createIcons();
            
            audioStatus.textContent = `Audio grabado (${seconds}s)`;
            // audioDurationInput.value = seconds; // This will be set in formData directly
        }
    }

    document.getElementById('addLeadForm').onsubmit = async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Creando...';
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        lucide.createIcons();

        const formData = new FormData(e.target);
        if (audioChunks.length > 0) {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            formData.append('audio_file', audioBlob, 'capture.webm');
            formData.append('audio_duration', seconds); // Add duration to formData
        }

        try {
            const res = await fetch('insert.php', { method: 'POST', body: formData });
            
            const contentType = res.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                const data = await res.json();
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.innerHTML = 'Crear Registro';
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            } else {
                // If not JSON, assume success and reload, or handle as generic error
                location.reload();
            }
        } catch (err) {
            alert('A network error occurred.');
            submitBtn.innerHTML = 'Crear Registro';
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    };

    // Close Add modal on ESC
    window.addEventListener('keydown', (e) => {
        const detailModalOpen = document.getElementById('detailModal') && !document.getElementById('detailModal').classList.contains('hidden');
        if(e.key === 'Escape' && !detailModalOpen) {
            const modal = document.getElementById('addLeadModal');
            if(!modal.classList.contains('hidden')) {
                toggleModal();
            }
        }
    });
</script>
