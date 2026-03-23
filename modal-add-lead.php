<!-- Modal Backdrop -->
<div id="addLeadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md transition-all overscroll-y-contain">
    
    <!-- Modal Content (Scrollable for more fields) -->
    <div class="relative w-full max-w-xl p-10 bg-zinc-900 border border-zinc-800 rounded-[32px] shadow-2xl animate-in zoom-in-95 duration-200 overflow-y-auto max-h-[90vh]">
        
        <button onclick="toggleModal()" class="absolute top-8 right-8 text-zinc-600 hover:text-white transition-colors">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>

        <div class="mb-10 text-center">
            <div class="w-16 h-16 bg-blue-600/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-blue-500/20">
                <i data-lucide="shield-plus" class="w-8 h-8 text-blue-500"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-white mb-2 tracking-tight">Registrar Lead</h2>
            <p class="text-zinc-500 text-sm">Captura todos los detalles estratégicos del lead.</p>
        </div>

        <form id="modalLeadForm" class="space-y-6">
            <!-- Sección 1: Información Básica -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Nombre Lead</label>
                    <input type="text" name="name" placeholder="Juán Pérez" required
                           class="block w-full px-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium">
                </div>
                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Email Principal</label>
                    <input type="email" name="email" placeholder="juan@gmail.com" required
                           class="block w-full px-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium">
                </div>
            </div>

            <!-- Sección 2: Detalles de Empresa -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 py-4 border-t border-zinc-800/50">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Nombre Empresa</label>
                    <input type="text" name="company" placeholder="Ej: Tech Corp"
                           class="block w-full px-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Sitio Web</label>
                    <input type="url" name="website" placeholder="https://..."
                           class="block w-full px-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium text-sm">
                </div>
            </div>

            <!-- Sección 3: Precio y Fuente (Selector Orgánico/Pago) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 py-4 border-y border-zinc-800/50">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Precio Propuesta (€)</label>
                    <input type="number" step="0.01" name="proposal_price" placeholder="0.00"
                           class="block w-full px-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-bold text-xl">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Fuente de Tráfico</label>
                    <div class="flex gap-2">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="source" value="organico" class="hidden peer" checked>
                            <div class="p-4 text-center bg-zinc-950/50 border border-zinc-800 rounded-2xl text-[11px] font-bold text-zinc-600 peer-checked:bg-blue-600/10 peer-checked:border-blue-500 peer-checked:text-blue-500 transition-all">ORGÁNICO</div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="source" value="pago" class="hidden peer">
                            <div class="p-4 text-center bg-zinc-950/50 border border-zinc-800 rounded-2xl text-[11px] font-bold text-zinc-600 peer-checked:bg-blue-600/10 peer-checked:border-blue-500 peer-checked:text-blue-500 transition-all">PAGO</div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sección 4: Etiquetas (Diseño Píldora) -->
            <div class="space-y-4">
                <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Etiquetas del Lead</label>
                <div class="flex flex-wrap gap-3">
                    <label class="cursor-pointer group">
                        <input type="checkbox" name="tags[]" value="metaads" class="hidden peer">
                        <div class="px-5 py-2.5 rounded-2xl border border-zinc-800 text-sm font-bold text-zinc-600 bg-zinc-950/50 peer-checked:bg-blue-600/10 peer-checked:border-blue-500 peer-checked:text-blue-500 transition-all hover:border-zinc-700 underline underline-offset-4 decoration-blue-500/0 peer-checked:decoration-blue-500/100">Metaads</div>
                    </label>
                    <label class="cursor-pointer group">
                        <input type="checkbox" name="tags[]" value="arquitectos" class="hidden peer">
                        <div class="px-5 py-2.5 rounded-2xl border border-zinc-800 text-sm font-bold text-zinc-600 bg-zinc-950/50 peer-checked:bg-blue-600/10 peer-checked:border-blue-500 peer-checked:text-blue-500 transition-all hover:border-zinc-700 underline underline-offset-4 decoration-blue-500/0 peer-checked:decoration-blue-500/100">Arquitectos</div>
                    </label>
                </div>
            </div>

            <!-- Sección 5: Grabador de Llamada Profesional -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Estación de Grabación</label>
                    <select id="micSelect" class="text-[9px] bg-zinc-950 border border-zinc-800 rounded-lg px-2 py-1 text-zinc-500 outline-none focus:border-blue-500 transition-colors max-w-[150px] truncate"></select>
                </div>
                
                <div class="relative bg-zinc-950/40 p-6 border border-zinc-800 rounded-[2rem] overflow-hidden group">
                    <!-- Visualizer Canvas -->
                    <canvas id="visualizer" class="absolute inset-0 w-full h-full opacity-30 pointer-events-none"></canvas>
                    
                    <div class="relative z-10 flex items-center gap-6">
                        <button type="button" id="recordBtn" onclick="toggleRecording()" class="w-16 h-16 bg-red-600 hover:bg-red-500 text-white rounded-full flex items-center justify-center transition-all shadow-xl active:scale-95 shadow-red-600/20 group-hover:scale-105">
                            <i data-lucide="mic" id="micIcon" class="w-7 h-7"></i>
                        </button>
                        
                        <div class="flex-1">
                            <div id="recordingStatus" class="text-xs font-black text-zinc-600 uppercase tracking-widest mb-1">Micro listo</div>
                            <div id="recordingTimer" class="text-2xl font-mono font-bold text-white tabular-nums">00:00</div>
                        </div>

                        <div class="flex items-center gap-3">
                            <audio id="audioPreview" controls class="hidden max-h-8 scale-90 opacity-80"></audio>
                            <!-- Botón Descartar (Papelera) -->
                            <button type="button" id="discardBtn" onclick="discardAudio()" class="hidden p-3 bg-zinc-800 hover:bg-red-600/20 text-zinc-500 hover:text-red-500 rounded-2xl border border-zinc-700 transition-all active:scale-90" title="Descartar audio">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección 6: Subida de Archivos -->
            <div class="space-y-2 pt-2">
                <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Documentos Adicionales (Máx. 150MB)</label>
                <div class="relative group">
                    <input type="file" name="lead_file" id="lead_file"
                           class="block w-full text-xs text-zinc-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-blue-600/10 file:text-blue-500 hover:file:bg-blue-600/20 transition-all cursor-pointer border border-zinc-800 p-3 rounded-2xl group-hover:border-blue-500/30">
                </div>
            </div>

            <button type="submit" id="modalSubmitBtn" class="w-full py-5 px-6 bg-blue-600 hover:bg-blue-500 text-white text-base font-black rounded-2xl shadow-xl shadow-blue-600/20 flex items-center justify-center gap-3 active:scale-95 transition-all">
                <span>Finalizar Registro</span>
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </button>
        </form>

        <div id="modalStatusMessage" class="hidden mt-6 p-4 rounded-2xl text-center font-bold text-sm tracking-widest uppercase"></div>
    </div>
</div>

<script>
    let mediaRecorder;
    let audioChunks = [];
    let audioBlob;
    let isRecording = false;
    let timerInterval;
    let audioContext;
    let analyser;
    let animationId;

    // Cargar micrófonos disponibles
    async function setupMics() {
        const select = document.getElementById('micSelect');
        try {
            await navigator.mediaDevices.getUserMedia({ audio: true });
            const devices = await navigator.mediaDevices.enumerateDevices();
            select.innerHTML = '';
            devices.filter(d => d.kind === 'audioinput').forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.deviceId;
                opt.text = d.label || `Micro ${select.length + 1}`;
                select.appendChild(opt);
            });
        } catch(e) { console.error("Error cargando mics", e); }
    }

    function toggleModal() {
        const modal = document.getElementById('addLeadModal');
        modal.classList.toggle('hidden');
        if(!modal.classList.contains('hidden')) {
            document.getElementById('modalStatusMessage').classList.add('hidden');
            document.getElementById('modalLeadForm').reset();
            setupMics();
            resetAudioUI();
        }
    }

    function discardAudio() {
        if(confirm('¿Seguro que quieres descartar este audio?')) {
            resetAudioUI();
        }
    }

    function resetAudioUI() {
        stopRecording();
        audioBlob = null;
        audioChunks = [];
        document.getElementById('micIcon').innerHTML = '<i data-lucide="mic"></i>';
        document.getElementById('recordBtn').classList.remove('bg-zinc-800');
        document.getElementById('recordBtn').classList.add('bg-red-600');
        document.getElementById('recordingStatus').textContent = 'Micro listo';
        document.getElementById('recordingStatus').className = 'text-xs font-black text-zinc-600 uppercase tracking-widest mb-1';
        document.getElementById('recordingTimer').textContent = '00:00';
        document.getElementById('audioPreview').classList.add('hidden');
        document.getElementById('discardBtn').classList.add('hidden');
        if(animationId) cancelAnimationFrame(animationId);
        const canvas = document.getElementById('visualizer');
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0,0, canvas.width, canvas.height);
        lucide.createIcons();
    }

    function startTimer() {
        let sec = 0;
        timerInterval = setInterval(() => {
            sec++;
            const m = Math.floor(sec / 60).toString().padStart(2, '0');
            const s = (sec % 60).toString().padStart(2, '0');
            document.getElementById('recordingTimer').textContent = `${m}:${s}`;
        }, 1000);
    }

    function drawVisualizer() {
        const canvas = document.getElementById('visualizer');
        const ctx = canvas.getContext('2d');
        const bufferLength = analyser.frequencyBinCount;
        const dataArray = new Uint8Array(bufferLength);

        const draw = () => {
            animationId = requestAnimationFrame(draw);
            analyser.getByteFrequencyData(dataArray);
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#0066FF';
            const barWidth = (canvas.width / bufferLength) * 2.5;
            let barHeight;
            let x = 0;
            for(let i = 0; i < bufferLength; i++) {
                barHeight = dataArray[i] / 2;
                ctx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
                x += barWidth + 1;
            }
        };
        draw();
    }

    async function toggleRecording() {
        if (!isRecording && audioBlob) {
            if(!confirm('Ya tienes una grabación. ¿Quieres borrarla y empezar de nuevo?')) {
                return;
            }
            resetAudioUI();
        }

        const status = document.getElementById('recordingStatus');
        const preview = document.getElementById('audioPreview');
        const btn = document.getElementById('recordBtn');
        const discBtn = document.getElementById('discardBtn');
        const deviceId = document.getElementById('micSelect').value;

        if (!isRecording) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: { deviceId: { exact: deviceId } } });
                
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                analyser = audioContext.createAnalyser();
                const source = audioContext.createMediaStreamSource(stream);
                source.connect(analyser);
                analyser.fftSize = 256;
                drawVisualizer();

                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
                mediaRecorder.onstop = () => {
                    audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    preview.src = URL.createObjectURL(audioBlob);
                    preview.classList.remove('hidden');
                    discBtn.classList.remove('hidden');
                    status.textContent = 'Grabación finalizada';
                    lucide.createIcons();
                };

                mediaRecorder.start();
                startTimer();
                isRecording = true;
                btn.classList.replace('bg-red-600', 'bg-zinc-800');
                status.className = 'text-xs font-black text-red-500 uppercase tracking-widest mb-1 animate-pulse';
                status.textContent = 'GRABANDO...';
            } catch (err) { alert('Micro no disponible: ' + err); }
        } else {
            stopRecording();
        }
    }

    function stopRecording() {
        if(mediaRecorder && isRecording) mediaRecorder.stop();
        if(timerInterval) clearInterval(timerInterval);
        isRecording = false;
        document.getElementById('recordBtn').classList.replace('bg-zinc-800', 'bg-red-600');
        document.getElementById('recordingStatus').className = 'text-xs font-black text-zinc-500 uppercase tracking-widest mb-1';
        if(audioContext) {
            audioContext.close();
            audioContext = null;
        }
    }



    const modalForm = document.getElementById('modalLeadForm');
    const modalSubmitBtn = document.getElementById('modalSubmitBtn');
    const modalStatus = document.getElementById('modalStatusMessage');

    modalForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        modalSubmitBtn.disabled = true;
        modalSubmitBtn.innerHTML = 'Enviando Datos y Audio...';

        const formData = new FormData(modalForm);
        
        // Adjuntar grabación de audio si existe
        if (audioBlob) {
            formData.append('audio_file', audioBlob, 'record.webm');
        }

        try {
            const response = await fetch('insert.php', {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();

            if (result.status === 'success') {
                modalStatus.textContent = 'REGISTRO GUARDADO';
                modalStatus.className = 'mt-6 p-4 rounded-2xl text-center font-bold bg-green-500/10 text-green-500 border border-green-500/20 text-[10px] tracking-widest';
                modalStatus.classList.remove('hidden');
                setTimeout(() => { location.reload(); }, 1200);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            modalStatus.textContent = error.message;
            modalStatus.className = 'mt-6 p-4 rounded-2xl text-center font-bold bg-red-500/10 text-red-500 border border-red-500/20 text-[10px] tracking-widest';
            modalStatus.classList.remove('hidden');
            modalSubmitBtn.disabled = false;
        } finally {
            modalSubmitBtn.innerHTML = '<span>Finalizar Registro</span><i data-lucide="check-circle"></i>';
            lucide.createIcons();
        }
    });

    window.onclick = function(event) {
        const modal = document.getElementById('addLeadModal');
        if (event.target == modal) { toggleModal(); }
    }
</script>


