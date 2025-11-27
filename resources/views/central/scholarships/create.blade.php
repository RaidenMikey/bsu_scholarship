@php
    // Precompute conditions and documents BEFORE head scripts
    $conditionsData = old('conditions', isset($scholarship)
        ? $scholarship->conditions->map(function($c){
            return ['type' => $c->name, 'value' => $c->value];
        })->toArray()
        : []
    );

    $documentsData = old('documents', isset($scholarship)
        ? $scholarship->requiredDocuments->map(function($r){
            return ['name' => $r->document_name, 'type' => $r->document_type, 'mandatory' => $r->is_mandatory];
        })->toArray()
        : []
    );

@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($scholarship) ? 'Edit Scholarship' : 'Create Scholarship' }} - BSU</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Data for Alpine.js (JSON blocks to avoid JS lint issues) -->
    <script id="conditions-data" type="application/json">
{!! json_encode($conditionsData, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) !!}
    </script>
    <script id="documents-data" type="application/json">
{!! json_encode($documentsData, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) !!}
    </script>
    
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    {{ isset($scholarship) ? 'Edit Scheme' : 'Add New Scheme' }}
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    {{ isset($scholarship) ? 'Update the details of this scholarship scheme.' : 'Create a new scholarship scheme for students.' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('central.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red transition-colors">
                    Cancel
                </a>
            </div>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data"
          action="{{ isset($scholarship) 
                    ? route('central.scholarships.update', $scholarship->id) 
                    : route('central.scholarships.store') }}"
          class="space-y-8">
        @csrf
        @if(isset($scholarship))
            @method('PUT')
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 mb-6 animate-pulse">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Main Info -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Scheme Details Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                        <div class="p-2 bg-red-100 rounded-lg text-bsu-red">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Scheme Details</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="scholarship_name" class="block text-sm font-semibold text-gray-700 mb-1">Scheme Name</label>
                            <input type="text" id="scholarship_name" name="scholarship_name"
                                   value="{{ old('scholarship_name', $scholarship->scholarship_name ?? '') }}" required
                                   placeholder="e.g. Academic Excellence Scholarship"
                                   class="w-full rounded-lg border-gray-300 focus:border-bsu-red focus:ring focus:ring-bsu-red/20 transition shadow-sm">
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" required rows="4"
                                      placeholder="Describe the scholarship scheme, its purpose, and target beneficiaries..."
                                      class="w-full rounded-lg border-gray-300 focus:border-bsu-red focus:ring focus:ring-bsu-red/20 transition shadow-sm">{{ old('description', $scholarship->description ?? '') }}</textarea>
                        </div>

                        <!-- Type Selection -->
                        <div x-data="{ type: '{{ old('scholarship_type', $scholarship->scholarship_type ?? 'private') }}' }">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Scheme Type</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex cursor-pointer rounded-xl border p-4 shadow-sm focus:outline-none hover:bg-gray-50 transition-colors"
                                       :class="type === 'private' ? 'border-bsu-red ring-1 ring-bsu-red bg-red-50/30' : 'border-gray-200'">
                                    <input type="radio" name="scholarship_type" value="private" class="sr-only" x-model="type">
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Private</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Funded by private organizations or individuals.</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 text-bsu-red" :class="type === 'private' ? '' : 'invisible'" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </label>

                                <label class="relative flex cursor-pointer rounded-xl border p-4 shadow-sm focus:outline-none hover:bg-gray-50 transition-colors"
                                       :class="type === 'government' ? 'border-bsu-red ring-1 ring-bsu-red bg-red-50/30' : 'border-gray-200'">
                                    <input type="radio" name="scholarship_type" value="government" class="sr-only" x-model="type">
                                    <span class="flex flex-1">
                                        <span class="flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Government</span>
                                            <span class="mt-1 flex items-center text-sm text-gray-500">Funded by government agencies.</span>
                                        </span>
                                    </span>
                                    <svg class="h-5 w-5 text-bsu-red" :class="type === 'government' ? '' : 'invisible'" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conditions & Requirements -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Requirements & Conditions</h2>
                    </div>
                    
                    <div class="p-6 space-y-8">
                        <!-- Conditions Alpine Component -->
                        <div x-data="{ 
                            conditions: (function(){
                                try {
                                    const el = document.getElementById('conditions-data');
                                    return JSON.parse((el && el.textContent) || '[]');
                                } catch(e) { return []; }
                            })()
                        }">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Eligibility Conditions</h3>
                                <button type="button" @click="conditions.push({type:'gwa',value:''})"
                                        class="text-sm text-bsu-red font-semibold hover:text-bsu-redDark flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Condition
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div x-show="conditions.length === 0" class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                    <p class="text-sm text-gray-500">No specific eligibility conditions set.</p>
                                </div>

                                <template x-for="(cond, index) in conditions" :key="index">
                                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200 group">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1">
                                            <select x-model="cond.type" :name="'conditions['+index+'][type]'" 
                                                    class="rounded-lg border-gray-300 text-sm focus:ring-bsu-red focus:border-bsu-red">
                                                <option value="gwa">Minimum GWA</option>
                                                <option value="year_level">Year Level</option>
                                                <option value="income">Maximum Income</option>
                                                <option value="disability">Disability</option>
                                                <option value="program">Program</option>
                                                <option value="sex">Gender</option>
                                            </select>

                                            <!-- Dynamic Inputs based on Type -->
                                            <template x-if="cond.type === 'gwa'">
                                                <input type="number" step="0.01" x-model="cond.value" :name="'conditions['+index+'][value]'" placeholder="e.g. 2.50" class="rounded-lg border-gray-300 text-sm focus:ring-bsu-red focus:border-bsu-red">
                                            </template>
                                            <template x-if="cond.type === 'year_level'">
                                                <select x-model="cond.value" :name="'conditions['+index+'][value]'" class="rounded-lg border-gray-300 text-sm focus:ring-bsu-red focus:border-bsu-red">
                                                    <option value="1st Year">1st Year</option>
                                                    <option value="2nd Year">2nd Year</option>
                                                    <option value="3rd Year">3rd Year</option>
                                                    <option value="4th Year">4th Year</option>
                                                </select>
                                            </template>
                                            <template x-if="cond.type === 'income'">
                                                <input type="number" x-model="cond.value" :name="'conditions['+index+'][value]'" placeholder="Amount (₱)" class="rounded-lg border-gray-300 text-sm focus:ring-bsu-red focus:border-bsu-red">
                                            </template>
                                            <template x-if="cond.type === 'disability'">
                                                <select x-model="cond.value" :name="'conditions['+index+'][value]'" class="rounded-lg border-gray-300 text-sm focus:ring-bsu-red focus:border-bsu-red">
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                            </template>
                                            <template x-if="cond.type === 'program'">
                                                <select x-model="cond.value" :name="'conditions['+index+'][value]'" class="rounded-lg border-gray-300 text-sm focus:ring-bsu-red focus:border-bsu-red">
                                                    <option value="">Select Program</option>
                                                    <option value="BS Computer Science">BS Computer Science</option>
                                                    <option value="BS Information Technology">BS Information Technology</option>
                                                    <!-- Add other programs as needed -->
                                                </select>
                                            </template>
                                            <template x-if="cond.type === 'sex'">
                                                <select x-model="cond.value" :name="'conditions['+index+'][value]'" class="rounded-lg border-gray-300 text-sm focus:ring-bsu-red focus:border-bsu-red">
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </template>
                                        </div>
                                        <button type="button" @click="conditions.splice(index,1)" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        <!-- Documents Alpine Component -->
                        <div x-data="{ 
                            documents: (function(){
                                try {
                                    const el = document.getElementById('documents-data');
                                    return JSON.parse((el && el.textContent) || '[]');
                                } catch(e) { return []; }
                            })()
                        }">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Required Documents</h3>
                                <button type="button" @click="documents.push({name:'', type:'pdf', mandatory:1})"
                                        class="text-sm text-bsu-red font-semibold hover:text-bsu-redDark flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Add Document
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div x-show="documents.length === 0" class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                    <p class="text-sm text-gray-500">No documents required yet.</p>
                                </div>

                                <template x-for="(doc, index) in documents" :key="index">
                                    <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200 group">
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 flex-1">
                                            <input type="text" x-model="doc.name" :name="'documents['+index+'][name]'" placeholder="Document Name" class="rounded-lg border-gray-300 text-sm focus:ring-bsu-red focus:border-bsu-red">
                                            <select x-model="doc.type" :name="'documents['+index+'][type]'" class="rounded-lg border-gray-300 text-sm focus:ring-bsu-red focus:border-bsu-red">
                                                <option value="pdf">PDF Only</option>
                                                <option value="image">Image Only</option>
                                                <option value="both">PDF or Image</option>
                                            </select>
                                            <select x-model="doc.mandatory" :name="'documents['+index+'][mandatory]'" class="rounded-lg border-gray-300 text-sm focus:ring-bsu-red focus:border-bsu-red">
                                                <option value="1">Mandatory</option>
                                                <option value="0">Optional</option>
                                            </select>
                                        </div>
                                        <button type="button" @click="documents.splice(index,1)" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Eligibility Notes -->
                        <div>
                            <label for="eligibility_notes" class="block text-sm font-semibold text-gray-700 mb-1">Additional Eligibility Notes</label>
                            <textarea id="eligibility_notes" name="eligibility_notes" rows="3"
                                      placeholder="Any other specific requirements or notes..."
                                      class="w-full rounded-lg border-gray-300 focus:border-bsu-red focus:ring focus:ring-bsu-red/20 transition shadow-sm">{{ old('eligibility_notes', $scholarship->eligibility_notes ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Configuration -->
            <div class="space-y-8">
                
                <!-- Configuration Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                        <div class="p-2 bg-yellow-100 rounded-lg text-yellow-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Configuration</h2>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Grant Amount -->
                        <div>
                            <label for="grant_amount" class="block text-sm font-semibold text-gray-700 mb-1">Grant Amount (₱)</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" step="0.01" id="grant_amount" name="grant_amount" min="0"
                                       value="{{ old('grant_amount', $scholarship->grant_amount ?? '') }}"
                                       class="block w-full rounded-lg border-gray-300 pl-7 focus:border-bsu-red focus:ring-bsu-red sm:text-sm">
                            </div>
                        </div>

                        <!-- Slots -->
                        <div>
                            <label for="slots_available" class="block text-sm font-semibold text-gray-700 mb-1">Available Slots</label>
                            <input type="number" id="slots_available" name="slots_available" min="0"
                                   value="{{ old('slots_available', $scholarship->slots_available ?? '') }}"
                                   placeholder="Leave empty for unlimited"
                                   class="w-full rounded-lg border-gray-300 focus:border-bsu-red focus:ring focus:ring-bsu-red/20 transition shadow-sm">
                        </div>

                        <!-- Grant Frequency -->
                        <div>
                            <label for="grant_type" class="block text-sm font-semibold text-gray-700 mb-1">Grant Frequency</label>
                            <select id="grant_type" name="grant_type" required
                                    class="w-full rounded-lg border-gray-300 focus:border-bsu-red focus:ring focus:ring-bsu-red/20 transition shadow-sm">
                                <option value="recurring" {{ old('grant_type', $scholarship->grant_type ?? '') == 'recurring' ? 'selected' : '' }}>Recurring (Renewable)</option>
                                <option value="one_time" {{ old('grant_type', $scholarship->grant_type ?? '') == 'one_time' ? 'selected' : '' }}>One-time Grant</option>
                                <option value="discontinued" {{ old('grant_type', $scholarship->grant_type ?? '') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                            </select>
                        </div>

                        <!-- Allow Existing Scholarship -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="allow_existing_scholarship" name="allow_existing_scholarship" type="checkbox" value="1"
                                       {{ old('allow_existing_scholarship', $scholarship->allow_existing_scholarship ?? false) ? 'checked' : '' }}
                                       class="focus:ring-bsu-red h-4 w-4 text-bsu-red border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="allow_existing_scholarship" class="font-medium text-gray-700">Stackable Scholarship</label>
                                <p class="text-gray-500">Allow students with existing scholarships to apply for this scheme.</p>
                            </div>
                        </div>
                        </div>
                        </div>

                        <hr class="border-gray-100">

                        <!-- Dates -->
                        <div>
                            <label for="application_start_date" class="block text-sm font-semibold text-gray-700 mb-1">Opening Date</label>
                            <input type="date" id="application_start_date" name="application_start_date"
                                   value="{{ old('application_start_date', isset($scholarship->application_start_date) ? \Carbon\Carbon::parse($scholarship->application_start_date)->format('Y-m-d') : '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-bsu-red focus:ring focus:ring-bsu-red/20 transition shadow-sm">
                            <p class="mt-1 text-xs text-gray-500">Leave empty to open immediately.</p>
                        </div>

                        <div>
                            <label for="submission_deadline" class="block text-sm font-semibold text-gray-700 mb-1">Submission Deadline</label>
                            <input type="date" id="submission_deadline" name="submission_deadline" required
                                   value="{{ old('submission_deadline', isset($scholarship->submission_deadline) ? \Carbon\Carbon::parse($scholarship->submission_deadline)->format('Y-m-d') : '') }}"
                                   class="w-full rounded-lg border-gray-300 focus:border-bsu-red focus:ring focus:ring-bsu-red/20 transition shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- Visuals Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                        <div class="p-2 bg-purple-100 rounded-lg text-purple-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Visuals</h2>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Background Image</label>
                        
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:bg-gray-50 transition-colors cursor-pointer" onclick="document.getElementById('background_image').click()">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="background_image" class="relative cursor-pointer bg-white rounded-md font-medium text-bsu-red hover:text-bsu-redDark focus-within:outline-none">
                                        <span>Upload a file</span>
                                        <input id="background_image" name="background_image" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>

                        @if(isset($scholarship) && $scholarship->background_image)
                            <div class="mt-4 relative rounded-lg overflow-hidden h-32 border border-gray-200">
                                <img src="{{ $scholarship->getBackgroundImageUrl() }}" alt="Current background" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center text-white text-sm font-medium">
                                    Current Image
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-bsu-red hover:bg-bsu-redDark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-bsu-red transition-all transform hover:-translate-y-0.5">
                    {{ isset($scholarship) ? 'Update Scheme' : 'Create Scheme' }}
                </button>

            </div>
        </div>
    </form>
</div>

</body>
</html>
