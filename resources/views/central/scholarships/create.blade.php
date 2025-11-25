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
<body class="bg-gray-50 min-h-screen">

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">

    @include('central.partials.page-header', [
        'title' => isset($scholarship) ? 'Edit Scholarship' : 'Add New Scholarship'
    ])

    <div class="bg-white rounded-xl shadow-lg border-2 border-red-700 p-6 md:p-8 mt-6">

    <form method="POST" enctype="multipart/form-data"
          action="{{ isset($scholarship) 
                    ? route('central.scholarships.update', $scholarship->id) 
                    : route('central.scholarships.store') }}">
        @csrf
        @if(isset($scholarship))
            @method('PUT')
        @endif
        
        <!-- Show validation errors -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <strong>Please fix the following errors:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Show success message -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <strong>Success!</strong> {{ session('success') }}
            </div>
        @endif

        <!-- Scholarship Type -->
        <label class="block text-sm font-medium text-gray-700 mb-2">Scholarship Type</label>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="flex items-center">
                <input type="radio" id="scholarship_type_private" name="scholarship_type" value="private"
                       {{ old('scholarship_type', $scholarship->scholarship_type ?? 'private') === 'private' ? 'checked' : '' }}
                       class="h-4 w-4 text-red-700 focus:ring-red-700 border-gray-300">
                <label for="scholarship_type_private" class="ml-2 block text-sm text-gray-700">Private</label>
            </div>
            <div class="flex items-center">
                <input type="radio" id="scholarship_type_government" name="scholarship_type" value="government"
                       {{ old('scholarship_type', $scholarship->scholarship_type ?? 'private') === 'government' ? 'checked' : '' }}
                       class="h-4 w-4 text-red-700 focus:ring-red-700 border-gray-300">
                <label for="scholarship_type_government" class="ml-2 block text-sm text-gray-700">Government</label>
            </div>
        </div>

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
        <div x-data="{ 
            conditions: (function(){
                try {
                    const el = document.getElementById('conditions-data');
                    return JSON.parse((el && el.textContent) || '[]');
                } catch(e) { return []; }
            })(),
            init() {
                // Conditions initialized
            }
        }" class="mt-6">
            <h3 class="text-md font-semibold text-gray-800 mb-2">Condition Requirements</h3>
            

            <div x-show="conditions.length === 0" class="text-gray-500 text-sm mb-2">
                No conditions added yet. Click "Add Condition" to get started.
            </div>

            <template x-for="(cond, index) in conditions" :key="index">
                <div class="flex space-x-2 mt-2">
                    <select x-model="cond.type" :name="'conditions['+index+'][type]'" 
                            class="w-1/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                        <option value="gwa">Minimum GWA</option>
                        <option value="year_level">Year Level</option>
                        <option value="income">Maximum Income</option>
                        <option value="disability">Disability</option>
                        <option value="program">Program</option>
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
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
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


                    <template x-if="cond.type === 'sex'">
                        <select x-model="cond.value" :name="'conditions['+index+'][value]'" 
                                class="w-2/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                            <option value="">-- Select Gender --</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </template>

                    <button type="button" @click="conditions.splice(index,1)" class="text-red-600 font-bold hover:text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </template>

            <button type="button" @click="conditions.push({type:'gwa',value:''})"
                    class="mt-2 px-3 py-1 bg-red-700 text-white rounded-lg hover:bg-red-800">
                + Add Condition
            </button>
        </div>

        <!-- Requirements -->
        <div x-data="{ 
            documents: (function(){
                try {
                    const el = document.getElementById('documents-data');
                    return JSON.parse((el && el.textContent) || '[]');
                } catch(e) { return []; }
            })(),
            init() {
                // Documents initialized
            }
        }" class="mt-6">
            <h3 class="text-md font-semibold text-gray-800 mb-2">Document Requirements</h3>
            

            <div x-show="documents.length === 0" class="text-gray-500 text-sm mb-2">
                No documents required yet. Click "Add Document" to get started.
            </div>

            <template x-for="(doc, index) in documents" :key="index">
                <div class="flex space-x-2 mt-2">
                    <input type="text" x-model="doc.name" :name="'documents['+index+'][name]'" 
                           placeholder="Document Name"
                           class="w-1/3 border rounded-lg p-2 focus:ring focus:ring-red-700">

                    <select x-model="doc.type" :name="'documents['+index+'][type]'" 
                            class="w-1/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                        <option value="pdf">PDF</option>
                        <option value="image">Image</option>
                        <option value="both">Both</option>
                    </select>

                    <select x-model="doc.mandatory" :name="'documents['+index+'][mandatory]'" 
                            class="w-1/3 border rounded-lg p-2 focus:ring focus:ring-red-700">
                        <option value="1">Required</option>
                        <option value="0">Optional</option>
                    </select>

                    <button type="button" @click="documents.splice(index,1)" class="text-red-600 font-bold hover:text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </template>

            <button type="button" @click="documents.push({name:'', type:'pdf', mandatory:1})"
                    class="mt-2 px-3 py-1 bg-red-700 text-white rounded-lg hover:bg-red-800">
                + Add Document
            </button>
        </div>

        <!-- Application Start Date -->
        <label for="application_start_date" class="block text-sm font-medium text-gray-700 mt-2">Application Start Date (Optional)</label>
        <input type="date" id="application_start_date" name="application_start_date"
               value="{{ old('application_start_date', isset($scholarship->application_start_date) ? \Carbon\Carbon::parse($scholarship->application_start_date)->format('Y-m-d') : '') }}"
               class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700">
        <p class="text-xs text-gray-500 mt-1">Leave empty to allow immediate applications</p>

        <!-- Submission Deadline -->
        <label for="submission_deadline" class="block text-sm font-medium text-gray-700 mt-2">Submission Deadline</label>
        <input type="date" id="submission_deadline" name="submission_deadline"
               value="{{ old('submission_deadline', isset($scholarship->submission_deadline) ? \Carbon\Carbon::parse($scholarship->submission_deadline)->format('Y-m-d') : '') }}" required
               class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700">

        <!-- Slots Available -->
        <label for="slots_available" class="block text-sm font-medium text-gray-700 mt-2">Slots Available</label>
        <input type="number" id="slots_available" name="slots_available" min="0"
               value="{{ old('slots_available', $scholarship->slots_available ?? '') }}"
               class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700">
        <p class="text-xs text-gray-500 mt-1">Leave empty for unlimited slots</p>

        <!-- Grant Amount -->
        <label for="grant_amount" class="block text-sm font-medium text-gray-700 mt-2">Grant Amount (₱)</label>
        <input type="number" step="0.01" id="grant_amount" name="grant_amount" min="0"
               value="{{ old('grant_amount', $scholarship->grant_amount ?? '') }}"
               class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700">

        <!-- Eligibility Notes -->
        <label for="eligibility_notes" class="block text-sm font-medium text-gray-700 mt-2">Additional Eligibility Notes (Optional)</label>
        <textarea id="eligibility_notes" name="eligibility_notes" rows="3"
                  class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700">{{ old('eligibility_notes', $scholarship->eligibility_notes ?? '') }}</textarea>
        <p class="text-xs text-gray-500 mt-1">Additional information about eligibility requirements</p>

        <!-- Background Image Upload -->
        <label for="background_image" class="block text-sm font-medium text-gray-700 mt-2">Background Image (Optional)</label>
        <input type="file" id="background_image" name="background_image" accept="image/*"
               class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700">
        <p class="text-xs text-gray-500 mt-1">Upload a background image for this scholarship (JPEG, PNG, JPG, GIF - Max 2MB)</p>
        
        @if(isset($scholarship) && $scholarship->background_image)
            <div class="mt-2">
                <p class="text-sm text-gray-600">Current image:</p>
                <img src="{{ $scholarship->getBackgroundImageUrl() }}" alt="Current background" class="w-32 h-20 object-cover rounded border">
            </div>
        @endif

        <!-- Grant Type -->
        <label for="grant_type" class="block text-sm font-medium text-gray-700 mt-4">Grant Type</label>
        <select id="grant_type" name="grant_type" required
                class="w-full border rounded-lg p-2 focus:ring focus:ring-red-700">
            <option value="">Select Grant Type</option>
            <option value="one_time" {{ old('grant_type', $scholarship->grant_type ?? '') == 'one_time' ? 'selected' : '' }}>One-time Grant</option>
            <option value="recurring" {{ old('grant_type', $scholarship->grant_type ?? '') == 'recurring' ? 'selected' : '' }}>Recurring Grants</option>
            <option value="discontinued" {{ old('grant_type', $scholarship->grant_type ?? '') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
        </select>
        <p class="text-xs text-gray-500 mt-1">
            <strong>One-time:</strong> Single grant only, closes after first claim. Renewal is not allowed.<br>
            <strong>Recurring:</strong> Multiple grants allowed (semester-based or as announced). Renewal is automatically allowed.<br>
            <strong>Discontinued:</strong> Scholarship has been cancelled or discontinued. Renewal is not allowed.
        </p>

        <!-- Actions -->
        <div class="mt-6 flex justify-between">
            <a href="{{ route('central.dashboard') }}"
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
                Cancel
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-semibold rounded-lg transition">
                @if(isset($scholarship))
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v7a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z" />
                        </svg>
                        Update
                    </span>
                @else
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        Submit
                    </span>
                @endif
            </button>
        </div>
    </form>
    </div>
</div>

</body>
</html>
