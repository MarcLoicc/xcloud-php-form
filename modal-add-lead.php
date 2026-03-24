<?php if (count(get_included_files()) <= 1) die('Acceso denegado'); ?>
<div id="addLeadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-2xl p-4 hidden overflow-y-auto">
    <div class="bg-white/95 border border-white w-full max-w-2xl rounded-[3.5rem] shadow-[0_50px_100px_-20px_rgba(30,41,59,0.3)] p-12 transform transition-all animate-in zoom-in duration-300">
        
        <div class="flex justify-between items-start mb-12">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-2 h-2 bg-indigo-600 rounded-full animate-pulse shadow-glow"></div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Data Entry 5.0</span>
                </div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter italic">Nuevo <span class="text-indigo-600 not-italic">Prospecto</span></h2>
                <p class="text-slate-500 text-[13px] font-bold mt-2 uppercase tracking-widest opacity-80">Registra un nuevo activo en la base de datos cloud.</p>
            </div>
            <button onclick="toggleModal()" class="p-4 bg-slate-50 hover:bg-red-50 hover:text-red-500 rounded-[2rem] transition-all text-slate-300 active:scale-90">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <form id="addLeadForm" enctype="multipart/form-data" class="space-y-12">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- Identidad Primaria -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3 group">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Nombre Completo</label>
                    <div class="relative">
                        <i data-lucide="user" class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-600 transition-all"></i>
                        <input type="text" name="name" required placeholder="Ej: Marc Loi" 
                               class="block w-full pl-14 pr-6 py-4.5 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-8 focus:ring-indigo-100 focus:border-indigo-600 focus:bg-white text-slate-800 font-bold outline-none transition-all shadow-inner">
                    </div>
                </div>
                <div class="space-y-3 group">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Identificador Móvil</label>
                    <div class="relative">
                        <i data-lucide="phone" class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-600 transition-all"></i>
                        <input type="tel" name="phone" required placeholder="+34 000 000 000" 
                               class="block w-full pl-14 pr-6 py-4.5 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-8 focus:ring-indigo-100 focus:border-indigo-600 focus:bg-white text-slate-800 font-bold outline-none transition-all shadow-inner">
                    </div>
                </div>
            </div>

            <!-- Perfil Corporativo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3 group">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Organización / Empresa</label>
                    <div class="relative">
                        <i data-lucide="building-2" class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-600 transition-all"></i>
                        <input type="text" name="company" placeholder="Empresa S.L." 
                               class="block w-full pl-14 pr-6 py-4.5 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-8 focus:ring-indigo-100 focus:border-indigo-600 focus:bg-white text-slate-800 font-bold outline-none transition-all shadow-sm">
                    </div>
                </div>
                <div class="space-y-3 group">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2">Email de Contacto</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-600 transition-all"></i>
                        <input type="email" name="email" placeholder="hola@empresa.com" 
                               class="block w-full pl-14 pr-6 py-4.5 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-8 focus:ring-indigo-100 focus:border-indigo-600 focus:bg-white text-slate-800 font-bold outline-none transition-all shadow-sm">
                    </div>
                </div>
            </div>

            <!-- Business Logic Section -->
            <div class="p-8 bg-indigo-50/50 border border-indigo-100/50 rounded-[2.5rem] relative overflow-hidden group/bits">
                <div class="absolute inset-0 bg-white opacity-0 group-hover/bits:opacity-40 transition-opacity"></div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest text-center">Inversión €</label>
                        <input type="number" step="0.01" name="proposal_price" placeholder="0.00"
                               class="block w-full px-5 py-4 bg-white border border-indigo-100 rounded-2xl focus:ring-8 focus:ring-indigo-100 focus:border-indigo-600 text-slate-800 font-black text-2xl text-center outline-none transition-all italic">
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest text-center">Adquisición</label>
                        <div class="flex gap-2 p-1.5 bg-white border border-indigo-100 rounded-[1.5rem] h-[64px]">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="source" value="pago" class="hidden peer" checked>
                                <div class="h-full flex items-center justify-center text-[9px] font-black text-slate-400 rounded-xl peer-checked:bg-slate-900 peer-checked:text-white transition-all uppercase tracking-widest">ADS</div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="source" value="organico" class="hidden peer">
                                <div class="h-full flex items-center justify-center text-[9px] font-black text-slate-400 rounded-xl peer-checked:bg-slate-900 peer-checked:text-white transition-all uppercase tracking-widest">ORG</div>
                            </label>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-indigo-400 uppercase tracking-widest text-center">Estado Flujo</label>
                        <select name="status" class="w-full bg-white border border-indigo-100 rounded-2xl py-4 px-3 text-[9px] text-indigo-600 font-black uppercase outline-none focus:ring-8 focus:ring-indigo-100 transition-all tracking-widest h-[64px] appearance-none cursor-pointer text-center">
                            <option value="nuevo">NUEVO LEAD</option>
                            <option value="enviar_propuesta">PROPUESTA</option>
                            <option value="interesado_tarde">RESERVA</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Grabación de Auditoría Voice -->
            <div class="space-y-4">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] ml-2 flex items-center gap-3">
                    Estación de Grabación Cloud
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse shadow-[0_0_10px_#ef4444]"></span>
                </label>
                <div class="flex items-center gap-4 p-5 bg-slate-900 rounded-[2.5rem] border border-slate-800 shadow-2xl shadow-indigo-100 overflow-hidden relative group/record">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/10 to-transparent"></div>
                    <button type="button" id="startBtn" class="w-16 h-16 bg-red-600 hover:bg-red-500 text-white rounded-[1.4rem] flex items-center justify-center transition-all shadow-xl shadow-red-500/20 group hover:scale-105 active:scale-95 group relative z-10">
                        <i data-lucide="mic" class="w-8 h-8 group-hover:animate-bounce"></i>
                    </button>
                    <button type="button" id="stopBtn" disabled class="w-16 h-16 bg-white hover:bg-slate-100 text-slate-900 rounded-[1.4rem] flex items-center justify-center transition-all disabled:opacity-20 relative z-10">
                        <i data-lucide="square" class="w-7 h-7 fill-slate-900"></i>
                    </button>
                    <div class="flex-1 flex flex-col px-4 relative z-10">
                        <div class="flex items-center gap-3 mb-1">
                            <span id="timerText" class="text-2xl font-black text-white tabular-nums tracking-tighter italic">00:00</span>
                            <div class="w-2 h-2 rounded-full bg-slate-600" id="recIndicator"></div>
                        </div>
                        <span id="audioStatus" class="text-[9px] font-black text-slate-500 uppercase tracking-widest leading-none">Esperando disparo del operador (Marc)...</span>
                    </div>
                    <div id="audioPreviewContainer" class="hidden ml-auto relative z-10">
                        <div class="px-6 py-3 bg-indigo-500/20 border border-indigo-400 rounded-full text-[10px] font-black text-indigo-400 uppercase animate-pulse">Audio Analizado ✅</div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-100 flex items-center justify-between">
                <p class="text-[9px] font-bold text-slate-400 italic max-w-xs uppercase leading-loose opacity-50">Al procesar este registro, se sincronizarán los metadatos de auditoría y archivos adjuntos en el clúster de xCloud.</p>
                <div class="flex gap-4">
                    <button type="button" onclick="toggleModal()" class="px-10 py-5 bg-slate-50 text-slate-400 text-[10px] font-black rounded-3xl uppercase tracking-widest hover:bg-slate-100 transition-all">Descartar</button>
                    <button type="submit" class="px-12 py-5 bg-slate-900 text-white text-[10px] font-black rounded-3xl uppercase tracking-[0.3em] hover:bg-black transition-all shadow-2xl shadow-slate-200 active:scale-95 flex items-center gap-3 group">
                        Confirmar Envío <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-2 transition-all"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let mediaRecorder;
    let audioChunks = [];
    let startTime;
    let timerInterval;

    function toggleModal() {
        const modal = document.getElementById('addLeadModal');
        modal.classList.toggle('hidden');
        document.body.style.overflow = modal.classList.contains('hidden') ? 'auto' : 'hidden';
        lucide.createIcons();
    }

    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const timerText = document.getElementById('timerText');
    const statusText = document.getElementById('audioStatus');
    const indicator = document.getElementById('recIndicator');

    startBtn.onclick = async () => {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        mediaRecorder.start();
        audioChunks = [];

        startTime = Date.now();
        timerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const m = Math.floor(elapsed / 60).toString().padStart(2, '0');
            const s = (elapsed % 60).toString().padStart(2, '0');
            timerText.innerText = `${m}:${s}`;
        }, 1000);

        startBtn.disabled = true;
        stopBtn.disabled = false;
        statusText.innerText = "Capturando espectro de voz comercial...";
        statusText.classList.replace('text-slate-500', 'text-red-400');
        indicator.classList.replace('bg-slate-600', 'bg-red-500');
        indicator.classList.add('animate-ping');

        mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
    };

    stopBtn.onclick = () => {
        mediaRecorder.stop();
        clearInterval(timerInterval);
        startBtn.disabled = false;
        stopBtn.disabled = true;
        statusText.innerText = "Grabación finalizada y comprimida en formato WebM (Estándar Cloud).";
        statusText.classList.replace('text-red-400', 'text-emerald-400');
        indicator.classList.remove('animate-ping');
        indicator.classList.replace('bg-red-500', 'bg-emerald-500');
        document.getElementById('audioPreviewContainer').classList.remove('hidden');
    };

    document.getElementById('addLeadForm').onsubmit = async (e) => {
        e.preventDefault();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        submitBtn.innerHTML = "Sincronizando...";
        submitBtn.disabled = true;

        const formData = new FormData(e.target);
        if (audioChunks.length > 0) {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            formData.append('audio_file', audioBlob, 'capture.webm');
        }

        const res = await fetch('insert.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.status === 'success') {
            location.reload();
        } else {
            alert("Fallo crítico en xCloud: " + data.message);
            submitBtn.innerHTML = "Confirmar Envío";
            submitBtn.disabled = false;
        }
    };
</script>
