<?php if (count(get_included_files()) <= 1) die('Acceso denegado'); ?>
<div id="addLeadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4 hidden overflow-y-auto">
    <div class="bg-white w-full max-w-2xl rounded-xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.3)] p-12 transform transition-all animate-in zoom-in duration-200">
        
        <div class="flex justify-between items-start mb-12 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-pulse shadow-md shadow-indigo-100"></div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest opacity-80">System Operator Active (Marc)</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Alta <span class="text-indigo-600 not-italic">Operativa</span></h2>
                <p class="text-slate-400 text-[12px] font-bold mt-2 uppercase tracking-widest opacity-60">Ingreso de nuevos datos comerciales en clúster xCloud.</p>
            </div>
            <button onclick="toggleModal()" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-lg transition-all text-slate-300">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="addLeadForm" enctype="multipart/form-data" class="space-y-12">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- Technical Identity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3 group">
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">UID / Nombre Principal</label>
                    <div class="relative">
                        <i data-lucide="user" class="w-4 h-4 absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-all"></i>
                        <input type="text" name="name" required placeholder="Ficha de identificación" 
                               class="block w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-lg focus:ring-4 focus:ring-slate-100 focus:border-slate-800 focus:bg-white text-slate-800 font-bold outline-none transition-all shadow-inner text-[13px]">
                    </div>
                </div>
                <div class="space-y-3 group">
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Endpoint Telefónico</label>
                    <div class="relative">
                        <i data-lucide="phone" class="w-4 h-4 absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-all"></i>
                        <input type="tel" name="phone" required placeholder="+34 000 000 000" 
                               class="block w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-lg focus:ring-4 focus:ring-slate-100 focus:border-slate-800 focus:bg-white text-slate-800 font-bold outline-none transition-all shadow-inner text-[13px] tabular-nums">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-3 group">
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tag Corporativo / Empresa</label>
                    <div class="relative">
                        <i data-lucide="building-2" class="w-4 h-4 absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-all"></i>
                        <input type="text" name="company" placeholder="Razón Social" 
                               class="block w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-lg focus:ring-4 focus:ring-slate-100 focus:border-slate-800 focus:bg-white text-slate-800 font-bold outline-none transition-all shadow-inner text-[13px]">
                    </div>
                </div>
                <div class="space-y-3 group">
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest ml-1">E-mail de Notificación</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-all"></i>
                        <input type="email" name="email" placeholder="comercial@empresa.com" 
                               class="block w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-lg focus:ring-4 focus:ring-slate-100 focus:border-slate-800 focus:bg-white text-slate-800 font-bold outline-none transition-all shadow-inner text-[13px]">
                    </div>
                </div>
            </div>

            <!-- Financial & Status -->
            <div class="p-8 bg-slate-900 rounded-xl relative overflow-hidden group/bits shadow-xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                    <div class="space-y-3">
                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest text-center italic">Inversión €</label>
                        <input type="number" step="0.01" name="proposal_price" placeholder="0.00"
                               class="block w-full px-5 py-4 bg-slate-800 border-0 rounded-lg focus:ring-4 focus:ring-indigo-500/20 text-white font-black text-2xl text-center outline-none transition-all tabular-nums italic placeholder:text-slate-700">
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest text-center italic">Canal Fuente</label>
                        <div class="flex gap-2 p-1.5 bg-slate-800 rounded-lg h-[68px]">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="source" value="pago" class="hidden peer" checked>
                                <div class="h-full flex items-center justify-center text-[9px] font-bold text-slate-500 rounded-md peer-checked:bg-indigo-600 peer-checked:text-white transition-all uppercase tracking-widest shadow-inner">PAID ADS</div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="source" value="organico" class="hidden peer">
                                <div class="h-full flex items-center justify-center text-[9px] font-bold text-slate-500 rounded-md peer-checked:bg-indigo-600 peer-checked:text-white transition-all uppercase tracking-widest shadow-inner">ORGÁNICO</div>
                            </label>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[8px] font-black text-slate-400 uppercase tracking-widest text-center italic">Status Pro</label>
                        <select name="status" class="w-full bg-slate-800 border-0 rounded-lg py-4 px-3 text-[9px] text-white font-bold uppercase outline-none focus:ring-4 focus:ring-indigo-500/20 transition-all tracking-widest h-[68px] appearance-none cursor-pointer text-center italic">
                            <option value="nuevo">NUEVO LEAD</option>
                            <option value="enviar_propuesta">PROPUESTA</option>
                            <option value="interesado_tarde">POST-RESERVA</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Global Voice Audit -->
            <div class="space-y-4">
                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1 flex items-center gap-2">
                    Auditoría de Voz (Live Stream Capture)
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse shadow-md opacity-80"></span>
                </label>
                <div class="flex items-center gap-4 p-4 bg-white border border-slate-200 rounded-xl shadow-inner relative overflow-hidden group/record">
                    <button type="button" id="startBtn" class="w-14 h-14 bg-slate-900 hover:bg-black text-white rounded-lg flex items-center justify-center transition-all shadow-xl group hover:scale-105 active:scale-95 relative z-10">
                        <i data-lucide="mic" class="w-6 h-6"></i>
                    </button>
                    <button type="button" id="stopBtn" disabled class="w-14 h-14 bg-slate-50 hover:bg-slate-100 text-slate-900 rounded-lg flex items-center justify-center transition-all disabled:opacity-20 relative z-10 border border-slate-100">
                        <i data-lucide="square" class="w-6 h-6 fill-slate-900"></i>
                    </button>
                    <div class="flex-1 flex flex-col px-4 relative z-10">
                        <div class="flex items-center gap-2 mb-1">
                            <span id="timerText" class="text-xl font-bold text-slate-900 tabular-nums tracking-tighter italic">00:00</span>
                            <div class="w-1.5 h-1.5 rounded-full bg-slate-200" id="recIndicator"></div>
                        </div>
                        <span id="audioStatus" class="text-[9px] font-bold text-slate-300 uppercase tracking-widest leading-none">Esperando comando del Master User...</span>
                    </div>
                    <div id="audioPreviewContainer" class="hidden ml-auto relative z-10">
                        <div class="px-5 py-2.5 bg-emerald-50 border border-emerald-100 rounded-full text-[9px] font-bold text-emerald-600 uppercase italic">Digital Capture OK ✅</div>
                    </div>
                </div>
            </div>

            <div class="pt-10 border-t border-slate-100 flex items-center justify-between">
                <p class="text-[8px] font-bold text-slate-300 italic max-w-xs uppercase leading-relaxed opacity-60">Aviso: El ingreso de datos incorrectos puede afectar la integridad de las analíticas operacionales de fin de mes.</p>
                <div class="flex gap-3">
                    <button type="button" onclick="toggleModal()" class="px-6 py-4 text-slate-400 text-[10px] font-bold rounded-lg uppercase tracking-widest hover:text-slate-900 transition-all">Descartar</button>
                    <button type="submit" class="px-10 py-4 bg-slate-900 text-white text-[10px] font-bold rounded-lg uppercase tracking-widest hover:bg-black transition-all shadow-xl active:scale-95 flex items-center gap-2 group">
                        Confirmar Registro <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-all"></i>
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
        statusText.innerText = "Capturando audio para metadatos...";
        statusText.classList.replace('text-slate-300', 'text-indigo-400');
        indicator.classList.replace('bg-slate-200', 'bg-indigo-600');
        indicator.classList.add('animate-ping');

        mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
    };

    stopBtn.onclick = () => {
        mediaRecorder.stop();
        clearInterval(timerInterval);
        startBtn.disabled = false;
        stopBtn.disabled = true;
        statusText.innerText = "Procesamiento de audio finalizado con éxito.";
        statusText.classList.replace('text-indigo-400', 'text-emerald-500');
        indicator.classList.remove('animate-ping');
        indicator.classList.replace('bg-indigo-600', 'bg-emerald-500');
        document.getElementById('audioPreviewContainer').classList.remove('hidden');
    };

    document.getElementById('addLeadForm').onsubmit = async (e) => {
        e.preventDefault();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        submitBtn.innerHTML = "Syncing...";
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
            alert("Error Master xCloud: " + data.message);
            submitBtn.innerHTML = "Confirmar Registro";
            submitBtn.disabled = false;
        }
    };
</script>
