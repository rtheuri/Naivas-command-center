@extends('layouts.app', ['title' => 'OME Store Execution'])

@section('content')
<div class="space-y-6" x-data="{ 
    proofModal: false, 
    verifyModal: false,
    selectedTask: null,
    previewProofModal: false,
    selectedPhotoPreset: 'gondola',
    proofNotes: 'Physical POSM installed securely per branch visual merchandising guidelines.',
    customPhotoUrl: '',
    mobileFrameMode: false
}">

    <!-- Page Header & Persona Greeting -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Module 3: Operations Marketing Execution (OME)
                </span>
                <span class="text-xs text-slate-500">Store Floor Rollout & Photographic Proof</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Store Execution & Mobile Proof</h1>
            <p class="text-sm text-slate-600 mt-0.5">
                On-the-ground mobile execution checklist for Naivas supermarket branches. 
                <strong class="text-emerald-700">Submitting proof updates task status to "Executed - Pending Verification" on the Executive Hub.</strong>
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <!-- Mobile Preview Mode Toggle for Judges -->
            <button type="button" @click="mobileFrameMode = !mobileFrameMode" 
                    class="inline-flex items-center px-3.5 py-2 rounded-xl border border-gray-300 bg-white hover:bg-gray-50 text-slate-700 text-xs font-bold shadow-xs transition">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span x-text="mobileFrameMode ? 'Standard Desktop View' : 'Smartphone Preview Frame'"></span>
            </button>
        </div>
    </div>

    <!-- Live Testing Guide Alert For Judges -->
    <div class="rounded-xl bg-gradient-to-r from-emerald-50 via-white to-purple-50 border border-emerald-200 p-4 shadow-xs flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <span class="text-xl">💡</span>
            <div class="text-xs text-slate-700 leading-relaxed">
                <strong class="text-emerald-900 font-bold">Judges' Mobile Proof Test Point:</strong> Select a pending store rollout task below (e.g. <em>"Install Gondola End Banner in Fresh Produce Aisle"</em>). Click <strong class="text-emerald-700">"Upload Proof of Execution"</strong>. Choose a simulated supermarket photo preset and submit. The status immediately updates to <strong class="text-purple-700">"Executed - Pending Verification"</strong> and triggers a live audit entry on the Global Executive Dashboard!
            </div>
        </div>
    </div>

    <!-- Branch Switcher Bar -->
    <div class="bg-white rounded-2xl p-4 shadow-xs border border-gray-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#006837] flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Selected Supermarket Branch:</span>
                <div class="text-lg font-black text-slate-900">{{ $currentBranch }}</div>
            </div>
        </div>

        <!-- Branch Select Buttons -->
        <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 text-xs">
            @foreach($branches as $branch)
            <a href="{{ route('ome.dashboard', ['branch' => $branch]) }}" 
               class="px-3 py-1.5 rounded-lg font-bold whitespace-nowrap transition {{ $currentBranch === $branch ? 'bg-[#006837] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                {{ $branch }}
            </a>
            @endforeach
        </div>
    </div>

    <!-- Container wrapper with conditional Mobile Preview Frame styling -->
    <div :class="mobileFrameMode ? 'max-w-md mx-auto bg-slate-900 p-4 rounded-[40px] shadow-2xl ring-8 ring-slate-800' : ''">
        
        <div :class="mobileFrameMode ? 'bg-slate-50 rounded-[32px] p-4 overflow-hidden border border-slate-700 min-h-[680px]' : 'space-y-6'">

            <!-- Mobile Frame Top Status Header (Only visible in frame mode) -->
            <template x-if="mobileFrameMode">
                <div class="mb-4 pb-3 border-b border-gray-200 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-1 font-bold text-slate-900">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1"></span>
                        <span>Naivas Store App</span>
                    </div>
                    <span class="text-slate-500 font-mono text-[10px]">9:41 AM • 5G</span>
                </div>
            </template>

            <!-- Branch Progress Stats Card -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-gray-200/80 mb-6">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Branch Rollout Health</h3>
                        <p class="text-xs text-slate-500">{{ $currentBranch }} Active POSM Tasks</p>
                    </div>
                    <span class="text-xs font-black text-emerald-700 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200">
                        {{ $completionRate }}% Done
                    </span>
                </div>

                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden mb-4">
                    <div class="bg-gradient-to-r from-[#006837] to-[#F7941D] h-2.5 rounded-full transition-all duration-500" 
                         style="width: {{ $completionRate }}%"></div>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="p-2 rounded-xl bg-amber-50 border border-amber-100">
                        <span class="text-[10px] text-amber-800 font-bold block uppercase">Pending</span>
                        <span class="text-base font-black text-amber-900">{{ $pendingTasks }}</span>
                    </div>
                    <div class="p-2 rounded-xl bg-purple-50 border border-purple-100">
                        <span class="text-[10px] text-purple-800 font-bold block uppercase">In Review</span>
                        <span class="text-base font-black text-purple-900">{{ $executedPendingVerification }}</span>
                    </div>
                    <div class="p-2 rounded-xl bg-emerald-50 border border-emerald-100">
                        <span class="text-[10px] text-emerald-800 font-bold block uppercase">Verified</span>
                        <span class="text-base font-black text-emerald-900">{{ $verifiedTasks }}</span>
                    </div>
                </div>
            </div>

            <!-- Task Checklist Section -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                        Store Execution Checklist ({{ $tasks->count() }})
                    </h3>
                    <span class="text-xs text-slate-400">Pushed from Marketing HQ</span>
                </div>

                @forelse($tasks as $task)
                <div class="bg-white rounded-2xl p-5 shadow-xs border transition-all duration-200 hover:shadow-md {{ $task->status === 'executed_pending_verification' ? 'border-purple-300 ring-1 ring-purple-200' : ($task->status === 'verified' ? 'border-emerald-300' : 'border-gray-200') }}">
                    
                    <!-- Task Header -->
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $task->zone }}
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    {{ $task->campaign->code }}
                                </span>
                            </div>
                            <h4 class="font-extrabold text-slate-900 text-sm leading-snug">
                                {{ $task->task_name }}
                            </h4>
                        </div>

                        <!-- Status Pill -->
                        <div class="shrink-0">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $task->getStatusBadgeClasses() }}">
                                {{ $task->getStatusDisplayName() }}
                            </span>
                        </div>
                    </div>

                    <!-- Campaign & Due Date Info -->
                    <div class="flex flex-wrap items-center justify-between text-xs text-slate-500 py-2 border-y border-gray-100 my-3 gap-2">
                        <div class="flex items-center space-x-1">
                            <span class="font-semibold text-emerald-800">{{ $task->campaign->title }}</span>
                            <span>•</span>
                            <span class="text-slate-600">{{ $task->campaign->category }}</span>
                        </div>
                        <div class="flex items-center text-slate-600">
                            <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Due: <strong>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'Immediate' }}</strong></span>
                        </div>
                    </div>

                    <!-- Attached Proof Image (If uploaded) -->
                    @if($task->proof_image_url)
                    <div class="p-3 rounded-xl bg-slate-50 border border-gray-200 mb-3 flex items-center space-x-3 cursor-pointer group"
                         @click="selectedTask = {{ json_encode($task) }}; previewProofModal = true">
                        <img src="{{ $task->proof_image_url }}" alt="Proof" class="w-16 h-16 rounded-lg object-cover ring-1 ring-gray-300 group-hover:scale-105 transition">
                        <div class="flex-1 text-xs">
                            <div class="font-bold text-slate-900 flex items-center space-x-1">
                                <span>Attached Photographic Proof</span>
                                <span class="text-purple-600">🔍</span>
                            </div>
                            <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $task->proof_notes }}</p>
                            <span class="text-[10px] text-slate-400">Submitted {{ $task->executed_at ? $task->executed_at->diffForHumans() : 'recently' }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Task Action Button -->
                    <div class="flex items-center justify-between pt-1">
                        @if($task->status === 'pending' || $task->status === 'in_progress')
                            <button type="button" 
                                    @click="selectedTask = {{ json_encode($task) }}; proofModal = true"
                                    class="w-full py-2.5 px-4 rounded-xl bg-[#006837] hover:bg-[#005a30] text-white text-xs font-bold shadow-md shadow-emerald-950/20 transition flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4 text-[#F7941D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Upload Proof of Execution</span>
                            </button>
                        @elseif($task->status === 'executed_pending_verification')
                            <div class="flex items-center justify-between w-full">
                                <span class="text-xs text-purple-700 font-bold flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-purple-500 animate-ping mr-1.5"></span>
                                    Sync'd to Executive Hub
                                </span>
                                <form action="{{ route('ome.tasks.verify', $task->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs">
                                        HQ Verify Now
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex items-center space-x-1.5 text-xs text-emerald-700 font-bold">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Installation Verified & Audited Live</span>
                            </div>
                        @endif
                    </div>

                </div>
                @empty
                <div class="text-center py-12 bg-white rounded-2xl border border-gray-200 p-6 text-xs text-slate-500">
                    No tasks currently scheduled for {{ $currentBranch }}.
                </div>
                @endforelse
            </div>

        </div>
    </div>

    <!-- Upload Proof of Execution Modal -->
    <div x-show="proofModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl ring-1 ring-black/10 text-slate-800" @click.away="proofModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-[#006837] flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900">Upload Proof of Store Execution</h3>
                        <p class="text-xs text-slate-500">Mobile Photographic Verification</p>
                    </div>
                </div>
                <button type="button" @click="proofModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="selectedTask">
                <form :action="'/ome/tasks/' + selectedTask.id + '/proof'" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="p-3 rounded-xl bg-slate-50 border border-gray-200">
                        <div class="font-bold text-slate-900 text-sm" x-text="selectedTask.task_name"></div>
                        <div class="text-xs text-slate-500 mt-0.5">
                            Branch: <strong class="text-slate-800" x-text="selectedTask.branch_name"></strong> | 
                            Zone: <span class="text-slate-700" x-text="selectedTask.zone"></span>
                        </div>
                    </div>

                    <!-- Preset Real-world Supermarket Photo Selector for Quick Evaluation -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">
                            Select Simulated In-Store Photo (1-Tap):
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer border-2 rounded-xl p-2 text-center transition"
                                   :class="selectedPhotoPreset === 'gondola' ? 'border-[#006837] bg-emerald-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="preset_photo" value="gondola" x-model="selectedPhotoPreset" class="sr-only">
                                <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80" alt="Gondola" class="w-full h-14 rounded-lg object-cover mb-1">
                                <span class="text-[10px] font-bold block text-slate-800">Gondola Header</span>
                            </label>

                            <label class="cursor-pointer border-2 rounded-xl p-2 text-center transition"
                                   :class="selectedPhotoPreset === 'archway' ? 'border-[#006837] bg-emerald-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="preset_photo" value="archway" x-model="selectedPhotoPreset" class="sr-only">
                                <img src="https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=400&q=80" alt="Archway" class="w-full h-14 rounded-lg object-cover mb-1">
                                <span class="text-[10px] font-bold block text-slate-800">Entrance Arch</span>
                            </label>

                            <label class="cursor-pointer border-2 rounded-xl p-2 text-center transition"
                                   :class="selectedPhotoPreset === 'danglers' ? 'border-[#006837] bg-emerald-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="preset_photo" value="danglers" x-model="selectedPhotoPreset" class="sr-only">
                                <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=400&q=80" alt="Danglers" class="w-full h-14 rounded-lg object-cover mb-1">
                                <span class="text-[10px] font-bold block text-slate-800">Price Danglers</span>
                            </label>
                        </div>
                    </div>

                    <!-- Custom Photo URL (Optional) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Or Paste Custom Image URL</label>
                        <input type="text" name="proof_image_url" x-model="customPhotoUrl" placeholder="https://..." 
                               class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none">
                    </div>

                    <!-- Store Manager Notes -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Branch Field Notes</label>
                        <textarea name="proof_notes" x-model="proofNotes" rows="2" 
                                  class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#006837] focus:outline-none"></textarea>
                    </div>

                    <div class="p-3 rounded-xl bg-purple-50 border border-purple-200 text-xs text-purple-900">
                        <strong>Instant Synchronization:</strong> Submitting will instantly mark this task as 
                        <strong class="text-purple-800">"Executed - Pending Verification"</strong> and post a notification to the CMO's live activity feed.
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button type="button" @click="proofModal = false" class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-slate-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-bold rounded-lg bg-[#006837] text-white hover:bg-[#005a30] shadow flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-[#F7941D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Submit Proof of Execution</span>
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- Preview Proof Modal -->
    <div x-show="previewProofModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl ring-1 ring-black/10 text-slate-800" @click.away="previewProofModal = false">
            <template x-if="selectedTask">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900" x-text="selectedTask.task_name"></h3>
                            <p class="text-xs text-slate-500" x-text="selectedTask.branch_name + ' • ' + selectedTask.zone"></p>
                        </div>
                        <button type="button" @click="previewProofModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="w-full h-72 rounded-xl bg-slate-100 overflow-hidden mb-4 relative flex items-center justify-center">
                        <img :src="selectedTask.proof_image_url" :alt="selectedTask.task_name" class="max-h-full max-w-full object-cover">
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-gray-200 text-xs text-slate-700 mb-4">
                        <strong class="block text-slate-900 mb-1">Field Execution Notes:</strong>
                        <p x-text="selectedTask.proof_notes"></p>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="button" @click="previewProofModal = false" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-slate-800 font-semibold text-xs">
                            Close
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
