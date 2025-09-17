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
                        <option value="program">Program</option>
                        <option value="campus">Campus</option>
                        <option value="age">Minimum Age</option>
                        <option value="sex">Gender</option>
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

                    <template x-if="cond.type === 'program'">
                        <select x-model="cond.value" :name="'conditions['+index+'][value]'" 
                                class="w-2/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                            <option value="">-- Select Program --</option>
                            <option value="BS Computer Science">BS Computer Science</option>
                            <option value="BS Information Technology">BS Information Technology</option>
                            <option value="BS Computer Engineering">BS Computer Engineering</option>
                            <option value="BS Electronics Engineering">BS Electronics Engineering</option>
                            <option value="BS Civil Engineering">BS Civil Engineering</option>
                            <option value="BS Mechanical Engineering">BS Mechanical Engineering</option>
                            <option value="BS Electrical Engineering">BS Electrical Engineering</option>
                            <option value="BS Industrial Engineering">BS Industrial Engineering</option>
                            <option value="BS Accountancy">BS Accountancy</option>
                            <option value="BS Business Administration">BS Business Administration</option>
                            <option value="BS Tourism Management">BS Tourism Management</option>
                            <option value="BS Hospitality Management">BS Hospitality Management</option>
                            <option value="BS Psychology">BS Psychology</option>
                            <option value="BS Education">BS Education</option>
                            <option value="BS Nursing">BS Nursing</option>
                            <option value="BS Medical Technology">BS Medical Technology</option>
                            <option value="BS Pharmacy">BS Pharmacy</option>
                            <option value="BS Biology">BS Biology</option>
                            <option value="BS Chemistry">BS Chemistry</option>
                            <option value="BS Mathematics">BS Mathematics</option>
                            <option value="BS Physics">BS Physics</option>
                            <option value="BS Environmental Science">BS Environmental Science</option>
                            <option value="BS Agriculture">BS Agriculture</option>
                            <option value="BS Fisheries">BS Fisheries</option>
                            <option value="BS Forestry">BS Forestry</option>
                            <option value="BS Architecture">BS Architecture</option>
                            <option value="BS Interior Design">BS Interior Design</option>
                            <option value="BS Fine Arts">BS Fine Arts</option>
                            <option value="BS Communication">BS Communication</option>
                            <option value="BS Social Work">BS Social Work</option>
                            <option value="BS Criminology">BS Criminology</option>
                            <option value="BS Political Science">BS Political Science</option>
                            <option value="BS History">BS History</option>
                            <option value="BS Literature">BS Literature</option>
                            <option value="BS Philosophy">BS Philosophy</option>
                            <option value="BS Economics">BS Economics</option>
                            <option value="BS Sociology">BS Sociology</option>
                            <option value="BS Anthropology">BS Anthropology</option>
                        </select>
                    </template>

                    <template x-if="cond.type === 'campus'">
                        <select x-model="cond.value" :name="'conditions['+index+'][value]'" 
                                class="w-2/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                            <option value="">-- Select Campus --</option>
                            <option value="BatStateU Alangilan">BatStateU Alangilan</option>
                            <option value="BatStateU Main">BatStateU Main</option>
                            <option value="BatStateU Lipa">BatStateU Lipa</option>
                            <option value="BatStateU Malvar">BatStateU Malvar</option>
                            <option value="BatStateU Lemery">BatStateU Lemery</option>
                            <option value="BatStateU San Juan">BatStateU San Juan</option>
                            <option value="BatStateU Lobo">BatStateU Lobo</option>
                            <option value="BatStateU Rosario">BatStateU Rosario</option>
                            <option value="BatStateU Balayan">BatStateU Balayan</option>
                            <option value="BatStateU Calaca">BatStateU Calaca</option>
                            <option value="BatStateU Calatagan">BatStateU Calatagan</option>
                            <option value="BatStateU Mabini">BatStateU Mabini</option>
                            <option value="BatStateU Nasugbu">BatStateU Nasugbu</option>
                            <option value="BatStateU Tuy">BatStateU Tuy</option>
                        </select>
                    </template>

                    <template x-if="cond.type === 'age'">
                        <input type="number" x-model="cond.value" 
                               :name="'conditions['+index+'][value]'" 
                               placeholder="e.g. 18"
                               class="w-2/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                    </template>

                    <template x-if="cond.type === 'sex'">
                        <select x-model="cond.value" :name="'conditions['+index+'][value]'" 
                                class="w-2/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                            <option value="">-- Select Gender --</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
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
