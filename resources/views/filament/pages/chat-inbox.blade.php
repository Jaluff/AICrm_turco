<x-filament-panels::page>
<style>
    .chat-inbox-container {
        background-color: #f4f4f5 !important;
        color: #18181b !important;
    }
    .dark .chat-inbox-container {
        background-color: #09090b !important;
        color: #f4f4f5 !important;
    }
    .chat-panel-left {
        background-color: #ffffff !important;
        border-color: #e4e4e7 !important;
    }
    .dark .chat-panel-left {
        background-color: #18181b !important;
        border-color: #27272a !important;
    }
    .chat-panel-right {
        background-color: #f4f4f5 !important;
        border-color: #e4e4e7 !important;
    }
    .dark .chat-panel-right {
        background-color: #18181b !important;
        border-color: #27272a !important;
    }
    .chat-pill-unselected {
        background-color: #e4e4e7 !important;
        border: none !important;
        border-width: 0 !important;
        color: #27272a !important;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .chat-pill-unselected:hover {
        background-color: #d4d4d8 !important;
    }
    .dark .chat-pill-unselected {
        background-color: rgba(255, 255, 255, 0.08) !important;
        color: #f4f4f5 !important;
    }
    .dark .chat-pill-unselected:hover {
        background-color: rgba(255, 255, 255, 0.15) !important;
    }
    .chat-pill-selected {
        border: none !important;
        border-width: 0 !important;
        background-color: rgba(245, 158, 11, 0.2) !important;
        color: rgb(217, 119, 6) !important;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .chat-pill-selected:hover {
        background-color: rgba(245, 158, 11, 0.28) !important;
    }
    .dark .chat-pill-selected {
        background-color: rgba(245, 158, 11, 0.25) !important;
        color: rgb(251, 191, 36) !important;
    }
    .dark .chat-pill-selected:hover {
        background-color: rgba(245, 158, 11, 0.35) !important;
    }
    .chat-header-bg {
        background-color: #ffffff !important;
        border-color: #e4e4e7 !important;
    }
    .dark .chat-header-bg {
        background-color: #18181b !important;
        border-color: #27272a !important;
    }
    .chat-messages-area {
        background-color: #efeae2 !important;
    }
    .dark .chat-messages-area {
        background-color: #0b141a !important;
    }
    .chat-filter-popover {
        background-color: #ffffff !important;
        border-color: #e4e4e7 !important;
        color: #18181b !important;
    }
    .dark .chat-filter-popover {
        background-color: #18181b !important;
        border-color: #27272a !important;
        color: #f4f4f5 !important;
    }

    /* Ajustes de paddings y gaps que no están en el CSS compilado de Filament */
    .p-3\.5 { padding: 0.875rem !important; }
    .px-3\.5 { padding-left: 0.875rem !important; padding-right: 0.875rem !important; }
    .py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
    .py-2\.5 { padding-top: 0.625rem !important; padding-bottom: 0.625rem !important; }
    .p-2\.5 { padding: 0.625rem !important; }
    .px-3 { padding-left: 0.75rem !important; padding-right: 0.75rem !important; }
    .py-1\.5 { padding-top: 0.375rem !important; padding-bottom: 0.375rem !important; }
    .gap-2 { gap: 0.5rem !important; }
    .gap-3 { gap: 0.75rem !important; }
    .space-x-1\.5 > :not([hidden]) ~ :not([hidden]) {
        margin-left: 0.375rem !important;
    }

    /* Custom badges to prevent outline and background styling issues */
    .chat-channel-badge-whatsapp {
        font-size: 8px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 2px 6px !important;
        border-radius: 4px !important;
        background-color: rgba(16, 185, 129, 0.15) !important;
        color: #10b981 !important;
        border: 1px solid rgba(16, 185, 129, 0.3) !important;
    }
    .chat-channel-badge-queue {
        font-size: 8px !important;
        font-weight: 700 !important;
        padding: 2px 6px !important;
        border-radius: 4px !important;
        background-color: rgba(245, 158, 11, 0.15) !important;
        color: #f59e0b !important;
        border: 1px solid rgba(245, 158, 11, 0.3) !important;
    }
    .chat-channel-badge-user {
        font-size: 8px !important;
        font-weight: 700 !important;
        padding: 2px 6px !important;
        border-radius: 4px !important;
        background-color: rgba(59, 130, 246, 0.15) !important;
        color: #3b82f6 !important;
        border: 1px solid rgba(59, 130, 246, 0.3) !important;
    }

    /* Composer textarea styles */
    .chat-composer-textarea {
        background-color: #ffffff !important;
        color: #18181b !important;
        border-color: #e4e4e7 !important;
    }
    .dark .chat-composer-textarea {
        background-color: #1c1c1e !important;
        color: #f4f4f5 !important;
        border-color: #2c2c2e !important;
    }

    /* Action dropdown styling */
    .chat-dropdown-menu {
        background-color: #ffffff !important;
        border-color: #e4e4e7 !important;
    }
    .dark .chat-dropdown-menu {
        background-color: #1f1f23 !important;
        border-color: #27272a !important;
    }

    /* Modal container and element styles for reliable light/dark mode */
    .chat-modal-container {
        background-color: #ffffff !important;
        color: #18181b !important;
        border: 1px solid #d4d4d8 !important; /* zinc-300 border */
    }
    .dark .chat-modal-container {
        background-color: #18181b !important;
        color: #f4f4f5 !important;
        border: 1px solid #3f3f46 !important; /* zinc-700 border */
    }
    .chat-modal-header {
        border-bottom: 1px solid #e4e4e7 !important;
    }
    .dark .chat-modal-header {
        border-bottom: 1px solid #27272a !important;
    }
    .chat-modal-footer {
        background-color: #f9fafb !important;
        border-top: 1px solid #e4e4e7 !important;
    }
    .dark .chat-modal-footer {
        background-color: #111113 !important;
        border-top: 1px solid #27272a !important;
    }
    .chat-modal-input {
        background-color: #f9fafb !important;
        color: #18181b !important;
        border: 1px solid #e4e4e7 !important;
    }
    .dark .chat-modal-input {
        background-color: #09090b !important;
        color: #f4f4f5 !important;
        border: 1px solid #27272a !important;
    }

    /* Modal typography constraints to match system menus */
    .chat-modal-container h3 {
        font-size: 11px !important;
        font-weight: 700 !important;
    }
    .chat-modal-container p {
        font-size: 10.5px !important;
        line-height: 1.4 !important;
        color: #71717a !important;
    }
    .dark .chat-modal-container p {
        color: #a1a1aa !important;
    }
    .chat-modal-container label {
        font-size: 10px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        font-weight: 600 !important;
        color: #71717a !important;
    }
    .dark .chat-modal-container label {
        color: #a1a1aa !important;
    }
    .chat-modal-container select, 
    .chat-modal-container input, 
    .chat-modal-container textarea {
        font-size: 11px !important;
    }
    .chat-modal-container button {
        font-size: 11px !important;
    }

    /* Green count circles */
    .chat-count-badge {
        position: absolute !important;
        bottom: -5px !important;
        right: -5px !important;
        background-color: #10b981 !important;
        color: #000000 !important;
        font-size: 10px !important;
        font-weight: 800 !important; /* Bold */
        width: 17px !important;
        height: 17px !important;
        border-radius: 9999px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        z-index: 10 !important;
    }

    /* WhatsApp original bubble styling */
    .chat-bubble-inbound {
        background-color: #f0f2f5 !important;
        color: #111b21 !important;
        border: 1px solid #e2e5e9 !important;
    }
    .dark .chat-bubble-inbound {
        background-color: #202c33 !important;
        color: #e9edef !important;
        border: 1px solid #2a3942 !important;
    }
    .chat-bubble-outbound {
        background-color: #d9fdd3 !important;
        color: #111b21 !important;
        border: 1px solid #c1ebd0 !important;
    }
    .dark .chat-bubble-outbound {
        background-color: #005c4b !important;
        color: #e9edef !important;
        border: 1px solid #017560 !important;
    }

    /* Pastel blue avatar circles with padding spacing */
    .chat-avatar-circle {
        background-color: #dbeafe !important; /* light pastel blue */
        color: #1e40af !important; /* dark blue text */
        font-size: 13px !important;
        font-weight: 800 !important;
        width: 34px !important;
        height: 34px !important;
        border-radius: 9999px !important;
        margin-right: 12px !important;
    }
    .dark .chat-avatar-circle {
        background-color: #1e3a8a !important; /* dark pastel blue */
        color: #bfdbfe !important; /* light blue text */
    }

    /* Send button green WhatsApp style */
    .chat-send-btn {
        background-color: #00a884 !important; /* WhatsApp Green */
        color: #ffffff !important;
        border: none !important;
        border-radius: 9999px !important;
        width: 38px !important;
        height: 38px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .chat-send-btn:hover {
        background-color: #008f72 !important;
    }

    /* Popover opacity and solid backgrounds */
    .chat-filter-popover {
        background-color: #f9fafb !important; /* Lighter light gray */
        border-color: #d4d4d8 !important; /* zinc-300 border */
        border-width: 1px !important;
        border-top-width: 0px !important;
        opacity: 1 !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    }
    .dark .chat-filter-popover {
        background-color: #27272a !important; /* Lighter dark gray (zinc-800) */
        border-color: #3f3f46 !important; /* zinc-700 border */
        border-width: 1px !important;
        border-top-width: 0px !important;
        opacity: 1 !important;
    }

    /* Selected conversation card background highlights */
    .chat-conversation-card.active-selected {
        background-color: #ecfdf5 !important; /* emerald-50 (verde claro suave) */
        border-left-color: #10b981 !important; /* emerald-500 */
        border-top-color: #a7f3d0 !important; /* light green border top */
        border-bottom-color: #a7f3d0 !important; /* light green border bottom */
    }
    .dark .chat-conversation-card.active-selected {
        background-color: #27272a !important; /* zinc-800 (gris) */
        border-left-color: #3f3f46 !important; /* zinc-600 */
        border-top-color: #000000 !important; /* black border top */
        border-bottom-color: #000000 !important; /* black border bottom */
    }
</style>

<div class="flex gap-3 overflow-hidden relative z-0 chat-inbox-container rounded-xl" 
     style="height: calc(100vh - 6rem);"
     wire:poll.10s="refreshList"
     x-data="{ openFilters: false, showDetails: false }">
    
    <!-- COLUMNA 1 (Panel de la Izquierda): Lista de Chats y Filtros -->
    <div class="rounded-xl flex flex-col overflow-hidden shadow-sm dark:shadow-2xl border chat-panel-left shrink-0" style="width: 380px;">
        
        <!-- Buscador Superior y Botón Filtros (Popover) -->
        <div class="p-3.5 border-b border-zinc-200 dark:border-black chat-header-bg relative z-20 shrink-0">
            <div class="flex items-center gap-2">

                <!-- Buscador: componente nativo Filament -->
                <div class="flex-1">
                    <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                        <x-filament::input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Nombre, número o email"
                        />
                    </x-filament::input.wrapper>
                </div>

                <!-- Botón Filtros: componente nativo Filament, color primary cuando activo -->
                <x-filament::icon-button
                    icon="heroicon-m-funnel"
                    @click="openFilters = !openFilters"
                    :color="'gray'"
                    x-bind:class="openFilters ? 'fi-color-custom fi-color-primary' : ''"
                    label="Filtros"
                />
            </div>

            <!-- MENU FLOTANTE DE FILTROS (Popover nativo) -->
             <div x-show="openFilters" 
                  @click.away="openFilters = false" 
                  class="absolute z-[40] shadow-2xl overflow-hidden chat-modal-container"
                  style="display: none; left: 10px !important; right: 10px !important; top: 70px !important; border-radius: 12px !important;">
                <div class="p-4 space-y-3">
                    <!-- Filtro Departamento -->
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Departamentos</label>
                        <x-filament::input.wrapper>
                            <select wire:model.live="filterDepartmentId"
                                    class="w-full bg-transparent border-0 text-xs focus:ring-0 text-zinc-900 dark:text-white"
                                    style="outline: none !important; box-shadow: none !important;">
                                <option value="">Todos los departamentos</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </x-filament::input.wrapper>
                    </div>

                    <!-- Filtro Agente -->
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Usuarios</label>
                        <x-filament::input.wrapper>
                            <select wire:model.live="filterUserId"
                                    class="w-full bg-transparent border-0 text-xs focus:ring-0 text-zinc-900 dark:text-white"
                                    style="outline: none !important; box-shadow: none !important;">
                                <option value="">Todos los agentes</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                @endforeach
                            </select>
                        </x-filament::input.wrapper>
                    </div>

                    <!-- Filtro Estado -->
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Estado</label>
                        <x-filament::input.wrapper>
                            <select wire:model.live="filterStatus"
                                    class="w-full bg-transparent border-0 text-xs focus:ring-0 text-zinc-900 dark:text-white"
                                    style="outline: none !important; box-shadow: none !important;">
                                 <option value="">Todos</option>
                                 <option value="open">Abiertos</option>
                                 <option value="snoozed">Pospuestos</option>
                                 <option value="closed">Cerrados (Resueltos)</option>
                            </select>
                        </x-filament::input.wrapper>
                    </div>

                    <!-- Filtro Etiquetas -->
                    <div>
                        <label class="block text-[10px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5">Etiquetas</label>
                        <x-filament::input.wrapper>
                            <select wire:model.live="filterTagId"
                                    class="w-full bg-transparent border-0 text-xs focus:ring-0 text-zinc-900 dark:text-white"
                                    style="outline: none !important; box-shadow: none !important;">
                                <option value="">Todas las etiquetas</option>
                                @foreach($tags as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </x-filament::input.wrapper>
                    </div>
                </div>

                <!-- Footer del Popover -->
                <div class="px-4 py-3 flex items-center justify-end chat-modal-footer">
                    <div style="margin-right: 14px !important; display: inline-block !important;">
                        <x-filament::button color="gray" @click="openFilters = false"
                            wire:click="resetFilters">
                            Limpiar
                        </x-filament::button>
                    </div>
                    <div style="display: inline-block !important;">
                        <x-filament::button @click="openFilters = false">
                            Aplicar
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros Rápidos (Míos, En Espera, Todos) -->
        <div class="px-4 py-3 flex items-center gap-4 border-b border-zinc-200 dark:border-black chat-header-bg shrink-0">

            <!-- Míos Pill -->
            <div class="relative inline-block">
                <button 
                    wire:click="$set('filterScope', 'mine')"
                    class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-150 {{ $filterScope === 'mine' ? 'chat-pill-selected' : 'chat-pill-unselected' }}"
                    type="button">
                    Míos
                </button>
                @if($mineCount > 0)
                    <span class="chat-count-badge shadow-sm">
                         {{ $mineCount }}
                    </span>
                @endif
            </div>

            <!-- En Espera Pill -->
            <div class="relative inline-block">
                <button 
                    wire:click="$set('filterScope', 'unassigned')"
                    class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-150 {{ $filterScope === 'unassigned' ? 'chat-pill-selected' : 'chat-pill-unselected' }}"
                    type="button">
                    En espera
                </button>
                @if($unassignedCount > 0)
                    <span class="chat-count-badge shadow-sm">
                         {{ $unassignedCount }}
                    </span>
                @endif
            </div>

            <!-- Todos Pill -->
            <div class="relative inline-block">
                <button 
                    wire:click="$set('filterScope', 'all')"
                    class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all duration-150 {{ $filterScope === 'all' ? 'chat-pill-selected' : 'chat-pill-unselected' }}"
                    type="button">
                    Todos
                </button>
                @if($allCount > 0)
                    <span class="chat-count-badge shadow-sm">
                         {{ $allCount }}
                    </span>
                @endif
            </div>
        </div>




        <!-- Listado de Chats -->
        <div class="flex-1 overflow-y-auto scrollbar-thin divide-y divide-zinc-200/70 dark:divide-zinc-800/60 chat-panel-left">
            @forelse($conversations as $convo)
                <div wire:click="selectConversation({{ $convo->id }})" 
                     class="p-4 flex items-start space-x-4 cursor-pointer transition duration-155 relative border-l-[4px] chat-conversation-card {{ $activeConversationId === $convo->id ? 'active-selected' : 'hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 border-transparent' }}">
                    
                    <!-- Avatar del contacto -->
                    <div class="w-10 h-10 rounded-full chat-avatar-circle flex items-center justify-center font-extrabold shadow-sm text-sm shrink-0">
                        {{ substr($convo->contact->name ?? $convo->contact->phone ?? 'C', 0, 1) }}
                    </div>

                    <!-- Datos del Chat -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h2 class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">
                                {{ $convo->contact->name ?? $convo->contact->phone }}
                            </h2>
                            <div class="flex items-center space-x-1.5" @click.stop="">
                                <span class="text-[10px] text-zinc-500 dark:text-zinc-450">
                                    {{ $convo->last_message_at ? $convo->last_message_at->format('H:i') : '' }}
                                </span>
                                
                                <!-- Card actions arrow dropdown -->
                                <div x-data="{ openCardMenu: false }" class="relative inline-block text-left">
                                    <button @click="openCardMenu = !openCardMenu" 
                                            class="p-0.5 hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded text-zinc-500 dark:text-zinc-400 transition cursor-pointer border-none"
                                            style="outline: none !important; background: transparent;">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <div x-show="openCardMenu" @click.away="openCardMenu = false" 
                                         class="origin-top-right absolute right-0 mt-1 w-56 rounded-md shadow-lg z-50 chat-dropdown-menu border"
                                         style="right: 0 !important; left: auto !important;">
                                        <div class="py-1">
                                            @if(!$convo->assigned_user_id || $convo->assigned_user_id !== auth()->id())
                                                <button wire:click="selectAndAccept({{ $convo->id }})" @click="openCardMenu = false" class="block w-full text-left px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer border-none whitespace-nowrap" style="background: transparent;">
                                                    Aceptar Chat
                                                </button>
                                            @else
                                                <button wire:click="selectAndResolve({{ $convo->id }})" @click="openCardMenu = false" class="block w-full text-left px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer border-none whitespace-nowrap" style="background: transparent;">
                                                    Resolver
                                                </button>
                                                <button wire:click="selectAndReturnToQueue({{ $convo->id }})" @click="openCardMenu = false" class="block w-full text-left px-4 py-2 text-xs text-zinc-655 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer border-none border-t border-zinc-100 dark:border-zinc-800/50 whitespace-nowrap" style="background: transparent;">
                                                     Devolver a cola
                                                </button>
                                                <button wire:click="selectAndSnooze({{ $convo->id }})" @click="openCardMenu = false" class="block w-full text-left px-4 py-2 text-xs text-zinc-655 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer border-none whitespace-nowrap" style="background: transparent;">
                                                     Posponer chat
                                                </button>
                                                <button wire:click="selectAndTransfer({{ $convo->id }})" @click="openCardMenu = false" class="block w-full text-left px-4 py-2 text-xs text-zinc-655 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer border-none whitespace-nowrap" style="background: transparent;">
                                                     Transferir chat
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate mb-2">
                            {{ $convo->messages->last()?->body ?? 'Sin mensajes aún' }}
                        </p>
                        
                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center space-x-1.5">
                                <span class="chat-channel-badge-whatsapp">
                                    WhatsApp
                                </span>
                                @if($convo->assignedUser)
                                    <span class="chat-channel-badge-user truncate" style="max-width: 120px;">
                                        👤 {{ $convo->assignedUser->name }}
                                    </span>
                                @else
                                    <span class="chat-channel-badge-queue">
                                        Cola
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-start text-center text-zinc-400 dark:text-zinc-500 text-xs min-h-[350px]"
                     style="padding-top: 80px !important; padding-bottom: 40px !important; padding-left: 20px !important; padding-right: 20px !important;">
                    <svg class="w-8 h-8 mb-3 text-zinc-300 dark:text-zinc-655" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span>No se encontraron conversaciones.</span>
                </div>
            @endforelse
        </div>
    </div>

    <!-- COLUMNA 2 (Panel de la Derecha): Chat Activo o Detalle -->
    <div class="flex-1 rounded-xl flex flex-col overflow-visible shadow-sm dark:shadow-2xl border chat-panel-right relative z-10">
        @if($activeConversation)
             <!-- Header del Chat Activo -->
             <div class="py-3 px-4 flex items-center justify-between border-b border-zinc-200 dark:border-black chat-header-bg">
                 <div @click="showDetails = !showDetails" class="flex items-center space-x-4 cursor-pointer hover:opacity-85 transition" title="Haz clic para ver detalles del contacto">
                     <div class="w-10 h-10 rounded-full chat-avatar-circle flex items-center justify-center font-extrabold shadow-sm text-sm shrink-0">
                         {{ substr($activeConversation->contact->name ?? $activeConversation->contact->phone ?? 'C', 0, 1) }}
                     </div>
                     <div class="flex flex-col space-y-0.5">
                         <div class="flex items-center">
                             <h2 class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">
                                 {{ $activeConversation->contact->name ?? $activeConversation->contact->phone }}
                             </h2>
                         </div>
                         <div class="flex items-center space-x-2 text-[10px] text-zinc-400 dark:text-zinc-500 leading-none mt-0.5" style="font-size: 10px !important;">
                             <span>Asignado a: <span class="text-zinc-500 dark:text-zinc-400 font-medium">{{ $activeConversation->assignedUser?->name ?? 'Sin asignar' }}</span></span>
                             <span>•</span>
                             <span class="flex items-center">
                                 <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 shrink-0 inline-block"></span>
                                 <span>{{ $activeConversation->channelConnection?->name ?? 'Conexión' }}</span>
                             </span>
                         </div>
                     </div>
                 </div>

                <!-- Botonera de Acciones -->
                <div class="flex items-center space-x-2">
                    @if(!$activeConversation->assigned_user_id || $activeConversation->assigned_user_id !== auth()->id())
                        <!-- Aceptar Conversación -->
                        <button wire:click="accept" 
                                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-xs font-bold rounded-md transition duration-200 shadow-sm text-white cursor-pointer border-none" 
                                style="outline: none !important;">
                            <span>Aceptar Chat</span>
                        </button>
                    @else
                        <!-- Resolver (Cerrar) -->
                        <button wire:click="openResolveModal" 
                                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-xs font-bold rounded-l-md transition text-white cursor-pointer border-none" 
                                style="outline: none !important;">
                            Resolver
                        </button>
                        
                        <!-- Acciones Adicionales Dropdown -->
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open" 
                                    class="px-2 py-2 bg-amber-500 hover:bg-amber-600 text-xs font-bold rounded-r-md transition text-white cursor-pointer border-none" 
                                    style="outline: none !important; border-left: 1px solid rgba(255,255,255,0.15) !important;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                             <div x-show="open" @click.away="open = false" 
                                 class="origin-top-right absolute mt-2 w-56 rounded-md shadow-lg z-50 chat-dropdown-menu border"
                                 style="right: 0 !important; left: auto !important;">
                                <div class="py-1">
                                    <button wire:click="keepWithMe" @click="open = false" class="block w-full text-left px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer border-none whitespace-nowrap" style="background: transparent;">
                                        Mantener conmigo
                                    </button>
                                    <button wire:click="returnToQueue" @click="open = false" class="block w-full text-left px-4 py-2 text-xs text-zinc-655 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer border-none border-t border-zinc-100 dark:border-zinc-800/50 whitespace-nowrap" style="background: transparent;">
                                        Devolver a cola
                                    </button>
                                    <button wire:click="openSnoozeModal" @click="open = false" class="block w-full text-left px-4 py-2 text-xs text-zinc-655 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer border-none whitespace-nowrap" style="background: transparent;">
                                        Posponer chat
                                    </button>
                                    <button wire:click="openTransferModal" @click="open = false" class="block w-full text-left px-4 py-2 text-xs text-zinc-655 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer border-none whitespace-nowrap" style="background: transparent;">
                                        Transferir chat
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Listado de Mensajes (con Fondo Doodle de WhatsApp) -->
            <div class="flex-1 p-6 overflow-y-auto scrollbar-thin flex flex-col space-y-4 relative z-0 chat-messages-area"
                 x-init="$nextTick(() => { $el.scrollTop = $el.scrollHeight })"
                 x-on:message-sent.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight })">
                
                <!-- Doodle Background Layer Overlay -->
                <div class="absolute inset-0 pointer-events-none z-[-1] chat-doodle-overlay" 
                     style="opacity: 0.03; background-image: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2280%22 height=%2280%22 viewBox=%220 0 80 80%22><path d=%22M40 0 C20 0 0 20 0 40 C0 60 20 80 40 80 C60 80 80 60 80 40 C80 20 60 0 40 0 Z M40 10 C50 10 60 20 60 30 C60 40 50 50 40 50 C30 50 20 40 20 30 C20 20 30 10 40 10 Z%22 fill=%22%23888888%22/></svg>'); background-repeat: repeat; background-size: 120px 120px;">
                </div>

                @php
                    $lastDate = null;
                @endphp

                @foreach($messages as $msg)
                    @php
                        $msgDate = $msg->sent_at ? $msg->sent_at->format('Y-m-d') : $msg->created_at->format('Y-m-d');
                    @endphp

                    @if($lastDate !== $msgDate)
                        <!-- Separador de fecha -->
                        <div class="flex justify-center my-2">
                            <span class="text-[10px] px-3 py-1 rounded font-semibold bg-zinc-200 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400">
                                @if($msgDate === now()->format('Y-m-d'))
                                    Hoy
                                @elseif($msgDate === now()->subDay()->format('Y-m-d'))
                                    Ayer
                                @else
                                    {{ $msg->sent_at ? $msg->sent_at->format('d/m/Y') : $msg->created_at->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>
                        @php $lastDate = $msgDate; @endphp
                    @endif

                    @if($msg->sender_type === 'system')
                        <!-- Mensaje de Sistema / Auditoría -->
                        <div class="flex justify-center my-1">
                            <div class="px-3 py-1.5 rounded-lg text-[10px] italic bg-zinc-100 dark:bg-zinc-800 text-zinc-500 dark:text-zinc-400">
                                {{ $msg->body }}
                            </div>
                        </div>
                    @else
                        <div class="flex {{ $msg->direction === 'inbound' ? 'justify-start' : 'justify-end' }} mb-2">
                            <div class="flex flex-col {{ $msg->direction === 'inbound' ? 'items-start' : 'items-end' }}" style="max-width: 70%;">
                                 <div class="px-3.5 py-2 rounded-xl text-xs leading-relaxed shadow-sm {{ $msg->direction === 'inbound' ? 'chat-bubble-inbound' : 'chat-bubble-outbound' }}">
                                     @if($msg->direction === 'outbound' && $msg->sender_user_id)
                                         <div class="font-bold text-[10px] mb-0.5 opacity-80">
                                             {{ \App\Models\User::find($msg->sender_user_id)?->name ?? 'IA' }}:
                                         </div>
                                     @endif
                                     {!! nl2br(e($msg->body)) !!}
                                 </div>

                                <span class="text-[9px] text-zinc-500 mt-1 px-1 flex items-center space-x-1">
                                    <span>{{ $msg->sent_at ? $msg->sent_at->format('H:i') : $msg->created_at->format('H:i') }}</span>
                                    @if($msg->direction === 'outbound')
                                        <span class="text-amber-500">
                                            @if($msg->status === 'sent' || $msg->status === 'delivered' || $msg->status === 'read')
                                                ✔
                                            @elseif($msg->status === 'failed')
                                                <span class="text-red-500" title="{{ $msg->metadata['error'] ?? 'Falla de envío' }}">⚠️</span>
                                            @else
                                                ⏳
                                            @endif
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Entrada de Mensajes (Composer) con Respuestas Rápidas -->
            <div class="p-3 border-t chat-header-bg"
                 x-data="{
                    allReplies: @js($quickReplies->map(fn($r) => ['id' => $r->id, 'shortcut' => $r->shortcut, 'title' => $r->title, 'body' => $r->body])->values()),
                    showPicker: false,
                    pickerQuery: '',
                    selectedIndex: 0,
                    get filteredReplies() {
                        if (!this.pickerQuery) return this.allReplies;
                        const q = this.pickerQuery.toLowerCase();
                        return this.allReplies.filter(r =>
                            r.shortcut.toLowerCase().includes(q) ||
                            r.title.toLowerCase().includes(q)
                        );
                    },
                    onInput(event) {
                        const val = event.target.value;
                        const match = val.match(/(?:^|\s)\/(\S*)$/);
                        if (match !== null) {
                            this.pickerQuery = match[1];
                            this.showPicker = true;
                            this.selectedIndex = 0;
                        } else {
                            this.showPicker = false;
                            this.pickerQuery = '';
                        }
                    },
                    onKeydown(event) {
                        if (!this.showPicker) {
                             if (event.key === 'Enter' && !event.shiftKey) {
                                 event.preventDefault();
                                 $wire.sendMessage();
                             }
                             return;
                         }
                         if (event.key === 'ArrowDown') {
                             event.preventDefault();
                             this.selectedIndex = Math.min(this.selectedIndex + 1, this.filteredReplies.length - 1);
                         } else if (event.key === 'ArrowUp') {
                             event.preventDefault();
                             this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
                         } else if (event.key === 'Enter' || event.key === 'Tab') {
                             event.preventDefault();
                             if (this.filteredReplies[this.selectedIndex]) {
                                 this.insertReply(this.filteredReplies[this.selectedIndex]);
                             }
                         } else if (event.key === 'Escape') {
                             this.showPicker = false;
                         }
                     },
                    insertReply(reply) {
                        const textarea = this.$refs.composer;
                        let body = reply.body;
                        // Interpolar variables dinámicas del lado cliente
                        const contactName   = @js($activeConversation?->contact?->name ?? '');
                        const contactNumber = @js($activeConversation?->contact?->phone ?? '');
                        const contactEmail  = @js($activeConversation?->contact?->email ?? '');
                        const userName      = @js(auth()->user()?->name ?? '');
                        const hour = new Date().getHours();
                        const greeting = hour < 12 ? 'Buenos días' : hour < 19 ? 'Buenas tardes' : 'Buenas noches';
                        body = body
                            .replace(/\{\{contactName\}\}/g, contactName)
                            .replace(/\{\{contactNumber\}\}/g, contactNumber)
                            .replace(/\{\{contactEmail\}\}/g, contactEmail)
                            .replace(/\{\{userName\}\}/g, userName)
                            .replace(/\{\{greeting\}\}/g, greeting);
                        // Reemplaza el /comando en el texto con el body
                        const current = textarea.value;
                        const replaced = current.replace(/(?:^|\s)\/\S*$/, (m) => m.startsWith(' ') ? ' ' + body : body);
                        textarea.value = replaced;
                        // Sincronizar con Livewire
                        textarea.dispatchEvent(new Event('input'));
                        this.showPicker = false;
                        this.pickerQuery = '';
                        textarea.focus();
                    }
                 }">

                <!-- Picker de Respuestas Rápidas -->
                <div x-show="showPicker && filteredReplies.length > 0"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mb-2 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-xl overflow-hidden"
                     style="display:none; background: var(--chat-panel-bg, white);"
                     :style="document.documentElement.classList.contains('dark') ? 'background:#18181b;' : 'background:#fff;'">
                    <div class="px-3 py-1.5 border-b border-zinc-100 dark:border-zinc-800 flex items-center gap-1.5">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Respuestas rápidas</span>
                        <span class="text-[10px] text-zinc-400">— escribe para filtrar · ↑↓ navegar · Enter insertar · Esc cerrar</span>
                    </div>
                    <ul class="max-h-52 overflow-y-auto py-1">
                        <template x-for="(reply, idx) in filteredReplies" :key="reply.id">
                            <li @click="insertReply(reply)"
                                :class="idx === selectedIndex
                                    ? 'bg-amber-50 dark:bg-amber-900/20 cursor-pointer'
                                    : 'hover:bg-zinc-50 dark:hover:bg-zinc-800/60 cursor-pointer'"
                                class="px-3 py-2 flex items-start gap-3 transition-colors">
                                <span class="shrink-0 mt-0.5 inline-block font-mono text-[10px] font-bold px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-600 dark:text-zinc-300"
                                      x-text="'/' + reply.shortcut"></span>
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate" x-text="reply.title"></div>
                                    <div class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate mt-0.5" x-text="reply.body"></div>
                                </div>
                            </li>
                        </template>
                        <li x-show="filteredReplies.length === 0" class="px-3 py-3 text-xs text-zinc-400 text-center">
                            Sin resultados
                        </li>
                    </ul>
                </div>

                <form wire:submit.prevent="sendMessage" class="flex items-center space-x-2">
                    <textarea wire:model="newMessageBody"
                              x-ref="composer"
                              @input="onInput($event); $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                              @keydown="onKeydown($event)"
                              x-on:keydown.ctrl.enter.prevent="showPicker = false; $wire.sendMessage()"
                              placeholder="Escriba un mensaje o / para respuestas rápidas"
                              rows="1"
                              class="flex-1 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-0 focus:border-amber-500 transition text-sm chat-composer-textarea border resize-none"
                              style="outline: none !important; box-shadow: none !important; min-height: 38px; max-height: 120px; overflow-y: auto;"></textarea>

                    <button type="button"
                            wire:click="openScheduleModal"
                            class="p-2.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-zinc-600 dark:text-zinc-300 rounded-lg shadow-sm transition duration-200 flex items-center justify-center cursor-pointer border-none"
                            style="outline: none !important;"
                            title="Programar Mensaje">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>

                    <button type="submit"
                            class="p-2.5 chat-send-btn text-white rounded-lg shadow-md transition duration-200 flex items-center justify-center cursor-pointer border-none"
                            style="outline: none !important;">
                        <svg class="w-4.5 h-4.5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <!-- Panel Vacío de Selección -->
            <div class="flex-1 flex flex-col items-center justify-center p-8 chat-panel-right">
                <div class="w-16 h-16 rounded-full flex items-center justify-center text-zinc-400 text-xl shadow mb-4 border chat-header-bg">
                    💬
                </div>
                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Bandeja de Entrada</h4>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 text-center max-w-xs mt-1">
                    Selecciona una conversación de la lista izquierda para comenzar a chatear.
                </p>
            </div>
        @endif
    </div>

    <!-- COLUMNA 3 (Detalles de Contacto) -->
    @if($activeConversation)
    <div x-show="showDetails" x-transition
         class="rounded-xl flex flex-col overflow-hidden shadow-sm dark:shadow-2xl border chat-panel-left shrink-0" style="width: 280px; display: none;">
        <div class="p-4 border-b chat-header-bg">
            <h3 class="font-bold text-sm text-zinc-900 dark:text-zinc-100">Detalles de Contacto</h3>
        </div>
        <div class="p-4 flex-1 overflow-y-auto scrollbar-thin">
            <!-- Info básica -->
            <div class="flex flex-col items-center mb-6">
                <div class="w-16 h-16 rounded-full bg-[#10b981] flex items-center justify-center font-bold text-white shadow-sm text-2xl mb-3">
                    {{ substr($activeConversation->contact->name ?? $activeConversation->contact->phone ?? 'C', 0, 1) }}
                </div>
                <h4 class="font-bold text-zinc-900 dark:text-zinc-100 text-center">{{ $activeConversation->contact->name ?? 'Sin Nombre' }}</h4>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 text-center">{{ $activeConversation->contact->phone }}</p>
                @if($activeConversation->contact->email)
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 text-center">{{ $activeConversation->contact->email }}</p>
                @endif
            </div>

            <!-- Etiquetas -->
            @if($activeConversation->tags->isNotEmpty())
            <div class="mb-5">
                <h5 class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-2 uppercase tracking-wide">Etiquetas</h5>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($activeConversation->tags as $tag)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Detalles adicionales -->
            <div class="space-y-3 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                <div>
                    <span class="block text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Canal</span>
                    <span class="text-xs text-zinc-800 dark:text-zinc-200">{{ $activeConversation->channelConnection->channel_type ?? 'Desconocido' }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Departamento</span>
                    <span class="text-xs text-zinc-800 dark:text-zinc-200">{{ $activeConversation->department->name ?? 'Sin asignar' }}</span>
                </div>
                <div>
                    <span class="block text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Estado</span>
                    <span class="text-xs text-zinc-800 dark:text-zinc-200 capitalize">{{ trans('chat.'.$activeConversation->status) ?? $activeConversation->status }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL: Resolver Conversación -->
    @if($showResolveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md transition-opacity duration-300">
            <div class="rounded-xl shadow-xl w-full max-w-sm overflow-hidden transform scale-100 transition-all duration-300 chat-modal-container border">
                <div class="p-4 flex items-center justify-between chat-modal-header">
                    <h3 class="font-bold text-xs">Resolver conversación</h3>
                    <button wire:click="closeResolveModal" class="text-zinc-500 hover:text-zinc-300 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-4 space-y-4">
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                        Selecciona el motivo de contacto para cerrar este chat.
                    </p>
                    
                    @if($contactReasons->isNotEmpty())
                    <div>
                        <label class="block text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1">Motivo del contacto (Opcional)</label>
                        <select wire:model="resolveReasonId" 
                                class="w-full rounded-lg p-2 text-xs focus:outline-none chat-modal-input">
                            <option value="">-- Ninguno --</option>
                            @foreach($contactReasons as $reason)
                                <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <label class="flex items-center space-x-2 cursor-pointer mt-2">
                        <input type="checkbox" wire:model="resolveWithoutGoodbye" class="rounded border-zinc-300 text-amber-500 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50">
                        <span class="text-xs">Resolver sin enviar mensaje de despedida</span>
                    </label>
                </div>

                <div class="p-4 flex items-center justify-end space-x-1.5 chat-modal-footer">
                    <button wire:click="closeResolveModal" 
                            class="px-3 py-1.5 text-[10px] font-semibold rounded transition cursor-pointer chat-modal-input">
                        Cancelar
                    </button>
                    <button wire:click="confirmResolve" 
                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-[10px] font-semibold rounded text-white shadow transition cursor-pointer border-none">
                        Confirmar y Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: Posponer Conversación (Snooze) -->
    @if($showSnoozeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md transition-opacity duration-300">
            <div class="rounded-xl shadow-xl w-full max-w-sm overflow-hidden transform scale-100 transition-all duration-300 chat-modal-container border">
                <div class="p-4 flex items-center justify-between chat-modal-header">
                    <h3 class="font-bold text-xs">Posponer conversación</h3>
                    <button wire:click="closeSnoozeModal" class="text-zinc-500 hover:text-zinc-300 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-4 space-y-3">
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                        El chat se ocultará de los pendientes y volverá a aparecer al expirar el tiempo.
                    </p>
                    <div>
                        <label class="block text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1">Posponer por</label>
                        <select wire:model="snoozeHours" 
                                class="w-full rounded-lg p-2 text-xs focus:outline-none chat-modal-input">
                            <option value="2">2 horas</option>
                            <option value="4">4 horas</option>
                            <option value="8">8 horas</option>
                            <option value="24">24 horas (1 día)</option>
                            <option value="48">48 horas (2 días)</option>
                        </select>
                    </div>
                </div>

                <div class="p-4 flex items-center justify-end space-x-1.5 chat-modal-footer">
                    <button wire:click="closeSnoozeModal" 
                            class="px-3 py-1.5 text-[10px] font-semibold rounded transition cursor-pointer chat-modal-input">
                        Cancelar
                    </button>
                    <button wire:click="snooze" 
                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-[10px] font-semibold rounded text-white shadow transition cursor-pointer border-none">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: Transferir Conversación -->
    @if($showTransferModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md transition-opacity duration-300">
            <div class="rounded-xl shadow-xl w-full max-w-sm overflow-hidden transform scale-100 transition-all duration-300 chat-modal-container border">
                <div class="p-4 flex items-center justify-between chat-modal-header">
                    <h3 class="font-bold text-xs">Transferir conversación</h3>
                    <button wire:click="closeTransferModal" class="text-zinc-500 hover:text-zinc-300 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-4 space-y-3">
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                        Asigna este chat a otro agente o departamento de destino.
                    </p>

                    <div>
                        <label class="block text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1">Departamento de Destino</label>
                        <select wire:model="transferDepartmentId" 
                                class="w-full rounded-lg p-2.5 text-xs focus:outline-none chat-modal-input">
                            <option value="">Mantener depto. actual</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1">Agente de Destino</label>
                        <select wire:model="transferUserId" 
                                class="w-full rounded-lg p-2.5 text-xs focus:outline-none chat-modal-input">
                            <option value="">Cola (Sin agente)</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }} ({{ $agent->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1">Descripción / Motivo del traspaso</label>
                        <textarea wire:model="transferReason" 
                                  placeholder="Escribe el motivo del traspaso..."
                                  rows="2"
                                  class="w-full rounded-lg p-2.5 text-xs focus:outline-none chat-modal-input"></textarea>
                    </div>
                </div>

                <div class="p-4 flex items-center justify-end space-x-1.5 chat-modal-footer">
                    <button wire:click="closeTransferModal" 
                            class="px-3 py-1.5 text-[10px] font-semibold rounded transition cursor-pointer chat-modal-input">
                        Cancelar
                    </button>
                    <button wire:click="transfer" 
                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-[10px] font-semibold rounded text-white shadow transition cursor-pointer border-none">
                        Transferir
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL: Programar Mensaje -->
    @if($showScheduleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90 backdrop-blur-md transition-opacity duration-300">
            <div class="rounded-xl shadow-xl w-full max-w-sm overflow-hidden transform scale-100 transition-all duration-300 chat-modal-container border">
                <div class="p-4 flex items-center justify-between chat-modal-header">
                    <h3 class="font-bold text-xs">Programar mensaje</h3>
                    <button wire:click="closeScheduleModal" class="text-zinc-500 hover:text-zinc-300 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-4 space-y-4">
                    <!-- Fecha y Hora -->
                    <div>
                        <label class="block text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1">Fecha y Hora de Envío</label>
                        <input type="datetime-local" 
                               wire:model="scheduleSendAt" 
                               class="w-full rounded-lg p-2.5 text-xs focus:outline-none chat-modal-input"
                               required>
                    </div>

                    <!-- Selección de Plantilla (Opcional) -->
                    <div>
                        <label class="block text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1">Seleccionar Plantilla (Opcional)</label>
                        <select wire:model.live="scheduleTemplateId" 
                                class="w-full rounded-lg p-2.5 text-xs focus:outline-none chat-modal-input">
                            <option value="">-- Mensaje de texto libre --</option>
                            @foreach($templates as $tmpl)
                                <option value="{{ $tmpl->id }}">{{ $tmpl->name }} ({{ $tmpl->language }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Relleno de variables dinámicas si hay plantilla seleccionada -->
                    @if($selectedTemplateForSchedule)
                        @php
                            $varCount = count($selectedTemplateVariablesForSchedule);
                        @endphp
                        
                        @if($varCount > 0)
                            <div class="space-y-3 p-3 bg-zinc-50 dark:bg-zinc-950/30 rounded-lg border border-zinc-200/50 dark:border-zinc-800/50">
                                <h4 class="text-[10px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-1">Variables de la Plantilla</h4>
                                
                                @foreach($selectedTemplateVariablesForSchedule as $index => $varValue)
                                    <div>
                                        <label class="block text-[10px] font-semibold text-zinc-600 dark:text-zinc-400 mb-1">
                                            Placeholder <?php echo '{{' . ($index + 1) . '}}'; ?>
                                        </label>
                                        <input type="text" 
                                               wire:model="scheduleVariables.{{ $index }}" 
                                               class="w-full rounded p-2 text-xs focus:outline-none chat-modal-input"
                                               placeholder="Escribe el valor para este campo..."
                                               required>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded text-[10px] font-medium">
                                Esta plantilla no requiere variables dinámicas.
                            </div>
                        @endif
                    @endif

                    <!-- Mensaje libre / Vista previa -->
                    <div>
                        <label class="block text-[10px] font-semibold text-zinc-500 dark:text-zinc-400 mb-1">
                            @if($selectedTemplateForSchedule) Vista Previa del Mensaje @else Mensaje a enviar @endif
                        </label>
                        <textarea wire:model="scheduleMessageBody" 
                                  rows="3" 
                                  class="w-full rounded-lg p-2.5 text-xs focus:outline-none chat-modal-input resize-none"
                                  placeholder="Escribe el mensaje..."
                                  @if($selectedTemplateForSchedule) readonly @endif
                                  required></textarea>
                    </div>
                </div>

                <div class="p-4 flex items-center justify-end space-x-1.5 chat-modal-footer">
                    <button wire:click="closeScheduleModal" 
                            class="px-3 py-1.5 text-[10px] font-semibold rounded transition cursor-pointer chat-modal-input">
                        Cancelar
                    </button>
                    <button wire:click="scheduleMessage" 
                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-[10px] font-semibold rounded text-white shadow transition cursor-pointer border-none">
                        Programar Envío
                    </button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
