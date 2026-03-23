<!-- Modal Backdrop -->
<div id="addLeadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all">
    
    <!-- Modal Content -->
    <div class="relative w-full max-w-lg p-8 bg-zinc-900 border border-zinc-800 rounded-3xl shadow-2xl animate-in zoom-in-95 duration-200">
        
        <!-- Close Button -->
        <button onclick="toggleModal()" class="absolute top-6 right-6 text-zinc-500 hover:text-white transition-colors">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>

        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-white mb-2">Nuevo Lead</h2>
            <p class="text-zinc-500 text-sm">Completa la información para registrar el prospecto en el sistema.</p>
        </div>

        <form id="modalLeadForm" class="space-y-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-zinc-500 uppercase tracking-widest mb-2 ml-1">Nombre Completo</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-indigo-500 text-zinc-600 transition-colors">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <input type="text" name="name" placeholder="Ej: Marc Loic" required
                               class="block w-full pl-12 pr-4 py-4 bg-zinc-950 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 text-white placeholder-zinc-700 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-zinc-500 uppercase tracking-widest mb-2 ml-1">Correo Electrónico</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-indigo-500 text-zinc-600 transition-colors">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <input type="email" name="email" placeholder="nombre@ejemplo.com" required
                               class="block w-full pl-12 pr-4 py-4 bg-zinc-950 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 text-white placeholder-zinc-700 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase tracking-widest mb-2 ml-1">Teléfono</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-indigo-500 text-zinc-600 transition-colors">
                                <i data-lucide="phone" class="w-5 h-5"></i>
                            </div>
                            <input type="tel" name="phone" placeholder="+34 600..."
                                   class="block w-full pl-12 pr-4 py-4 bg-zinc-950 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 text-white placeholder-zinc-700 transition-all">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-zinc-500 uppercase tracking-widest mb-2 ml-1">Nota del Prospecto</label>
                    <textarea name="message" rows="3" placeholder="Detalles o anotaciones..."
                              class="block w-full p-4 bg-zinc-950 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 text-white placeholder-zinc-700 transition-all"></textarea>
                </div>
            </div>

            <button type="submit" id="modalSubmitBtn" class="w-full py-4 px-6 bg-white hover:bg-zinc-200 text-black font-bold rounded-2xl shadow-lg flex items-center justify-center gap-3 transform active:scale-95 transition-all">
                <span class="btn-text">Guardar Registro</span>
                <i data-lucide="plus" class="w-5 h-5"></i>
            </button>
        </form>

        <div id="modalStatusMessage" class="hidden mt-6 p-4 rounded-2xl text-center font-semibold text-sm"></div>
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
        modalSubmitBtn.innerHTML = 'Guardando...';

        const formData = new FormData(modalForm);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('insert.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.status === 'success') {
                modalStatus.textContent = '✅ ' + result.message;
                modalStatus.className = 'mt-6 p-4 rounded-2xl text-center font-semibold bg-green-500/10 text-green-500 border border-green-500/20';
                modalStatus.classList.remove('hidden');
                
                // Recargar dashboard tras 1 seg para ver el nuevo lead
                setTimeout(() => {
                    location.reload();
                }, 1200);

            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            modalStatus.textContent = '❌ Error: ' + error.message;
            modalStatus.className = 'mt-6 p-4 rounded-2xl text-center font-semibold bg-red-500/10 text-red-500 border border-red-500/20';
            modalStatus.classList.remove('hidden');
            modalSubmitBtn.disabled = false;
        } finally {
            modalSubmitBtn.innerHTML = '<span>Guardar Registro</span><i data-lucide="plus"></i>';
            lucide.createIcons();
        }
    });

    // Cerrar modal al hacer click fuera
    window.onclick = function(event) {
        const modal = document.getElementById('addLeadModal');
        if (event.target == modal) {
            toggleModal();
        }
    }
</script>
