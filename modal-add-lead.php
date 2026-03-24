<?php if (count(get_included_files()) <= 1) die('Acceso denegado'); ?>
<div id="addLeadModal" role="dialog" aria-modal="true" aria-labelledby="addLeadModalTitle" class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/80 backdrop-blur-sm p-4 hidden overflow-y-auto">
    <div class="bg-zinc-900 w-full max-w-2xl rounded-xl shadow-2xl p-8 transform transition-all animate-in zoom-in duration-200 border border-zinc-800" tabindex="-1" id="addLeadModalContent">
        
        <div class="flex justify-between items-start mb-8 pb-6 border-b border-zinc-800">
            <div>
                <h2 id="addLeadModalTitle" class="text-2xl font-bold text-zinc-100 tracking-tight">New Customer Record</h2>
                <p class="text-[14px] text-zinc-400 mt-2 font-medium">Add a new client to the database.</p>
            </div>
            <button onclick="toggleModal()" aria-label="Close modal" class="p-2 text-zinc-500 hover:text-zinc-100 hover:bg-zinc-800 rounded-md transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                <i data-lucide="x" class="w-5 h-5" aria-hidden="true"></i>
            </button>
        </div>

        <form id="addLeadForm" enctype="multipart/form-data" class="space-y-8">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <fieldset class="space-y-4">
                <legend class="text-[15px] font-bold text-zinc-100 border-b border-zinc-800 pb-2 w-full mb-4">Identity</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="new-name" class="block text-[13px] font-semibold text-zinc-300">Customer Name *</label>
                        <div class="relative">
                            <i data-lucide="user" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                            <input type="text" id="new-name" name="name" required placeholder="John Doe" 
                                   class="block w-full pl-9 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-zinc-100 outline-none transition-colors text-[14px] placeholder:text-zinc-600">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="new-company" class="block text-[13px] font-semibold text-zinc-300">Company</label>
                        <div class="relative">
                            <i data-lucide="building-2" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                            <input type="text" id="new-company" name="company" placeholder="Acme Corp" 
                                   class="block w-full pl-9 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-zinc-100 outline-none transition-colors text-[14px] placeholder:text-zinc-600">
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="space-y-4">
                <legend class="text-[15px] font-bold text-zinc-100 border-b border-zinc-800 pb-2 w-full mb-4">Contact Details</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="new-phone" class="block text-[13px] font-semibold text-zinc-300">Phone Number *</label>
                        <div class="relative">
                            <i data-lucide="phone" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                            <input type="tel" id="new-phone" name="phone" required placeholder="+1 234 567 8900" 
                                   class="block w-full pl-9 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-zinc-100 outline-none transition-colors text-[14px] placeholder:text-zinc-600">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="new-email" class="block text-[13px] font-semibold text-zinc-300">Email Address</label>
                        <div class="relative">
                            <i data-lucide="mail" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" aria-hidden="true"></i>
                            <input type="email" id="new-email" name="email" placeholder="john@acme.com" 
                                   class="block w-full pl-9 pr-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-zinc-100 outline-none transition-colors text-[14px] placeholder:text-zinc-600">
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="space-y-4">
                <legend class="text-[15px] font-bold text-zinc-100 border-b border-zinc-800 pb-2 w-full mb-4">Deal Information</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label for="new-price" class="block text-[13px] font-semibold text-zinc-300">Value (€)</label>
                        <input type="number" id="new-price" step="0.01" name="proposal_price" placeholder="0.00"
                               class="block w-full px-3 py-2.5 bg-zinc-950 border border-zinc-800 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-zinc-100 outline-none transition-colors text-[14px] placeholder:text-zinc-600">
                    </div>
                    <div class="space-y-2">
                        <label for="new-status" class="block text-[13px] font-semibold text-zinc-300">Initial Stage</label>
                        <select id="new-status" name="status" class="w-full bg-zinc-950 border border-zinc-800 rounded-md py-2.5 px-3 text-[14px] text-zinc-100 focus:ring-2 focus:ring-indigo-500 transition-colors">
                            <option value="nuevo">New</option>
                            <option value="enviar_propuesta">Proposal</option>
                            <option value="interesado_tarde">Postponed</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <span class="block text-[13px] font-semibold text-zinc-300 mb-2">Acquisition Source</span>
                        <div class="flex gap-2" role="radiogroup" aria-label="Acquisition Source">
                            <label class="flex-1 relative cursor-pointer group">
                                <input type="radio" name="source" value="pago" class="sr-only peer" checked>
                                <span class="flex items-center justify-center p-2.5 text-[13px] font-medium rounded-md border border-zinc-800 bg-zinc-950 text-zinc-400 peer-checked:bg-zinc-100 peer-checked:text-zinc-950 peer-checked:border-zinc-100 transition-colors peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500">Paid</span>
                            </label>
                            <label class="flex-1 relative cursor-pointer group">
                                <input type="radio" name="source" value="organico" class="sr-only peer">
                                <span class="flex items-center justify-center p-2.5 text-[13px] font-medium rounded-md border border-zinc-800 bg-zinc-950 text-zinc-400 peer-checked:bg-zinc-100 peer-checked:text-zinc-950 peer-checked:border-zinc-100 transition-colors peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500">Organic</span>
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="space-y-4">
                <legend class="text-[15px] font-bold text-zinc-100 border-b border-zinc-800 pb-2 w-full mb-4">Voice Note (Optional)</legend>
                <div class="flex items-center gap-4 p-4 bg-zinc-950 border border-zinc-800 rounded-lg">
                    <button type="button" id="startBtn" aria-label="Start recording" class="w-10 h-10 bg-zinc-100 hover:bg-zinc-300 text-zinc-900 rounded-full flex items-center justify-center transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">
                        <i data-lucide="mic" class="w-5 h-5" aria-hidden="true"></i>
                    </button>
                    <button type="button" id="stopBtn" aria-label="Stop recording" disabled class="w-10 h-10 bg-zinc-900 border border-zinc-800 text-zinc-500 rounded-full flex items-center justify-center transition-colors disabled:opacity-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 hover:text-red-400">
                        <i data-lucide="square" class="w-4 h-4 fill-current" aria-hidden="true"></i>
                    </button>
                    
                    <div class="flex-1 flex items-center gap-3">
                        <span id="timerText" class="text-[14px] font-medium text-zinc-300 tabular-nums" aria-live="polite">00:00</span>
                        <div class="w-2 h-2 rounded-full bg-zinc-700" id="recIndicator" aria-hidden="true"></div>
                        <span id="audioStatus" class="sr-only" aria-live="polite">Ready to record</span>
                    </div>
                    
                    <div id="audioPreviewContainer" class="hidden" aria-live="polite">
                        <span class="px-3 py-1 bg-emerald-900/30 border border-emerald-500/30 rounded-md text-[12px] font-medium text-emerald-400">Recorded</span>
                    </div>
                </div>
            </fieldset>

            <div class="pt-6 border-t border-zinc-800 flex justify-end gap-3 mt-8">
                <button type="button" onclick="toggleModal()" class="px-5 py-2.5 text-zinc-400 text-[14px] font-medium rounded-md hover:text-zinc-100 hover:bg-zinc-800 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500">Cancel</button>
                <button type="submit" class="px-6 py-2.5 bg-zinc-100 text-zinc-950 text-[14px] font-bold rounded-md hover:bg-zinc-300 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 shadow-sm">
                    Create Record
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

    let triggerBtn = null; // To restore focus when modal closes

    function toggleModal() {
        const modal = document.getElementById('addLeadModal');
        const isHidden = modal.classList.contains('hidden');
        
        if (isHidden) {
            triggerBtn = document.activeElement;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            lucide.createIcons();
            setTimeout(() => { document.getElementById('new-name').focus(); }, 50);
        } else {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            if (triggerBtn) triggerBtn.focus();
        }
    }

    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const timerText = document.getElementById('timerText');
    const statusText = document.getElementById('audioStatus');
    const indicator = document.getElementById('recIndicator');

    startBtn.onclick = async () => {
        try {
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
            statusText.innerText = "Recording started";
            indicator.classList.replace('bg-zinc-700', 'bg-red-500');
            indicator.classList.add('animate-pulse');

            mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
            
            // Move focus to stop button logically
            stopBtn.focus();
        } catch(err) {
            alert('Microphone access denied or not available.');
        }
    };

    stopBtn.onclick = () => {
        if(mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            clearInterval(timerInterval);
            startBtn.disabled = false;
            stopBtn.disabled = true;
            statusText.innerText = "Recording finished";
            indicator.classList.remove('animate-pulse');
            indicator.classList.replace('bg-red-500', 'bg-emerald-500');
            document.getElementById('audioPreviewContainer').classList.remove('hidden');
            startBtn.focus();
        }
    };

    document.getElementById('addLeadForm').onsubmit = async (e) => {
        e.preventDefault();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        submitBtn.innerHTML = "Creating...";
        submitBtn.disabled = true;

        const formData = new FormData(e.target);
        if (audioChunks.length > 0) {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            formData.append('audio_file', audioBlob, 'capture.webm');
        }

        try {
            const res = await fetch('insert.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.status === 'success') {
                location.reload();
            } else {
                alert("Error: " + data.message);
                submitBtn.innerHTML = "Create Record";
                submitBtn.disabled = false;
            }
        } catch (err) {
            alert('A network error occurred.');
            submitBtn.innerHTML = "Create Record";
            submitBtn.disabled = false;
        }
    };

    // Close Add modal on ESC
    window.addEventListener('keydown', (e) => {
        const modal = document.getElementById('addLeadModal');
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            toggleModal();
        }
    });
</script>
