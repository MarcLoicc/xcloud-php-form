<?php
// Obtener etiquetas existentes de la base de datos para sugerencias
$existingTags = [];
if (isset($conn)) {
    $tagQuery = $conn->query("SELECT DISTINCT tags FROM leads WHERE tags IS NOT NULL AND tags != ''");
    if ($tagQuery) {
        while ($tRow = $tagQuery->fetch_assoc()) {
            $parts = explode(',', $tRow['tags']);
            foreach ($parts as $p) {
                $tag = trim($p);
                if (!empty($tag) && !in_array($tag, $existingTags)) {
                    $existingTags[] = $tag;
                }
            }
        }
    }
    sort($existingTags);
}
?>
<!-- Modal Backdrop -->
<div id="addLeadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md transition-all overscroll-y-contain">
    
    <!-- Modal Content -->
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Nombre Lead *</label>
                    <input type="text" name="name" placeholder="Juán Pérez" required
                           class="block w-full px-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium">
                </div>
                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Teléfono Directo *</label>
                    <input type="tel" name="phone" placeholder="+34 600..." required
                           class="block w-full px-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium">
                </div>
                <div class="space-y-2 col-span-2 md:col-span-2">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Email (Opcional)</label>
                    <input type="email" name="email" placeholder="juan@gmail.com"
                           class="block w-full px-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 py-4 border-t border-zinc-800/50">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Nombre Empresa</label>
                    <input type="text" name="company" placeholder="Ej: Tech Corp"
                           class="block w-full px-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Sitio Web</label>
                    <input type="text" name="website" placeholder="ejemplo.es"
                           class="block w-full px-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium text-sm">
                </div>
            </div>

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

            <!-- Sección Mejorada de Etiquetas -->
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Etiquetas (separadas por comas)</label>
                <input type="text" name="tags" id="tagInput" placeholder="Ej: Importante, Arquitectos"
                       class="block w-full px-4 py-3 bg-zinc-950/50 border border-zinc-800 rounded-xl focus:ring-1 focus:ring-blue-500/50 text-white text-xs transition-all">
                
                <?php if (!empty($existingTags)): ?>
                <div class="flex flex-wrap gap-2 pt-1">
                    <span class="text-[9px] text-zinc-700 font-bold uppercase w-full mb-1">Usadas recientemente:</span>
                    <?php foreach ($existingTags as $tag): ?>
                        <button type="button" onclick="addTag('<?php echo $tag; ?>')" 
                                class="px-3 py-1 bg-zinc-900 border border-zinc-800 hover:border-blue-500 rounded-lg text-[10px] text-zinc-500 hover:text-blue-500 transition-all font-semibold uppercase">
                            + <?php echo $tag; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Grabación de Llamada -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Estación de Grabación</label>
                    <select id="micSelect" class="text-[9px] bg-zinc-950 border border-zinc-800 rounded-lg px-2 py-1 text-zinc-500 outline-none focus:border-blue-500 transition-colors max-w-[150px] truncate"></select>
                </div>
                
                <div class="relative bg-zinc-950/40 p-6 border border-zinc-800 rounded-[2rem] overflow-hidden group">
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
                            <button type="button" id="discardBtn" onclick="discardAudio()" class="hidden p-3 bg-zinc-800 hover:bg-red-600/20 text-zinc-500 hover:text-red-500 rounded-2xl border border-zinc-700 transition-all active:scale-90" title="Descartar audio">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subida de Archivos -->
            <div class="space-y-2 pt-2">
                <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Documentos Adicionales</label>
                <input type="file" name="lead_file" id="lead_file"
                       class="block w-full text-xs text-zinc-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600/10 file:text-blue-500 hover:file:bg-blue-600/20 transition-all cursor-pointer border border-zinc-800 p-3 rounded-2xl">
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
    // Lucide Icons
    lucide.createIcons();

    // Nueva función para añadir etiquetas dinámicamente
    function addTag(tag) {
        const input = document.getElementById('tagInput');
        let currentTags = input.value.split(',').map(t => t.trim()).filter(t => t !== "");
        if (!currentTags.includes(tag)) {
            currentTags.push(tag);
            input.value = currentTags.join(', ');
        }
    }

    let mediaRecorder;
    let audioChunks = [];
    let audioBlob;
    let isRecording = false;
    let timerInterval;
    let audioContext;
    let analyser;
    let animationId;

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
        if(confirm('¿Descartar grabación?')) resetAudioUI();
    }

    function resetAudioUI() {
        stopRecording();
        audioBlob = null;
        audioChunks = [];
        document.getElementById('micIcon').innerHTML = '<i data-lucide="mic"></i>';
        document.getElementById('recordingStatus').textContent = 'Micro listo';
        document.getElementById('recordingTimer').textContent = '00:00';
        document.getElementById('audioPreview').classList.add('hidden');
        document.getElementById('discardBtn').classList.add('hidden');
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
            let barHeight; let x = 0;
            for(let i = 0; i < bufferLength; i++) {
                barHeight = dataArray[i] / 2;
                ctx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
                x += barWidth + 1;
            }
        };
        draw();
    }

    async function toggleRecording() {
        if (!isRecording && audioBlob && !confirm('¿Nueva grabación?')) return;
        if (!isRecording) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: { deviceId: document.getElementById('micSelect').value } });
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                analyser = audioContext.createAnalyser();
                audioContext.createMediaStreamSource(stream).connect(analyser);
                drawVisualizer();
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
                mediaRecorder.onstop = () => {
                    audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    document.getElementById('audioPreview').src = URL.createObjectURL(audioBlob);
                    document.getElementById('audioPreview').classList.remove('hidden');
                    document.getElementById('discardBtn').classList.remove('hidden');
                };
                mediaRecorder.start();
                startTimer();
                isRecording = true;
                document.getElementById('recordingStatus').textContent = 'GRABANDO...';
            } catch (err) { alert('Micro no disponible'); }
        } else stopRecording();
    }

    function stopRecording() {
        if(mediaRecorder && isRecording) mediaRecorder.stop();
        if(timerInterval) clearInterval(timerInterval);
        isRecording = false;
        document.getElementById('recordingStatus').textContent = 'Listo';
        if(audioContext) audioContext.close();
    }

    document.getElementById('modalLeadForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        modalSubmitBtn.disabled = true;
        modalSubmitBtn.innerHTML = 'Enviando...';
        const formData = new FormData(e.target);
        if (audioBlob) formData.append('audio_file', audioBlob, 'record.webm');
        try {
            const r = await fetch('insert.php', { method: 'POST', body: formData });
            const res = await r.json();
            if (res.status === 'success') {
                setTimeout(() => location.reload(), 800);
            } else throw new Error(res.message);
        } catch (error) {
            alert(error.message);
            modalSubmitBtn.disabled = false;
        }
    });

    window.onclick = e => { if (e.target == addLeadModal) toggleModal(); }
</script>
