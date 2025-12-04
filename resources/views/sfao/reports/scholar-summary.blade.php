@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Report Actions -->
    <div class="mb-6 flex justify-between items-center no-print">
        <a href="{{ route('sfao.dashboard', ['tab' => 'reports-scholar_summary']) }}" class="text-gray-600 hover:text-bsu-red font-medium flex items-center transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Reports
        </a>
        <button onclick="window.print()" class="bg-bsu-red text-white px-4 py-2 rounded-md hover:bg-bsu-redDark flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Print Report
        </button>
    </div>

    <!-- Report Content -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden print:shadow-none">
        <!-- Report Header -->
        <div class="px-8 py-6 border-b border-gray-200 text-center">
            <img src="{{ asset('images/Batangas_State_Logo.png') }}" alt="BSU Logo" class="h-20 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-900 uppercase">Batangas State University</h1>
            <h2 class="text-xl font-semibold text-gray-700 mt-2">The National Engineering University</h2>
            <h3 class="text-lg font-medium text-gray-600 mt-4 uppercase tracking-wide">Scholar Summary Report</h3>
            
            <!-- Dynamic Campus Subtitle -->
            <div class="mt-2 inline-block px-4 py-1 rounded-full bg-red-50 text-bsu-red font-bold text-sm uppercase tracking-wider border border-red-100">
                Campus: {{ request('campus_id') == 'all' ? 'All Campuses' : $monitoredCampuses->where('id', request('campus_id'))->first()->name ?? 'Unknown Campus' }}
            </div>

            <p class="text-sm text-gray-500 mt-4">Generated on {{ now()->format('F d, Y') }}</p>
            <p class="text-sm text-gray-500">Prepared by: {{ $user->name }}</p>
        </div>

        <div class="p-8 space-y-12">

            <!-- Campus Sections -->
            @foreach($reportData as $data)
                <div class="campus-section break-inside-avoid">
                    <h2 class="text-2xl font-bold text-bsu-red border-b-2 border-bsu-red pb-2 mb-6 uppercase">
                        {{ $data['campus']->name }}
                    </h2>

                    <!-- Scholars Table -->
                    <div class="mb-8 border border-red-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-2">List of Scholars</h3>
                        @if(count($data['scholars']) > 0)
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 border-b border-gray-200">
                                        <th class="pb-2 font-medium">Applicant Name</th>
                                        <th class="pb-2 font-medium">Status</th>
                                        <th class="pb-2 font-medium">Department</th>
                                        <th class="pb-2 font-medium text-right">Year Level</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($data['scholars'] as $scholar)
                                        <tr>
                                            <td class="py-2 font-medium text-gray-900">{{ $scholar['name'] }}</td>
                                            <td class="py-2">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                    {{ str_contains($scholar['status'], 'Old') ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ $scholar['status'] }}
                                                </span>
                                            </td>
                                            <td class="py-2 text-gray-600">{{ $scholar['department'] }}</td>
                                            <td class="py-2 text-right text-gray-600">{{ $scholar['year_level'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="flex justify-center py-4">
                                <span class="bg-gray-100 text-gray-500 px-6 py-2 rounded-full text-sm italic border border-gray-200 shadow-sm">
                                    No scholars found
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Non-Scholars Table -->
                    <div class="mb-8 border border-red-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-2">List of Non-Scholars</h3>
                        @if(count($data['non_scholars']) > 0)
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left text-gray-500 border-b border-gray-200">
                                        <th class="pb-2 font-medium">Applicant Name</th>
                                        <th class="pb-2 font-medium">Status</th>
                                        <th class="pb-2 font-medium">Department</th>
                                        <th class="pb-2 font-medium text-right">Year Level</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($data['non_scholars'] as $nonScholar)
                                        <tr>
                                            <td class="py-2 font-medium text-gray-900">{{ $nonScholar['name'] }}</td>
                                            <td class="py-2">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    {{ $nonScholar['status'] }}
                                                </span>
                                            </td>
                                            <td class="py-2 text-gray-600">{{ $nonScholar['department'] }}</td>
                                            <td class="py-2 text-right text-gray-600">{{ $nonScholar['year_level'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="flex justify-center py-4">
                                <span class="bg-gray-100 text-gray-500 px-6 py-2 rounded-full text-sm italic border border-gray-200 shadow-sm">
                                    No non-scholars found
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Performance Rate / Summary Status -->
                    <div class="break-inside-avoid mt-8 mb-8 bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 uppercase">Performance Rate</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                            <div class="bg-white p-4 rounded shadow-sm">
                                <p class="text-sm text-gray-500 uppercase tracking-wide">Old Scholars</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $summaryStats['old_scholars'] }}</p>
                            </div>
                            <div class="bg-white p-4 rounded shadow-sm">
                                <p class="text-sm text-gray-500 uppercase tracking-wide">New Scholars</p>
                                <p class="text-2xl font-bold text-green-600">{{ $summaryStats['new_scholars'] }}</p>
                            </div>
                            <div class="bg-white p-4 rounded shadow-sm">
                                <p class="text-sm text-gray-500 uppercase tracking-wide">Non-Scholars</p>
                                <p class="text-2xl font-bold text-gray-600">{{ $summaryStats['non_scholars'] }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach

            <!-- Footer -->
            <div class="mt-12 pt-8 border-t border-gray-200 text-center text-sm text-gray-500">
                <p>This report is system-generated and serves as an official summary of scholars and non-scholars.</p>
                <p class="mt-1">Prepared by: {{ $user->name }} (SFAO Admin)</p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background: white;
        }
        .shadow-lg {
            box-shadow: none !important;
        }
        .max-w-7xl {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .break-inside-avoid {
            break-inside: avoid;
        }
    }
</style>
@endsection
