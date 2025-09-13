<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ isset($scholarship) ? 'Edit Scholarship' : 'Create Scholarship' }} - BSU</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Batangas_State_Logo.png') }}">
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">

@php
    // ✅ Precompute conditions
    $conditionsData = old('conditions', isset($scholarship)
        ? $scholarship->conditions->map(function($c){
            return ['type' => $c->type, 'value' => $c->value];
        })->toArray()
        : []
    );

    // ✅ Precompute documents
    $documentsData = old('documents', isset($scholarship)
        ? $scholarship->requirements->map(function($r){
            return ['name' => $r->name, 'type' => $r->type, 'mandatory' => $r->mandatory];
        })->toArray()
        : []
    );
@endphp

<div class="max-w-4xl mx-auto px-6 py-10 bg-white rounded-xl shadow-md border-2 border-red-700 mt-10">

    <h2 class="text-2xl font-bold text-red-700 text-center mb-6">
        {{ isset($scholarship) ? 'Edit Scholarship' : 'Add New Scholarship' }}
    </h2>

    <form method="POST"
          action="{{ isset($scholarship) 
                    ? route('central.scholarships.update', $scholarship->id) 
                    : route('central.scholarships.store') }}">
        @csrf
        @if(isset($scholarship))
            @method('PUT')
        @endif

        <!-- Scholarship Name -->
        <label for="scholarship_name" class="block text-sm font-medium text-gray-700">Scholarship Name</label>
        <input type="text" id="scholarship_name" name="scholarship_name"
               value="{{ old('scholarship_name', $scholarship->scholarship_name ?? '') }}" required
               class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700 focus:border-red-700">

        <!-- Description -->
        <label for="description" class="block text-sm font-medium text-gray-700 mt-2">Description</label>
        <textarea id="description" name="description" required
                  class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700 focus:border-red-700">{{ old('description', $scholarship->description ?? '') }}</textarea>

        <!-- Conditions -->
        <div x-data="{ conditions: @json($conditionsData) }" class="mt-6">
            <h3 class="text-md font-semibold text-gray-800 mb-2">Condition Requirements</h3>

            <template x-for="(cond, index) in conditions" :key="index">
                <div class="flex space-x-2 mt-2">
                    <select x-model="cond.type" :name="'conditions['+index+'][type]'" 
                            class="w-1/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                        <option value="gwa">Minimum GWA</option>
                        <option value="year_level">Year Level</option>
                        <option value="income">Maximum Income</option>
                        <option value="disability">Disability</option>
                    </select>

                    <template x-if="cond.type === 'gwa'">
                        <input type="number" step="0.01" x-model="cond.value" 
                               :name="'conditions['+index+'][value]'" 
                               placeholder="e.g. 2.50"
                               class="w-2/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                    </template>

                    <template x-if="cond.type === 'year_level'">
                        <select x-model="cond.value" :name="'conditions['+index+'][value]'" 
                                class="w-2/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </template>

                    <template x-if="cond.type === 'income'">
                        <input type="number" x-model="cond.value" 
                               :name="'conditions['+index+'][value]'" 
                               placeholder="₱ e.g. 15000"
                               class="w-2/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                    </template>

                    <template x-if="cond.type === 'disability'">
                        <select x-model="cond.value" :name="'conditions['+index+'][value]'" 
                                class="w-2/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </template>

                    <button type="button" @click="conditions.splice(index,1)" class="text-red-600 font-bold">✖</button>
                </div>
            </template>

            <button type="button" @click="conditions.push({type:'gwa',value:''})"
                    class="mt-2 px-3 py-1 bg-red-700 text-white rounded-lg hover:bg-red-800">
                + Add Condition
            </button>
        </div>

        <!-- Requirements -->
        <div x-data="{ documents: @json($documentsData) }" class="mt-6">
            <h3 class="text-md font-semibold text-gray-800 mb-2">Document Requirements</h3>

            <template x-for="(doc, index) in documents" :key="index">
                <div class="flex space-x-2 mt-2">
                    <input type="text" x-model="doc.name" :name="'documents['+index+'][name]'" 
                           placeholder="Document Name"
                           class="w-1/3 border rounded-lg p-2 focus:ring focus:ring-red-700">

                    <select x-model="doc.type" :name="'documents['+index+'][type]'" 
                            class="w-1/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                        <option value="pdf">PDF</option>
                        <option value="image">Image</option>
                        <option value="both">PDF or Image</option>
                    </select>

                    <select x-model="doc.mandatory" :name="'documents['+index+'][mandatory]'" 
                            class="w-1/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                        <option value="1">Required</option>
                        <option value="0">Optional</option>
                    </select>

                    <button type="button" @click="documents.splice(index,1)" class="text-red-600 font-bold">✖</button>
                </div>
            </template>

            <button type="button" @click="documents.push({name:'', type:'pdf', mandatory:1})"
                    class="mt-2 px-3 py-1 bg-red-700 text-white rounded-lg hover:bg-red-800">
                + Add Document
            </button>
        </div>

        <!-- Deadline -->
        <label for="deadline" class="block text-sm font-medium text-gray-700 mt-4">Deadline</label>
        <input type="date" id="deadline" name="deadline"
               value="{{ old('deadline', isset($scholarship->deadline) ? \Carbon\Carbon::parse($scholarship->deadline)->format('Y-m-d') : '') }}" required
               class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700">

        <!-- Slots Available -->
        <label for="slots_available" class="block text-sm font-medium text-gray-700 mt-2">Slots Available</label>
        <input type="number" id="slots_available" name="slots_available"
               value="{{ old('slots_available', $scholarship->slots_available ?? '') }}"
               class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700">

        <!-- Grant Amount -->
        <label for="grant_amount" class="block text-sm font-medium text-gray-700 mt-2">Grant Amount (₱)</label>
        <input type="number" step="0.01" id="grant_amount" name="grant_amount"
               value="{{ old('grant_amount', $scholarship->grant_amount ?? '') }}"
               class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700">

        <!-- Renewal Allowed -->
        <div class="flex items-center mt-2">
            <input type="checkbox" id="renewal_allowed" name="renewal_allowed" value="1"
                   {{ old('renewal_allowed', $scholarship->renewal_allowed ?? false) ? 'checked' : '' }}
                   class="h-4 w-4 text-red-700 focus:ring-red-700 border-gray-300 rounded">
            <label for="renewal_allowed" class="ml-2 block text-sm text-gray-700">Allow Renewal</label>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-between">
            <a href="{{ route('central.dashboard') }}"
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-semibold rounded-lg transition">
                {{ isset($scholarship) ? '💾 Update' : '📤 Submit' }}
            </button>
        </div>
    </form>
</div>

</body>
</html>
