@extends('layouts.app', ['title' => 'Store Operations • Mobile Audit'])

@section('content')
<!-- Centered Smartphone Simulation Container for Mobile-First Experience -->
<div class="max-w-md mx-auto py-2" x-data="{ 
    proofModal: false, 
    verifyModal: false,
    selectedTask: null,
    previewProofModal: false,
    previewMasterModal: false,
    selectedAsset: null,
    selectedPhotoPreset: 'gondola',
    proofNotes: 'Physical POSM installed securely per branch merchandising guidelines.',
    customPhotoUrl: '',

    // Simulated Offline Sync State
    syncing: false,
    syncStage: 'idle',
    syncProgress: 0,
    syncMessage: '',

    triggerCamera() {
        document.getElementById('mobileCameraInput').click();
    },

    handleCameraCapture(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = (ev) => {
                this.customPhotoUrl = ev.target.result;
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    },

    startOfflineSync() {
        this.syncing = true;
        this.syncProgress = 15;
        this.syncMessage = 'Detecting low backroom connectivity (Chiller Storage Zone)...';

        setTimeout(() => {
            this.syncProgress = 45;
            this.syncMessage = 'Offline: Encrypting proof & caching in device storage...';
        }, 1000);

        setTimeout(() => {
            this.syncProgress = 85;
            this.syncMessage = 'Signal acquired! Syncing packet to Naivas Cloud Gateway...';
        }, 2000);

        setTimeout(() => {
            this.syncProgress = 100;
            this.syncMessage = 'Cloud Sync Complete! Dispatched audit notification to CMO.';
            setTimeout(() => {
                this.$refs.proofForm.submit();
            }, 600);
        }, 3000);
    }
}">

    <!-- Smartphone Bezel Outer Shell -->
    <div class="bg-white rounded-[38px] border-4 border-slate-300 shadow-2xl overflow-hidden relative border-t-[8px]">
        
        <!-- Mobile Status Bar (Simulated iPhone / Android Notch & Indicators) -->
        <div class="bg-slate-900 text-white px-6 pt-2 pb-1.5 flex items-center justify-between text-[11px] font-semibold tracking-tight">
            <span>{{ date('H:i') }}</span>
            <div class="w-16 h-3 bg-black rounded-full mx-auto"></div>
            <div class="flex items-center space-x-1.5 text-slate-300">
                <span class="text-[9px] font-bold">5G</span>
                <!-- Battery Icon -->
                <svg class="w-3.5 h-3.5 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>

        <!-- Mobile Header Card -->
        <div class="bg-white border-b border-slate-200 px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center space-x-1.5">
                        <span class="text-[10px] font-bold text-[#F7941D] uppercase tracking-wider">Module 5 • In-Store Ops</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 leading-tight">{{ Auth::user()?->name ?? 'James Wilson' }}</h2>
                    <p class="text-xs text-slate-500">{{ $currentBranch }} Manager</p>
                </div>

                <div class="text-right">
                    <span class="text-[11px] text-slate-400 block">Health</span>
                    <span class="text-sm font-extrabold text-[#F7941D]">{{ $completionRate }}%</span>
                </div>
            </div>

            <!-- Branch Rollout Progress Bar -->
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2.5 overflow-hidden">
                <div class="bg-[#F7941D] h-1.5 rounded-full" style="width: {{ $completionRate }}%"></div>
            </div>

            <!-- Branch Quick Switcher Chips -->
            <div class="flex items-center space-x-1 mt-3 overflow-x-auto pb-1 text-[11px]">
                @foreach($branches as $branch)
                <a href="{{ route('ome.dashboard', ['branch' => $branch]) }}" 
                   class="px-2.5 py-1 rounded-full whitespace-nowrap font-medium transition {{ $currentBranch === $branch ? 'bg-[#F7941D] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ str_replace('Naivas ', '', $branch) }}
                </a>
                @endforeach
            </div>
        </div>

        <!-- Mobile Content Checklist Body -->
        <div class="p-4 space-y-3 bg-slate-50/60 max-h-[580px] overflow-y-auto">
            
            <div class="flex items-center justify-between text-xs text-slate-600 mb-1">
                <span class="font-bold text-slate-800 uppercase tracking-wider text-[10px]">Active POSM Tasks ({{ $tasks->count() }})</span>
                <span class="text-[11px] text-slate-400">{{ $executedPendingVerification + $verifiedTasks }} Done</span>
            </div>

            @forelse($tasks as $task)
            <div class="bg-white rounded-xl p-3.5 border border-slate-200 shadow-xs space-y-2.5 transition">
                
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1">
                        <div class="flex items-center space-x-1.5 text-[10px] text-slate-400">
                            <span class="font-mono font-bold">{{ $task->campaign->code }}</span>
                            <span>•</span>
                            <span class="text-[#F7941D] font-medium">{{ $task->zone }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 mt-0.5 leading-snug">{{ $task->task_name }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $task->campaign->title }}</p>
                    </div>

                    <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider
                        @if($task->status === 'verified') bg-emerald-50 text-emerald-700 border border-emerald-200
                        @elseif($task->status === 'executed_pending_verification') bg-orange-50 text-[#F7941D] border border-orange-200
                        @elseif($task->status === 'redo_requested') bg-rose-50 text-rose-700 border border-rose-300 ring-1 ring-rose-200 animate-pulse
                        @else bg-slate-100 text-slate-600 @endif">
                        @if($task->status === 'executed_pending_verification') Synced
                        @elseif($task->status === 'verified') Live
                        @elseif($task->status === 'redo_requested') Redo Needed
                        @else Pending @endif
                    </span>
                </div>

                @if($task->status === 'redo_requested' && $task->redo_instructions)
                <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 space-y-1 text-xs">
                    <div class="flex items-center space-x-1.5 text-rose-800 font-bold text-[11px]">
                        <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Executive HQ Feedback • Redo Required:</span>
                    </div>
                    <p class="text-rose-900 text-[11px] leading-snug italic font-medium">"{{ $task->redo_instructions }}"</p>
                    <div class="flex items-center justify-between text-[10px] text-rose-600 pt-0.5">
                        <span>From: David Miller (CMO)</span>
                        @if($task->redo_requested_at)
                        <span>{{ $task->redo_requested_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
                @endif

                <!-- IMC Approved Master Artwork Card -->
                @php
                    $approvedArtwork = $task->approved_asset;
                @endphp
                @if($approvedArtwork)
                <div class="p-2.5 rounded-xl bg-orange-50/80 border border-orange-200 text-xs space-y-1.5 shadow-2xs">
                    <div class="flex items-center justify-between text-[10px]">
                        <span class="font-bold uppercase tracking-wider text-[#F7941D] flex items-center space-x-1">
                            <svg class="w-3 h-3 text-[#F7941D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>IMC Approved Artwork</span>
                        </span>
                        <span class="px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-bold text-[9px]">
                            ✓ Sarah Jenkins Sign-off
                        </span>
                    </div>

                    <div class="flex items-center space-x-2.5">
                        @if($approvedArtwork->file_url)
                        <div class="w-12 h-12 rounded-lg bg-white border border-orange-200 shrink-0 overflow-hidden cursor-pointer shadow-2xs hover:scale-105 transition"
                             @click="selectedAsset = {{ json_encode($approvedArtwork) }}; previewMasterModal = true"
                             title="Tap to preview high-res approved artwork">
                            <img src="{{ $approvedArtwork->file_url }}" alt="{{ $approvedArtwork->title }}" class="w-full h-full object-cover">
                        </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-slate-900 text-[11px] truncate">{{ $approvedArtwork->title }}</h5>
                            <div class="text-[10px] text-slate-600 mt-0.5">
                                <span class="font-semibold text-slate-800">{{ $approvedArtwork->dimensions ?: 'Standard' }}</span> • {{ $approvedArtwork->format ?: 'Print-Ready PDF' }}
                            </div>
                            @if($approvedArtwork->approval_notes)
                            <p class="text-[9px] text-slate-500 italic mt-0.5 truncate">"{{ $approvedArtwork->approval_notes }}"</p>
                            @endif
                        </div>

                        <button type="button" 
                                @click="selectedAsset = {{ json_encode($approvedArtwork) }}; previewMasterModal = true"
                                class="px-2 py-1 rounded-md bg-white hover:bg-orange-100 border border-orange-300 text-[#F7941D] text-[10px] font-bold transition shrink-0 shadow-2xs">
                            Inspect
                        </button>
                    </div>
                </div>
                @endif

                <!-- Proof Thumbnail if exists -->
                @if($task->proof_image_url)
                <div class="w-full h-28 rounded-lg bg-slate-100 overflow-hidden cursor-pointer relative"
                     @click="selectedTask = {{ json_encode($task) }}; previewProofModal = true">
                    <img src="{{ $task->proof_image_url }}" alt="{{ $task->task_name }}" class="w-full h-full object-cover">
                    <span class="absolute bottom-1 right-1 bg-black/60 text-white text-[9px] px-1.5 py-0.5 rounded">View Proof</span>
                </div>
                @endif

                <!-- Mobile Action Button -->
                <div class="pt-1">
                    @if($task->status === 'pending' || $task->status === 'in_progress')
                        <button type="button" 
                                @click="selectedTask = {{ json_encode($task) }}; proofModal = true"
                                class="w-full py-2 px-3 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-bold shadow-xs transition flex items-center justify-center space-x-2">
                            <!-- Camera Icon -->
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Upload Proof of Execution</span>
                        </button>
                    @elseif($task->status === 'redo_requested')
                        <button type="button" 
                                @click="selectedTask = {{ json_encode($task) }}; proofModal = true"
                                class="w-full py-2 px-3 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>Re-Upload Corrected Proof</span>
                        </button>
                    @elseif($task->status === 'executed_pending_verification')
                        <div class="p-2 rounded-lg bg-orange-50/70 border border-orange-200 text-center text-[11px] text-[#F7941D] font-semibold flex items-center justify-center space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Executed • Synced to Executive Hub</span>
                        </div>
                    @else
                        <div class="p-2 rounded-lg bg-emerald-50 border border-emerald-200 text-center text-[11px] text-emerald-800 font-semibold flex items-center justify-center space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Verified Live In-Store</span>
                        </div>
                    @endif
                </div>

            </div>
            @empty
            <div class="py-12 text-center text-slate-400 text-xs">No tasks scheduled for {{ $currentBranch }}.</div>
            @endforelse

        </div>

        <!-- Mobile Bottom Nav Bar (Simulated Home Indicator) -->
        <div class="bg-white border-t border-slate-200 p-3 text-center">
            <div class="w-28 h-1 bg-slate-300 rounded-full mx-auto"></div>
        </div>

    </div>

    <!-- Hidden Native Camera File Input -->
    <input type="file" id="mobileCameraInput" accept="image/*" capture="environment" class="hidden" @change="handleCameraCapture($event)">

    <!-- Upload Proof Modal with Simulated Offline Sync -->
    <div x-show="proofModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl border border-slate-200 text-slate-800" @click.away="if (!syncing) proofModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                <h3 class="text-sm font-bold text-slate-900">In-Store Mobile Proof Audit</h3>
                <button type="button" @click="proofModal = false" :disabled="syncing" class="text-slate-400 hover:text-slate-600 disabled:opacity-30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="selectedTask">
                <form :action="'/ome/tasks/' + selectedTask.id + '/proof'" method="POST" x-ref="proofForm" class="space-y-3.5 text-xs">
                    @csrf
                    
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="font-bold text-slate-900 text-xs" x-text="selectedTask.task_name"></div>
                        <div class="text-[11px] text-slate-500 mt-0.5" x-text="selectedTask.branch_name + ' • ' + selectedTask.zone"></div>
                    </div>

                    <template x-if="selectedTask.status === 'redo_requested' && selectedTask.redo_instructions">
                        <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-xs space-y-1">
                            <div class="font-bold text-rose-800 flex items-center space-x-1 text-[11px]">
                                <svg class="w-3.5 h-3.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Executive Feedback to Address:</span>
                            </div>
                            <p class="text-rose-900 text-[11px] italic font-medium leading-relaxed" x-text="'&quot;' + selectedTask.redo_instructions + '&quot;'"></p>
                        </div>
                    </template>

                    <!-- Reference Approved Master Artwork from IMC -->
                    <template x-if="selectedTask && selectedTask.approved_asset">
                        <div class="p-2.5 rounded-lg bg-orange-50 border border-orange-200 text-xs space-y-1.5">
                            <div class="flex items-center justify-between text-[10px]">
                                <span class="font-bold text-slate-800 flex items-center space-x-1">
                                    <svg class="w-3.5 h-3.5 text-[#F7941D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>IMC Approved Master Reference:</span>
                                </span>
                                <span class="text-[9px] font-bold text-[#F7941D] uppercase">Match Before Photo</span>
                            </div>
                            <div class="flex items-center space-x-2.5">
                                <template x-if="selectedTask.approved_asset.file_url">
                                    <img :src="selectedTask.approved_asset.file_url" class="w-12 h-12 object-cover rounded-md border border-orange-300 shadow-2xs shrink-0">
                                </template>
                                <div class="text-[11px] flex-1 min-w-0">
                                    <div class="font-bold text-slate-900 truncate" x-text="selectedTask.approved_asset.title"></div>
                                    <div class="text-[10px] text-slate-600" x-text="(selectedTask.approved_asset.dimensions || 'Standard') + ' • ' + (selectedTask.approved_asset.format || 'High-Res PDF')"></div>
                                    <div class="text-[9px] text-emerald-700 font-semibold">✓ Signed off by Sarah Jenkins (IMC Lead)</div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Trigger Native Camera or Pick Simulated -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block font-semibold text-slate-700">Photo Proof Capture:</label>
                            <button type="button" @click="triggerCamera()" 
                                    class="text-[11px] font-bold text-[#F7941D] hover:underline flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>Open Device Camera</span>
                            </button>
                        </div>

                        <!-- 3 Quick Presets for 1-Click Judging -->
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer border-2 rounded-lg p-1 text-center transition"
                                   :class="selectedPhotoPreset === 'gondola' ? 'border-[#F7941D] bg-orange-50/50' : 'border-slate-200'">
                                <input type="radio" name="preset_photo" value="gondola" x-model="selectedPhotoPreset" class="sr-only">
                                <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80" alt="Gondola" class="w-full h-12 rounded object-cover mb-0.5">
                                <span class="text-[9px] font-bold block text-slate-700">Gondola</span>
                            </label>

                            <label class="cursor-pointer border-2 rounded-lg p-1 text-center transition"
                                   :class="selectedPhotoPreset === 'archway' ? 'border-[#F7941D] bg-orange-50/50' : 'border-slate-200'">
                                <input type="radio" name="preset_photo" value="archway" x-model="selectedPhotoPreset" class="sr-only">
                                <img src="https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=400&q=80" alt="Archway" class="w-full h-12 rounded object-cover mb-0.5">
                                <span class="text-[9px] font-bold block text-slate-700">Archway</span>
                            </label>

                            <label class="cursor-pointer border-2 rounded-lg p-1 text-center transition"
                                   :class="selectedPhotoPreset === 'danglers' ? 'border-[#F7941D] bg-orange-50/50' : 'border-slate-200'">
                                <input type="radio" name="preset_photo" value="danglers" x-model="selectedPhotoPreset" class="sr-only">
                                <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=400&q=80" alt="Danglers" class="w-full h-12 rounded object-cover mb-0.5">
                                <span class="text-[9px] font-bold block text-slate-700">Danglers</span>
                            </label>
                        </div>
                    </div>

                    <input type="hidden" name="proof_image_url" x-model="customPhotoUrl">

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Store Field Notes</label>
                        <textarea name="proof_notes" x-model="proofNotes" rows="2" 
                                  class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-[#F7941D]"></textarea>
                    </div>

                    <!-- Simulated Offline Syncing Overlay State -->
                    <div x-show="syncing" class="p-3 rounded-lg bg-orange-50 border border-orange-200 space-y-2">
                        <div class="flex items-center space-x-2 text-xs font-bold text-[#F7941D]">
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Offline Synchronization Active...</span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-snug" x-text="syncMessage"></p>
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-orange-200/60 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#F7941D] h-1.5 rounded-full transition-all duration-500" :style="'width: ' + syncProgress + '%'"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-2">
                        <button type="button" @click="proofModal = false" :disabled="syncing" 
                                class="px-3.5 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 disabled:opacity-40">
                            Cancel
                        </button>
                        
                        <!-- Submit triggers 3-second offline sync simulation -->
                        <button type="button" @click="startOfflineSync()" :disabled="syncing"
                                class="px-4 py-2 text-xs font-bold rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white shadow-xs transition disabled:opacity-50">
                            <span x-text="syncing ? 'Syncing...' : (selectedTask.status === 'redo_requested' ? 'Submit Corrected Proof' : 'Submit Photo Proof')"></span>
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- Preview Proof Modal -->
    <div x-show="previewProofModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-2xl border border-slate-200 text-slate-800" @click.away="previewProofModal = false">
            <template x-if="selectedTask">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3">
                        <h3 class="text-xs font-bold text-slate-900 truncate" x-text="selectedTask.task_name"></h3>
                        <button type="button" @click="previewProofModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="w-full h-48 rounded-lg bg-slate-100 overflow-hidden mb-3">
                        <img :src="selectedTask.proof_image_url" :alt="selectedTask.task_name" class="w-full h-full object-cover">
                    </div>

                    <p class="text-xs text-slate-600 mb-3" x-text="selectedTask.proof_notes"></p>

                    <div class="flex items-center justify-end">
                        <button type="button" @click="previewProofModal = false" class="px-4 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            Close
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Master Artwork Inspection Modal -->
    <div x-show="previewMasterModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-sm w-full p-4 shadow-2xl border border-slate-200 text-slate-800" @click.away="previewMasterModal = false">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 mb-3">
                <div>
                    <h3 class="text-xs font-bold text-slate-900">IMC Master Creative Spec</h3>
                    <p class="text-[10px] text-slate-500">Official artwork signed off by IMC Lead</p>
                </div>
                <button type="button" @click="previewMasterModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <template x-if="selectedAsset">
                <div class="space-y-2.5 text-xs">
                    <div class="w-full h-48 rounded-lg bg-slate-100 overflow-hidden flex items-center justify-center border border-slate-200">
                        <img :src="selectedAsset.file_url" :alt="selectedAsset.title" class="max-h-full max-w-full object-contain">
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs" x-text="selectedAsset.title"></h4>
                        <div class="text-[11px] text-slate-500 mt-0.5" x-text="(selectedAsset.dimensions || 'Standard') + ' • ' + (selectedAsset.format || 'High-Res PDF')"></div>
                    </div>
                    <div class="p-2 bg-emerald-50 border border-emerald-200 rounded-lg text-[10px] text-emerald-800">
                        <span class="font-bold block">IMC Sign-Off Status:</span>
                        <span x-text="selectedAsset.approval_notes || 'Approved by Sarah Jenkins according to Naivas brand standards.'"></span>
                    </div>
                    <button type="button" @click="previewMasterModal = false" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg text-xs transition">
                        Close Preview
                    </button>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
