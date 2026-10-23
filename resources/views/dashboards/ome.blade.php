@extends('layouts.app', ['title' => 'OME Store Execution'])

@section('content')
<div class="space-y-6" x-data="{ 
    proofModal: false, 
    verifyModal: false,
    selectedTask: null,
    previewProofModal: false,
    selectedPhotoPreset: 'gondola',
    proofNotes: 'Physical POSM installed securely per branch merchandising guidelines.',
    customPhotoUrl: '',
    mobileFrameMode: false
}">

    <!-- Header -->
    <div class="bg-white rounded-xl p-6 border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-[#F7941D] uppercase tracking-wider block mb-1">Module 3: Store Operations</span>
            <h1 class="text-2xl font-bold text-slate-900">Store Execution & Mobile Proof</h1>
            <p class="text-xs text-slate-500 mt-1">
                Branch-level execution checklist. Submitting proof updates the task to <strong class="text-slate-800">"Executed - Pending Verification"</strong>.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <button type="button" @click="mobileFrameMode = !mobileFrameMode" 
                    class="px-3.5 py-2 rounded-lg border border-slate-200 hover:border-slate-300 bg-white text-slate-700 text-xs font-semibold shadow-xs transition">
                <span x-text="mobileFrameMode ? 'Standard Desktop View' : 'Mobile View Frame'"></span>
            </button>
        </div>
    </div>

    <!-- Branch Switcher -->
    <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="text-[11px] font-medium text-slate-400 uppercase tracking-wider block">Branch Location:</span>
            <div class="text-base font-bold text-slate-900">{{ $currentBranch }}</div>
        </div>

        <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 text-xs">
            @foreach($branches as $branch)
            <a href="{{ route('ome.dashboard', ['branch' => $branch]) }}" 
               class="px-3 py-1.5 rounded-lg font-medium transition {{ $currentBranch === $branch ? 'bg-[#F7941D] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                {{ $branch }}
            </a>
            @endforeach
        </div>
    </div>

    <!-- Optional Mobile View Container Wrapper -->
    <div :class="mobileFrameMode ? 'max-w-md mx-auto bg-slate-100 p-4 rounded-3xl border border-slate-300 shadow-lg' : ''">
        
        <div :class="mobileFrameMode ? 'bg-white rounded-2xl p-4 overflow-hidden border border-slate-200' : 'space-y-6'">

            <!-- Branch Progress Stats Card -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs mb-6">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Branch Rollout Health</h3>
                        <p class="text-xs text-slate-500">{{ $currentBranch }} POSM Tasks</p>
                    </div>
                    <span class="text-xs font-bold text-[#F7941D]">
                        {{ $completionRate }}% Done
                    </span>
                </div>

                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden mb-4">
                    <div class="bg-[#F7941D] h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="text-[10px] text-slate-500 block uppercase">Pending</span>
                        <span class="text-sm font-bold text-slate-900">{{ $pendingTasks }}</span>
                    </div>
                    <div class="p-2 rounded-lg bg-orange-50 border border-orange-100">
                        <span class="text-[10px] text-[#F7941D] font-bold block uppercase">In Review</span>
                        <span class="text-sm font-bold text-[#F7941D]">{{ $executedPendingVerification }}</span>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="text-[10px] text-slate-500 block uppercase">Verified</span>
                        <span class="text-sm font-bold text-slate-900">{{ $verifiedTasks }}</span>
                    </div>
                </div>
            </div>

            <!-- Task Checklist Section -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Store Execution Tasks ({{ $tasks->count() }})
                    </h3>
                </div>

                @forelse($tasks as $task)
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs transition hover:border-slate-300">
                    
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 text-[11px] text-slate-400 mb-1">
                                <span>{{ $task->zone }}</span>
                                <span>•</span>
                                <span class="font-mono">{{ $task->campaign->code }}</span>
                            </div>
                            <h4 class="font-bold text-slate-900 text-sm">
                                {{ $task->task_name }}
                            </h4>
                        </div>

                        <span class="px-2 py-0.5 rounded text-[11px] font-medium {{ $task->status === 'executed_pending_verification' ? 'bg-orange-50 text-[#F7941D] border border-orange-200' : ($task->status === 'verified' ? 'bg-slate-100 text-slate-800' : 'bg-slate-50 text-slate-600') }}">
                            {{ $task->getStatusDisplayName() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-500 py-2 border-y border-slate-100 my-2.5">
                        <span class="text-slate-800 font-medium">{{ $task->campaign->title }}</span>
                        <span>Due: {{ $task->due_date ? $task->due_date->format('M d, Y') : 'Immediate' }}</span>
                    </div>

                    @if($task->proof_image_url)
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 mb-3 flex items-center space-x-3 cursor-pointer"
                         @click="selectedTask = {{ json_encode($task) }}; previewProofModal = true">
                        <img src="{{ $task->proof_image_url }}" alt="Proof" class="w-12 h-12 rounded object-cover">
                        <div class="flex-1 text-xs">
                            <span class="font-semibold text-slate-800 block">Attached Proof Photo</span>
                            <p class="text-[11px] text-slate-500 line-clamp-1">{{ $task->proof_notes }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="pt-1 flex items-center justify-between">
                        @if($task->status === 'pending' || $task->status === 'in_progress')
                            <button type="button" 
                                    @click="selectedTask = {{ json_encode($task) }}; proofModal = true"
                                    class="w-full py-2 px-3 rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white text-xs font-semibold shadow-xs transition text-center">
                                Upload Proof of Execution
                            </button>
                        @elseif($task->status === 'executed_pending_verification')
                            <div class="flex items-center justify-between w-full">
                                <span class="text-xs text-[#F7941D] font-medium">Awaiting HQ Sign-Off</span>
                                <form action="{{ route('ome.tasks.verify', $task->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold">
                                        HQ Verify
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-xs text-slate-500 font-medium">
                                Verified & Live In-Store
                            </span>
                        @endif
                    </div>

                </div>
                @empty
                <div class="text-center py-12 bg-white rounded-xl border border-slate-200 p-6 text-xs text-slate-500">
                    No tasks currently scheduled for {{ $currentBranch }}.
                </div>
                @endforelse
            </div>

        </div>
    </div>

    <!-- Upload Proof Modal -->
    <div x-show="proofModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 text-slate-800" @click.away="proofModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-base font-bold text-slate-900">Upload Store Execution Proof</h3>
                <button type="button" @click="proofModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="selectedTask">
                <form :action="'/ome/tasks/' + selectedTask.id + '/proof'" method="POST" class="space-y-4 text-xs">
                    @csrf
                    
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                        <div class="font-bold text-slate-900 text-sm" x-text="selectedTask.task_name"></div>
                        <div class="text-slate-500 mt-0.5" x-text="selectedTask.branch_name + ' • ' + selectedTask.zone"></div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-2">Select In-Store Simulated Photo:</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="cursor-pointer border-2 rounded-lg p-1.5 text-center transition"
                                   :class="selectedPhotoPreset === 'gondola' ? 'border-[#F7941D] bg-orange-50/50' : 'border-slate-200 hover:border-slate-300'">
                                <input type="radio" name="preset_photo" value="gondola" x-model="selectedPhotoPreset" class="sr-only">
                                <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=400&q=80" alt="Gondola" class="w-full h-14 rounded object-cover mb-1">
                                <span class="text-[10px] font-medium block text-slate-800">Gondola</span>
                            </label>

                            <label class="cursor-pointer border-2 rounded-lg p-1.5 text-center transition"
                                   :class="selectedPhotoPreset === 'archway' ? 'border-[#F7941D] bg-orange-50/50' : 'border-slate-200 hover:border-slate-300'">
                                <input type="radio" name="preset_photo" value="archway" x-model="selectedPhotoPreset" class="sr-only">
                                <img src="https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=400&q=80" alt="Archway" class="w-full h-14 rounded object-cover mb-1">
                                <span class="text-[10px] font-medium block text-slate-800">Archway</span>
                            </label>

                            <label class="cursor-pointer border-2 rounded-lg p-1.5 text-center transition"
                                   :class="selectedPhotoPreset === 'danglers' ? 'border-[#F7941D] bg-orange-50/50' : 'border-slate-200 hover:border-slate-300'">
                                <input type="radio" name="preset_photo" value="danglers" x-model="selectedPhotoPreset" class="sr-only">
                                <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=400&q=80" alt="Danglers" class="w-full h-14 rounded object-cover mb-1">
                                <span class="text-[10px] font-medium block text-slate-800">Danglers</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Or Photo URL</label>
                        <input type="text" name="proof_image_url" x-model="customPhotoUrl" placeholder="https://..." 
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Store Field Notes</label>
                        <textarea name="proof_notes" x-model="proofNotes" rows="2" 
                                  class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 focus:border-[#F7941D] focus:ring-1 focus:ring-[#F7941D] focus:outline-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button type="button" @click="proofModal = false" class="px-4 py-2 text-xs font-semibold rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold rounded-lg bg-[#F7941D] hover:bg-[#e07f0f] text-white shadow-xs">
                            Submit Proof
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    <!-- Preview Proof Modal -->
    <div x-show="previewProofModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 text-slate-800" @click.away="previewProofModal = false">
            <template x-if="selectedTask">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <h3 class="text-sm font-bold text-slate-900" x-text="selectedTask.task_name"></h3>
                        <button type="button" @click="previewProofModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="w-full h-64 rounded-lg bg-slate-100 overflow-hidden mb-3 flex items-center justify-center">
                        <img :src="selectedTask.proof_image_url" :alt="selectedTask.task_name" class="max-h-full max-w-full object-cover">
                    </div>

                    <p class="text-xs text-slate-600 mb-4" x-text="selectedTask.proof_notes"></p>

                    <div class="flex items-center justify-end">
                        <button type="button" @click="previewProofModal = false" class="px-4 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            Close
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
@endsection
