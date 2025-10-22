<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Application;
use App\Models\Scholar;
use App\Models\Scholarship;
use App\Models\Campus;
use Carbon\Carbon;

class ApplicationsAndScholarsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all students
        $students = User::where('role', 'student')->get();
        $scholarships = Scholarship::all();
        
        if ($students->isEmpty() || $scholarships->isEmpty()) {
            $this->command->error('❌ Please run UsersTableSeeder and ScholarshipsTableSeeder first!');
            return;
        }
        
        // Split students: 50 scholars, 50 applicants
        $scholarStudents = $students->take(50);
        $applicantStudents = $students->skip(50);
        
        $this->command->info('🎓 Creating 50 scholars...');
        $this->createScholars($scholarStudents, $scholarships);
        
        $this->command->info('📝 Creating 50 applicants...');
        $this->createApplicants($applicantStudents, $scholarships);
        
        $this->command->info('✅ Applications and scholars created successfully!');
    }
    
    private function createScholars($students, $scholarships)
    {
        foreach ($students as $student) {
            // Create approved application
            $scholarship = $scholarships->random();
            $grantCount = rand(0, 3); // 0-3 grants (0 = new scholar, 1+ = old scholar)
            $application = Application::create([
                'user_id' => $student->id,
                'scholarship_id' => $scholarship->id,
                'type' => rand(0, 1) ? 'new' : 'continuing',
                'status' => 'approved',
                'grant_count' => $grantCount,
                'created_at' => now()->subDays(rand(30, 180)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
            
            // Create scholar record
            $scholarType = $grantCount > 0 ? 'old' : 'new';
            $startDate = $application->created_at->startOfMonth();
            $endDate = $scholarship->renewal_allowed 
                ? $startDate->copy()->addYear() 
                : $startDate->copy()->addMonths(6);
            
            Scholar::create([
                'user_id' => $student->id,
                'scholarship_id' => $scholarship->id,
                'application_id' => $application->id,
                'type' => $scholarType,
                'grant_count' => $application->grant_count,
                'total_grant_received' => $application->grant_count * $scholarship->grant_amount,
                'scholarship_start_date' => $startDate,
                'scholarship_end_date' => $endDate,
                'status' => 'active',
                'notes' => 'Created from approved application',
                'grant_history' => $this->generateGrantHistory($application, $scholarship),
            ]);
        }
    }
    
    private function createApplicants($students, $scholarships)
    {
        $applicationStatuses = ['pending', 'in_progress', 'rejected'];
        $statusWeights = [0.6, 0.3, 0.1]; // 60% pending, 30% in_progress, 10% rejected
        
        foreach ($students as $student) {
            $scholarship = $scholarships->random();
            $status = $this->weightedRandom($applicationStatuses, $statusWeights);
            
            Application::create([
                'user_id' => $student->id,
                'scholarship_id' => $scholarship->id,
                'type' => rand(0, 1) ? 'new' : 'continuing',
                'status' => $status,
                'grant_count' => 0,
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => $status === 'rejected' ? now()->subDays(rand(1, 15)) : null,
            ]);
        }
    }
    
    private function generateGrantHistory($application, $scholarship)
    {
        if ($application->grant_count === 0) {
            return null;
        }
        
        $grantHistory = [];
        $grantAmount = $scholarship->grant_amount;
        $startDate = $application->created_at->startOfMonth();
        
        for ($i = 1; $i <= $application->grant_count; $i++) {
            $grantDate = $startDate->copy()->addMonths($i - 1);
            
            $grantHistory[] = [
                'date' => $grantDate->toDateString(),
                'amount' => $grantAmount,
                'description' => "Grant #{$i} - {$scholarship->scholarship_name}",
                'grant_number' => $i
            ];
        }
        
        return $grantHistory;
    }
    
    private function weightedRandom($items, $weights)
    {
        $totalWeight = array_sum($weights);
        $random = mt_rand() / mt_getrandmax() * $totalWeight;
        
        $currentWeight = 0;
        foreach ($items as $index => $item) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $item;
            }
        }
        
        return $items[0]; // Fallback
    }
}
