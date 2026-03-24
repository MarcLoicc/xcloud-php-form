<?php if (count(get_included_files()) <= 1) die('Acceso denegado'); ?>
<div id="addLeadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-4 hidden overflow-y-auto">
    <div class="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-2xl shadow-[0_50px_100px_-20px_#000000] p-12 transform transition-all animate-in zoom-in duration-200">
        
        <div class="flex justify-between items-start mb-14 pb-10 border-b border-slate-800">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse shadow-md shadow-indigo-600"></div>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.4em] opacity-80">PROX_SYNC OPERATOR: MARC</span>
                </div>
                <h2 class="text-4xl font-black text-white tracking-tighter italic uppercase underline decoration-indigo-600 decoration-4 underline-offset-8">ALTA <span class="text-indigo-500 not-italic">MASTER</span></h2>
                <p class="text-slate-500 text-[14px] font-bold mt-8 uppercase tracking-widest opacity-60 italic">Ingreso jerárquico de metadatos comerciales.</p>
            </div>
            <button onclick="toggleModal()" class="p-4 bg-slate-950 hover:bg-red-950 hover:text-red-500 border border-slate-800 rounded-xl transition-all text-slate-700">
                <i data-lucide="x" class="w-7 h-7"></i>
            </button>
        </div>

        <form id="addLeadForm" enctype="multipart/form-data" class="space-y-12">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- Dark Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-4 group">
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-[0.3em] ml-1 italic">UID / IDENTIDAD LOG</label>
                    <div class="relative">
                        <i data-lucide="user" class="w-5 h-5 absolute left-6 top-1/2 -translate-y-1/2 text-slate-800 group-focus-within:text-indigo-400 transition-all"></i>
                        <input type="text" name="name" required placeholder="NOMBRE_UID" 
                               class="block w-full pl-16 pr-6 py-5 bg-slate-950 border border-slate-800 rounded-xl focus:ring-4 focus:ring-slate-800 focus:border-indigo-600 focus:bg-slate-950 text-white font-black outline-none transition-all shadow-inner text-[15px] italic uppercase">
                    </div>
                </div>
                <div class="space-y-4 group">
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-[0.3em] ml-1 italic">TELÉFONO ENDPOINT</label>
                    <div class="relative">
                        <i data-lucide="phone" class="w-5 h-5 absolute left-6 top-1/2 -translate-y-1/2 text-slate-800 group-focus-within:text-indigo-400 transition-all"></i>
                        <input type="tel" name="phone" required placeholder="+34 XXX XXX XXX" 
                               class="block w-full pl-16 pr-6 py-5 bg-slate-950 border border-slate-800 rounded-xl focus:ring-4 focus:ring-slate-800 focus:border-indigo-600 focus:bg-slate-950 text-white font-black outline-none transition-all shadow-inner text-[15px] tabular-nums italic">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-4 group">
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-[0.3em] ml-1 italic">CORPORACIÓN / ASOCIADO</label>
                    <div class="relative">
                        <i data-lucide="building-2" class="w-5 h-5 absolute left-6 top-1/2 -translate-y-1/2 text-slate-800 group-focus-within:text-indigo-400 transition-all"></i>
                        <input type="text" name="company" placeholder="ENTITY_DESCRIPTION" 
                               class="block w-full pl-16 pr-6 py-5 bg-slate-950 border border-slate-800 rounded-xl focus:ring-4 focus:ring-slate-800 focus:border-indigo-600 focus:bg-slate-950 text-white font-black outline-none transition-all shadow-inner text-[15px] italic uppercase">
                    </div>
                </div>
                <div class="space-y-4 group">
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-[0.3em] ml-1 italic">E-MAIL CANAL</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-5 h-5 absolute left-6 top-1/2 -translate-y-1/2 text-slate-800 group-focus-within:text-indigo-400 transition-all"></i>
                        <input type="email" name="email" placeholder="ADMIN@MASTER.DAT" 
                               class="block w-full pl-16 pr-6 py-5 bg-slate-950 border border-slate-800 rounded-xl focus:ring-4 focus:ring-slate-800 focus:border-indigo-600 focus:bg-slate-950 text-white font-black outline-none transition-all shadow-inner text-[15px] italic uppercase">
                    </div>
                </div>
            </div>

            <!-- Financial Assets Dark -->
            <div class="p-10 bg-slate-950 border border-slate-800 rounded-2xl relative overflow-hidden group/bits shadow-3xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 relative z-10">
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-slate-600 uppercase tracking-[0.4em] text-center italic">CAPITAL €</label>
                        <input type="number" step="0.01" name="proposal_price" placeholder="0.00"
                               class="block w-full px-6 py-5 bg-slate-900 border border-slate-800 rounded-xl focus:ring-4 focus:ring-indigo-900 text-white font-black text-3xl text-center outline-none transition-all tabular-nums italic placeholder:text-slate-800">
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-slate-600 uppercase tracking-[0.4em] text-center italic">ORIGEN_SET</label>
                        <div class="flex gap-3 p-2 bg-slate-900 rounded-xl h-[76px] border border-slate-800">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="source" value="pago" class="hidden peer" checked>
                                <div class="h-full flex items-center justify-center text-[11px] font-black text-slate-700 rounded-lg peer-checked:bg-white peer-checked:text-slate-950 transition-all uppercase tracking-widest shadow-2xl border border-transparent peer-checked:border-white italic">PAID</div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="source" value="organico" class="hidden peer">
                                <div class="h-full flex items-center justify-center text-[11px] font-black text-slate-700 rounded-lg peer-checked:bg-white peer-checked:text-slate-950 transition-all uppercase tracking-widest shadow-2xl border border-transparent peer-checked:border-white italic">ORG</div>
                            </label>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[10px] font-black text-slate-600 uppercase tracking-[0.4em] text-center italic">STATUS_CMD</label>
                        <select name="status" class="w-full bg-slate-900 border border-slate-800 rounded-xl py-5 px-4 text-[11px] text-white font-black uppercase outline-none focus:ring-4 focus:ring-indigo-900 transition-all tracking-widest h-[76px] appearance-none cursor-pointer text-center italic shadow-inner">
                            <option value="nuevo">NUEVO LOG</option>
                            <option value="enviar_propuesta">EXP_PROPUESTA</option>
                            <option value="interesado_tarde">POST_RESERVA</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Master Audio Recording Dark -->
            <div class="space-y-5">
                <label class="block text-[10px] font-black text-slate-700 uppercase tracking-[0.4em] ml-2 flex items-center gap-3 italic">
                    VOICE_LOG CAPTURE (CLOUD STREAM)
                    <span class="w-2 h-2 bg-red-600 rounded-full animate-pulse shadow-[0_0_15px_#dc2626]"></span>
                </label>
                <div class="flex items-center gap-6 p-6 bg-slate-950 border border-slate-800 rounded-2xl shadow-3xl relative overflow-hidden group/record">
                    <button type="button" id="startBtn" class="w-16 h-16 bg-white hover:bg-slate-200 text-slate-950 rounded-xl flex items-center justify-center transition-all shadow-2xl group hover:scale-105 active:scale-95 relative z-10 border-4 border-white">
                        <i data-lucide="mic" class="w-8 h-8"></i>
                    </button>
                    <button type="button" id="stopBtn" disabled class="w-16 h-16 bg-slate-900 hover:bg-red-950 hover:text-red-500 text-slate-500 rounded-xl flex items-center justify-center transition-all disabled:opacity-20 relative z-10 border border-slate-800">
                        <i data-lucide="square" class="w-8 h-8 fill-slate-800"></i>
                    </button>
                    <div class="flex-1 flex flex-col px-6 relative z-10">
                        <div class="flex items-center gap-3 mb-2">
                            <span id="timerText" class="text-2xl font-black text-white tabular-nums tracking-tighter italic leading-none">00:00</span>
                            <div class="w-2 h-2 rounded-full bg-slate-800" id="recIndicator"></div>
                        </div>
                        <span id="audioStatus" class="text-[10px] font-black text-slate-700 uppercase tracking-[0.4em] leading-none italic">AWAITING_CMD_FROM_MASTER_USER...</span>
                    </div>
                    <div id="audioPreviewContainer" class="hidden ml-auto relative z-10">
                        <div class="px-6 py-3 bg-emerald-950 border border-emerald-900 rounded-full text-[10px] font-black text-emerald-400 uppercase italic tracking-widest animate-pulse border-2">LOGGED ✅</div>
                    </div>
                </div>
            </div>

            <div class="pt-12 border-t border-slate-800 flex items-center justify-between">
                <p class="text-[9px] font-black text-slate-700 italic max-w-sm uppercase leading-loose opacity-60 tracking-widest">WARNING: DATA PERMANENCY IN CLUSTER IS GUARANTEED ONCE SYNCED.</p>
                <div class="flex gap-4">
                    <button type="button" onclick="toggleModal()" class="px-8 py-5 text-slate-600 text-[11px] font-black rounded-xl uppercase tracking-widest hover:text-white transition-all">ABORT_CMD</button>
                    <button type="submit" class="px-12 py-5 bg-white text-slate-950 text-[12px] font-black rounded-xl uppercase tracking-[0.3em] hover:bg-slate-200 transition-all shadow-3xl active:scale-95 flex items-center gap-4 group border border-white">
                        SYNC_RECORD <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-2 transition-all text-indigo-600"></i>
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
        statusText.innerText = "CAPTURING_AUDIO_METADATA...";
        statusText.classList.replace('text-slate-700', 'text-indigo-400');
        indicator.classList.replace('bg-slate-800', 'bg-indigo-600');
        indicator.classList.add('animate-ping');

        mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
    };

    stopBtn.onclick = () => {
        mediaRecorder.stop();
        clearInterval(timerInterval);
        startBtn.disabled = false;
        stopBtn.disabled = true;
        statusText.innerText = "CAPTURE_COMPLETED_SUCCESSFULLY.";
        statusText.classList.replace('text-indigo-400', 'text-emerald-500');
        indicator.classList.remove('animate-ping');
        indicator.classList.replace('bg-indigo-600', 'bg-emerald-500');
        document.getElementById('audioPreviewContainer').classList.remove('hidden');
    };

    document.getElementById('addLeadForm').onsubmit = async (e) => {
        e.preventDefault();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        submitBtn.innerHTML = "SYNCING...";
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
            alert("MASTER_ERROR_xCLOUD: " + data.message);
            submitBtn.innerHTML = "SYNC_RECORD";
            submitBtn.disabled = false;
        }
    };
</script>
