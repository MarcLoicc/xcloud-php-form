<!-- Modal Backdrop -->
<div id="addLeadModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md transition-all">
    
    <!-- Modal Content -->
    <div class="relative w-full max-w-lg p-10 bg-zinc-900 border border-zinc-800 rounded-[32px] shadow-2xl animate-in zoom-in-95 duration-200">
        
        <!-- Close Button -->
        <button onclick="toggleModal()" class="absolute top-8 right-8 text-zinc-600 hover:text-white transition-colors">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>

        <div class="mb-10 text-center">
            <div class="w-16 h-16 bg-blue-600/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-blue-500/20">
                <i data-lucide="user-plus" class="w-8 h-8 text-blue-500"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-white mb-2">Nuevo Lead</h2>
            <p class="text-zinc-500 text-sm">Ingresa los datos del nuevo prospecto.</p>
        </div>

        <form id="modalLeadForm" class="space-y-6">
            <div class="space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2 ml-1">Nombre Completo</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-blue-500 text-zinc-700 transition-colors">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <input type="text" name="name" placeholder="Ej: Marc Loic" required
                               class="block w-full pl-12 pr-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2 ml-1">Correo Electrónico</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-blue-500 text-zinc-700 transition-colors">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <input type="email" name="email" placeholder="nombre@ejemplo.com" required
                               class="block w-full pl-12 pr-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2 ml-1">Teléfono</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none group-focus-within:text-blue-500 text-zinc-700 transition-colors">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </div>
                        <input type="tel" name="phone" placeholder="+34 600..."
                               class="block w-full pl-12 pr-4 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2 ml-1">Nota adicional</label>
                    <textarea name="message" rows="2" placeholder="Detalles o anotaciones..."
                              class="block w-full p-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500/50 text-white placeholder-zinc-800 transition-all text-sm"></textarea>
                </div>
            </div>

            <!-- Boton Azul Eléctrico -->
            <button type="submit" id="modalSubmitBtn" class="w-full py-4.5 px-6 bg-blue-600 hover:bg-blue-500 text-white text-base font-black rounded-2xl shadow-xl shadow-blue-600/10 flex items-center justify-center gap-3 active:scale-95 transition-all">
                <span class="btn-text">Guardar en Base de Datos</span>
                <i data-lucide="arrow-right-circle" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
            </button>
        </form>

        <div id="modalStatusMessage" class="hidden mt-6 p-4 rounded-2xl text-center font-bold text-sm"></div>
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
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('insert.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.status === 'success') {
                modalStatus.textContent = '✅ REGISTRADO CON ÉXITO';
                modalStatus.className = 'mt-6 p-4 rounded-2xl text-center font-bold bg-blue-500/10 text-blue-500 border border-blue-500/20 tracking-widest text-xs';
                modalStatus.classList.remove('hidden');
                
                setTimeout(() => {
                    location.reload();
                }, 1000);

            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            modalStatus.textContent = '❌ ERROR: ' + error.message;
            modalStatus.className = 'mt-6 p-4 rounded-2xl text-center font-bold bg-red-500/10 text-red-500 border border-red-500/20 text-xs tracking-widest';
            modalStatus.classList.remove('hidden');
            modalSubmitBtn.disabled = false;
        } finally {
            modalSubmitBtn.innerHTML = '<span>Guardar en Base de Datos</span><i data-lucide="arrow-right-circle"></i>';
            lucide.createIcons();
        }
    });

    window.onclick = function(event) {
        const modal = document.getElementById('addLeadModal');
        if (event.target == modal) {
            toggleModal();
        }
    }
</script>
