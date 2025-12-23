<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    public function run()
    {
        $programs = [
            // CABEIHM (Pablo Borbon) - Updated List
            ['college' => 'CABEIHM', 'name' => 'Bachelor of Science in Accountancy', 'short_name' => 'BSA'],
            ['college' => 'CABEIHM', 'name' => 'Bachelor of Science in Management Accounting', 'short_name' => 'BSMA'],
            ['college' => 'CABEIHM', 'name' => 'Bachelor of Science in Business Administration', 'short_name' => 'BSBA'],
            ['college' => 'CABEIHM', 'name' => 'Bachelor of Science in Entrepreneurship', 'short_name' => 'BSEntrep'], 
            ['college' => 'CABEIHM', 'name' => 'Bachelor in Public Administration', 'short_name' => 'BPA'],
            ['college' => 'CABEIHM', 'name' => 'Bachelor of Science in Hospitality Management', 'short_name' => 'BSHM'],
            ['college' => 'CABEIHM', 'name' => 'Bachelor of Science in Tourism Management', 'short_name' => 'BSTM'],
            ['college' => 'CABEIHM', 'name' => 'Bachelor of Science in Customs Administration', 'short_name' => 'BSCA'],
            
            // CAS
            ['college' => 'CAS', 'name' => 'Bachelor of Science in Psychology', 'short_name' => 'BS Psychology'],
            ['college' => 'CAS', 'name' => 'Bachelor of Science in Criminology', 'short_name' => 'BS Criminology'],
            ['college' => 'CAS', 'name' => 'Bachelor of Arts in Communication', 'short_name' => 'BA Comm'],
            ['college' => 'CAS', 'name' => 'Bachelor of Science in Biology', 'short_name' => 'BS Biology'],
            ['college' => 'CAS', 'name' => 'Bachelor of Science in Chemistry', 'short_name' => 'BS Chemistry'],
            ['college' => 'CAS', 'name' => 'Bachelor of Science in Mathematics', 'short_name' => 'BS Math'],
            ['college' => 'CAS', 'name' => 'Bachelor of Arts in English Language Studies', 'short_name' => 'BA ELS'],

            // CICS
            ['college' => 'CICS', 'name' => 'Bachelor of Science in Information Technology', 'short_name' => 'BSIT'], 
            ['college' => 'CICS', 'name' => 'Bachelor of Science in Computer Science', 'short_name' => 'BSCS'],

            // CIT
            ['college' => 'CIT', 'name' => 'Bachelor of Industrial Technology', 'short_name' => 'BIT'],
            ['college' => 'CIT', 'name' => 'Diploma in Industrial Technology', 'short_name' => 'DIT'],

            // CTE
            ['college' => 'CTE', 'name' => 'Bachelor of Elementary Education', 'short_name' => 'BEEd'],
            ['college' => 'CTE', 'name' => 'Bachelor of Secondary Education', 'short_name' => 'BSEd'],
            ['college' => 'CTE', 'name' => 'Bachelor of Technology and Livelihood Education', 'short_name' => 'BTLEd'],
            ['college' => 'CTE', 'name' => 'Bachelor of Physical Education', 'short_name' => 'BPEd'],

            // CEAF (Engineering & Architecture)
            ['college' => 'CEAF', 'name' => 'Bachelor of Science in Civil Engineering', 'short_name' => 'BSCE'],
            ['college' => 'CEAF', 'name' => 'Bachelor of Science in Chemical Engineering', 'short_name' => 'BSCheE'],
            ['college' => 'CEAF', 'name' => 'Bachelor of Science in Computer Engineering', 'short_name' => 'BSCpE'],
            ['college' => 'CEAF', 'name' => 'Bachelor of Science in Electrical Engineering', 'short_name' => 'BSEE'],
            ['college' => 'CEAF', 'name' => 'Bachelor of Science in Electronics Engineering', 'short_name' => 'BSECE'],
            ['college' => 'CEAF', 'name' => 'Bachelor of Science in Industrial Engineering', 'short_name' => 'BSIE'],
            ['college' => 'CEAF', 'name' => 'Bachelor of Science in Mechanical Engineering', 'short_name' => 'BSME'],
            ['college' => 'CEAF', 'name' => 'Bachelor of Science in Architecture', 'short_name' => 'BSArch'],
            ['college' => 'CEAF', 'name' => 'Bachelor of Fine Arts', 'short_name' => 'BFA'],

            // CHS
            ['college' => 'CHS', 'name' => 'Bachelor of Science in Nursing', 'short_name' => 'BSN'],
            ['college' => 'CHS', 'name' => 'Bachelor of Science in Nutrition and Dietetics', 'short_name' => 'BSND'],

            // COL
            ['college' => 'COL', 'name' => 'Bachelor of Laws', 'short_name' => 'LLB'],
            ['college' => 'COL', 'name' => 'Juris Doctor', 'short_name' => 'JD'],
        ];

        foreach ($programs as $prog) {
            Program::updateOrCreate(
                ['name' => $prog['name']], // Unique by full name
                [
                    'college' => $prog['college'],
                    'short_name' => $prog['short_name']
                ]
            );
        }
    }
}
