<?php if (count(get_included_files()) <= 1) die('Acceso denegado'); ?>
<?php
$existingTags = [];
if (isset($conn)) {
    $tagQuery = $conn->query("SELECT DISTINCT tags FROM leads WHERE tags IS NOT NULL AND tags != ''");
    if ($tagQuery) {
        while ($tRow = $tagQuery->fetch_assoc()) {
            foreach (explode(',', $tRow['tags']) as $p) {
                $tag = trim($p);
                if (!empty($tag) && !in_array($tag, $existingTags)) $existingTags[] = $tag;
            }
        }
    }
    sort($existingTags);
}
?>
<!-- Modal Backdrop -->
<div id="addLeadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all overflow-y-auto">
    
    <!-- Modal Content -->
    <div class="relative w-full max-w-xl p-10 bg-white border border-slate-200 rounded-[2.5rem] shadow-2xl animate-in zoom-in-95 duration-200 mt-20 mb-20">
        
        <button onclick="toggleModal()" class="absolute top-8 right-8 p-2 bg-slate-50 hover:bg-slate-100 rounded-xl text-slate-400 hover:text-slate-900 transition-all">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>

        <div class="mb-10 text-center">
            <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-indigo-100">
                <i data-lucide="user-plus" class="w-8 h-8 text-indigo-600"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-900 mb-2 tracking-tight">Nuevo <span class="text-indigo-600">Lead</span></h2>
            <p class="text-slate-500 text-sm font-medium">Captura los detalles clave para la propuesta comercial.</p>
        </div>

        <form id="modalLeadForm" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nombre Completo *</label>
                    <input type="text" name="name" placeholder="Ej: Marc Loi" required
                           class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none text-slate-900 placeholder-slate-300 transition-all font-medium">
                </div>
                <div class="space-y-2 col-span-2 md:col-span-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Teléfono Móvil *</label>
                    <input type="tel" name="phone" placeholder="+34 600..." required
                           class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none text-slate-900 placeholder-slate-300 transition-all font-medium">
                </div>
                <div class="space-y-2 col-span-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Profesional</label>
                    <input type="email" name="email" placeholder="contacto@empresa.com"
                           class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 outline-none text-slate-900 placeholder-slate-300 transition-all font-medium">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-6 border-t border-slate-100">
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Empresa</label>
                    <input type="text" name="company" placeholder="Nombre de la empresa"
                           class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-slate-900 placeholder-slate-300 transition-all font-medium text-sm">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Web Corporativa</label>
                    <input type="text" name="website" placeholder="www.sitio.com"
                           class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-slate-900 placeholder-slate-300 transition-all font-medium text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-6 border-y border-slate-100">
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Presupuesto Estimado (€)</label>
                    <input type="number" step="0.01" name="proposal_price" placeholder="0.00"
                           class="block w-full px-5 py-3.5 bg-indigo-50 border border-indigo-100 rounded-2xl focus:ring-2 focus:ring-indigo-600/20 focus:border-indigo-600 text-indigo-700 placeholder-indigo-300 transition-all font-black text-2xl text-right">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1 text-center">Procedencia del Prospecto</label>
                    <div class="flex gap-3">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="source" value="organico" class="hidden peer" checked>
                            <div class="py-4 text-center bg-slate-50 border border-slate-200 rounded-2xl text-[10px] font-black text-slate-400 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:text-white transition-all shadow-sm">ORGÁNICO</div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="source" value="pago" class="hidden peer">
                            <div class="py-4 text-center bg-slate-50 border border-slate-200 rounded-2xl text-[10px] font-black text-slate-400 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:text-white transition-all shadow-sm">PAGO ADS</div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Etiquetado Inteligente</label>
                <input type="text" name="tags" id="tagInput" placeholder="Separadas por comas..."
                       class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 text-slate-900 text-xs transition-all outline-none">
                
                <?php if (!empty($existingTags)): ?>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($existingTags as $tag): ?>
                        <button type="button" onclick="addTag('<?php echo $tag; ?>')" 
                                class="px-3 py-1 bg-white border border-slate-200 hover:bg-indigo-600 hover:text-white rounded-lg text-[9px] text-slate-500 transition-all font-black uppercase">
                            + <?php echo $tag; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Grabación de Llamada Rediseñada -->
            <div class="pt-4 mt-2">
                <div class="bg-slate-900 rounded-[2.5rem] p-8 relative overflow-hidden shadow-xl">
                    <canvas id="visualizer" class="absolute inset-0 w-full h-full opacity-30 pointer-events-none"></canvas>
                    <div class="relative z-10 flex items-center gap-6">
                        <button type="button" id="recordBtn" onclick="toggleRecording()" class="w-20 h-20 bg-red-600 hover:bg-red-500 text-white rounded-full flex items-center justify-center transition-all shadow-xl active:scale-95 shadow-red-600/30">
                            <i data-lucide="mic" id="micIcon" class="w-8 h-8"></i>
                        </button>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse" id="recDot"></span>
                                <div id="recordingStatus" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Estación de Audio</div>
                            </div>
                            <div id="recordingTimer" class="text-3xl font-mono font-black text-white tabular-nums">00:00</div>
                        </div>
                        <button type="button" id="discardBtn" onclick="discardAudio()" class="hidden p-4 bg-slate-800 hover:bg-red-950 text-red-500 rounded-2xl border border-slate-700 transition-all" title="Eliminar">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Subida de Archivos -->
            <div class="space-y-3 pt-4">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Documentación Adicional</label>
                <div class="relative">
                    <input type="file" name="lead_file" id="lead_file"
                           class="block w-full text-xs text-slate-400 file:mr-6 file:py-3 file:px-8 file:rounded-2xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition-all cursor-pointer border-2 border-dashed border-slate-100 p-4 rounded-[2rem] bg-slate-50/50">
                </div>
            </div>

            <button type="submit" id="modalSubmitBtn" class="w-full py-5 px-6 bg-indigo-600 hover:bg-indigo-700 text-white text-lg font-black rounded-3xl shadow-2xl shadow-indigo-100 flex items-center justify-center gap-4 transition-all active:scale-95 mt-10">
                <span>Completar Registro</span>
                <i data-lucide="arrow-right-circle" class="w-6 h-6"></i>
            </button>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
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

    function toggleModal() {
        const modal = document.getElementById('addLeadModal');
        modal.classList.toggle('hidden');
        if(!modal.classList.contains('hidden')) {
            document.getElementById('modalLeadForm').reset();
            resetAudioUI();
        }
    }

    function discardAudio() {
        if(confirm('¿Eliminar grabación actual?')) resetAudioUI();
    }

    function resetAudioUI() {
        if(isRecording) stopRecording();
        audioBlob = null;
        audioChunks = [];
        document.getElementById('recordingStatus').textContent = 'Estación de Audio';
        document.getElementById('recordingTimer').textContent = '00:00';
        document.getElementById('discardBtn').classList.add('hidden');
        document.getElementById('recDot').classList.remove('bg-red-500');
        document.getElementById('recDot').classList.add('bg-slate-700');
        const canvas = document.getElementById('visualizer');
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0,0, canvas.width, canvas.height);
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
        const dataArray = new Uint8Array(analyser.frequencyBinCount);
        const draw = () => {
            if(!isRecording) return;
            requestAnimationFrame(draw);
            analyser.getByteFrequencyData(dataArray);
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = '#4f46e5';
            const barWidth = (canvas.width / dataArray.length) * 2.5;
            let x = 0;
            for(let i = 0; i < dataArray.length; i++) {
                let barHeight = dataArray[i] / 2;
                ctx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
                x += barWidth + 1;
            }
        };
        draw();
    }

    async function toggleRecording() {
        if (!isRecording) {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
                analyser = audioContext.createAnalyser();
                audioContext.createMediaStreamSource(stream).connect(analyser);
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
                mediaRecorder.onstop = () => {
                    audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    document.getElementById('discardBtn').classList.remove('hidden');
                };
                mediaRecorder.start();
                startTimer();
                isRecording = true;
                document.getElementById('recordingStatus').textContent = 'GRABANDO EN VIVO...';
                document.getElementById('recDot').classList.add('bg-red-500');
                drawVisualizer();
            } catch (err) { alert('Activa el micro para grabar.'); }
        } else stopRecording();
    }

    function stopRecording() {
        if(mediaRecorder && isRecording) mediaRecorder.stop();
        if(timerInterval) clearInterval(timerInterval);
        isRecording = false;
        document.getElementById('recordingStatus').textContent = 'Audio Procesado';
        document.getElementById('recDot').classList.remove('bg-red-500');
        if(audioContext) audioContext.close();
    }

    document.getElementById('modalLeadForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('modalSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = 'Enviando Datos...';
        const formData = new FormData(e.target);
        if (audioBlob) formData.append('audio_file', audioBlob, 'record.webm');
        try {
            const r = await fetch('insert', { method: 'POST', body: formData });
            const res = await r.json();
            if (res.status === 'success') location.reload();
            else alert(res.message);
        } catch (error) { alert('Error de conexión'); }
        btn.disabled = false;
    });
</script>
