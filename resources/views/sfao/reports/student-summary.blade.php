@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Report Actions -->
    <div class="mb-6 flex justify-between items-center no-print">
        <a href="{{ route('sfao.dashboard', ['tab' => 'reports-student_summary']) }}" class="text-gray-600 hover:text-bsu-red font-medium flex items-center transition-colors duration-200">
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
            <h3 class="text-lg font-medium text-gray-600 mt-4 uppercase tracking-wide">Student Summary Report</h3>
            
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

                    @if(count($data['departments']) > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            @foreach($data['departments'] as $deptData)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 break-inside-avoid">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-300 pb-2">
                                        {{ $deptData['department']->name }}
                                    </h3>
                                    
                                    @if(count($deptData['students']) > 0)
                                        <table class="min-w-full text-sm">
                                            <thead>
                                                <tr class="text-left text-gray-500 border-b border-gray-200">
                                                    <th class="pb-2 font-medium">Applicant Name</th>
                                                    <th class="pb-2 font-medium text-right">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($deptData['students'] as $student)
                                                    <tr>
                                                        <td class="py-2 font-medium {{ $student['sex'] === 'Male' ? 'text-blue-600' : ($student['sex'] === 'Female' ? 'text-pink-600' : 'text-gray-900') }}">
                                                            {{ $student['name'] }}
                                                        </td>
                                                        <td class="py-2 text-right">
                                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                                {{ $student['status'] === 'Accepted' || $student['status'] === 'Approved' || $student['status'] === 'Claimed' ? 'bg-green-100 text-green-800' : 
                                                                  ($student['status'] === 'Rejected' || $student['status'] === 'Disapproved' ? 'bg-red-100 text-red-800' : 
                                                                  ($student['status'] === 'Not Applied' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                                                {{ $student['status'] }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="flex justify-center py-4">
                                            <span class="bg-gray-100 text-gray-500 px-6 py-2 rounded-full text-sm italic border border-gray-200 shadow-sm">
                                                No applicants found
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No departments found for this campus.</p>
                    @endif
                </div>
            @endforeach

            <!-- Performance Rate / Summary Status -->
            <div class="break-inside-avoid mt-12">
                <h2 class="text-2xl font-bold text-gray-900 border-b-2 border-gray-300 pb-2 mb-6 uppercase">
                    Performance Rate & Summary Status
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Summary Table -->
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $total = $summaryStats['total'] > 0 ? $summaryStats['total'] : 1;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">Accepted/Approved</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $summaryStats['accepted'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ round(($summaryStats['accepted'] / $total) * 100, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">Rejected</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $summaryStats['rejected'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ round(($summaryStats['rejected'] / $total) * 100, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-yellow-600">Pending</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $summaryStats['pending'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ round(($summaryStats['pending'] / $total) * 100, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-600">Not Applied</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $summaryStats['not_applied'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ round(($summaryStats['not_applied'] / $total) * 100, 1) }}%</td>
                                </tr>
                                <tr class="bg-gray-50 font-bold">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Total</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ $summaryStats['total'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">100%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Conclusion / Chart Placeholder -->
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Conclusion</h4>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Based on the current data, 
                            <span class="font-bold text-green-600">{{ round(($summaryStats['accepted'] / $total) * 100, 1) }}%</span> of students have been approved for scholarships. 
                            <span class="font-bold text-yellow-600">{{ round(($summaryStats['pending'] / $total) * 100, 1) }}%</span> are still pending review.
                            
                            @if($summaryStats['not_applied'] > 0)
                                A significant portion (<span class="font-bold text-gray-600">{{ round(($summaryStats['not_applied'] / $total) * 100, 1) }}%</span>) of registered students have not yet applied for any scholarship.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Remarks Report -->
            <div class="break-inside-avoid mt-12">
                <h2 class="text-2xl font-bold text-gray-900 border-b-2 border-gray-300 pb-2 mb-6 uppercase">
                    Remarks Report
                </h2>
                
                @if(count($remarksData) > 0)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campus</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Application Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($remarksData as $remark)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $remark['campus'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $remark['name'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                {{ $remark['status'] === 'Accepted' || $remark['status'] === 'Approved' ? 'bg-green-100 text-green-800' : 
                                                  ($remark['status'] === 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $remark['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-red-600 font-medium">{{ $remark['remarks'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-green-50 p-4 rounded-md border border-green-200 text-green-700 text-center">
                        No remarks found for any applicants.
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="mt-12 pt-8 border-t border-gray-200 text-center text-sm text-gray-500">
                <p>This report is system-generated and serves as an official summary of student applicants.</p>
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
