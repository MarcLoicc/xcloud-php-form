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
            <h2 class="text-3xl font-extrabold text-white mb-2 tracking-tight">Registro Maestro</h2>
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

            <!-- Sección 4: Etiquetas Predefinidas -->
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Etiquetas / Tags</label>
                <div class="flex flex-wrap gap-4 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="tags[]" value="meaads" class="w-5 h-5 rounded border-zinc-700 bg-zinc-950 text-blue-600 focus:ring-blue-500/50 transition-colors">
                        <span class="text-sm font-semibold text-zinc-500 group-hover:text-blue-400">Meaads</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="tags[]" value="arquitectos" class="w-5 h-5 rounded border-zinc-700 bg-zinc-950 text-blue-600 focus:ring-blue-500/50 transition-colors">
                        <span class="text-sm font-semibold text-zinc-500 group-hover:text-blue-400">Arquitectos</span>
                    </label>
                </div>
            </div>

            <!-- Otros Datos -->
            <div class="space-y-2 pt-4">
                <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest ml-1">Observaciones Finales</label>
                <textarea name="message" rows="2" placeholder="Cualquier otra información relevante..."
                          class="block w-full p-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all text-sm"></textarea>
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
    function toggleModal() {
        const modal = document.getElementById('addLeadModal');
        modal.classList.toggle('hidden');
        if(!modal.classList.contains('hidden')) {
            document.getElementById('modalStatusMessage').classList.add('hidden');
            document.getElementById('modalLeadForm').reset();
        }
    }

    const modalForm = document.getElementById('modalLeadForm');
    const modalSubmitBtn = document.getElementById('modalSubmitBtn');
    const modalStatus = document.getElementById('modalStatusMessage');

    modalForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        modalSubmitBtn.disabled = true;
        modalSubmitBtn.innerHTML = 'Procesando...';

        const formData = new FormData(modalForm);
        
        // Manejo manual de checkboxes para etiquetas para pasarlo como array
        const dataObj = Object.fromEntries(formData.entries());
        dataObj.tags = formData.getAll('tags[]'); // Sobreescribimos con el array real de tags

        try {
            const response = await fetch('insert.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dataObj),
            });

            const result = await response.json();

            if (result.status === 'success') {
                modalStatus.textContent = 'REGISTRO GUARDADO';
                modalStatus.className = 'mt-6 p-4 rounded-2xl text-center font-bold bg-green-500/10 text-green-500 border border-green-500/20 text-xs tracking-widest';
                modalStatus.classList.remove('hidden');
                setTimeout(() => { location.reload(); }, 1200);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            modalStatus.textContent = error.message;
            modalStatus.className = 'mt-6 p-4 rounded-2xl text-center font-bold bg-red-500/10 text-red-500 border border-red-500/20 text-xs tracking-widest';
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
