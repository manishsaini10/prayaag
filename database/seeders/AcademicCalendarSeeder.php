<?php

namespace Database\Seeders;

use App\Models\AcademicCalendarEntry;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AcademicCalendarSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Fetch created_by User
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name'     => 'School Admin',
                'email'    => 'admin@school.test',
                'password' => bcrypt('password')
            ]);
        }

        // 2. Create Classes
        $classesData = [
            'Nursery', 'LKG', 'UKG',
            'Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5',
            'Class 6', 'Class 7', 'Class 8', 'Class 9', 'Class 10',
            'Class 11', 'Class 12'
        ];

        $classes = [];
        foreach ($classesData as $className) {
            $classes[$className] = SchoolClass::firstOrCreate(['class_name' => $className]);
        }

        // 3. Create Academic Session
        $session = AcademicSession::firstOrCreate(
            ['session_name' => '2026-2027'],
            [
                'start_date' => '2026-04-01',
                'end_date'   => '2027-03-31',
                'is_current' => true
            ]
        );

        // Make sure it is current if it already existed
        $session->update(['is_current' => true]);
        AcademicSession::where('id', '!=', $session->id)->update(['is_current' => false]);

        // 4. Create Academic Terms
        $term1 = AcademicTerm::firstOrCreate(
            [
                'session_id' => $session->id,
                'term_name'  => 'Term 1'
            ],
            [
                'start_date' => '2026-04-01',
                'end_date'   => '2026-09-30'
            ]
        );

        $term2 = AcademicTerm::firstOrCreate(
            [
                'session_id' => $session->id,
                'term_name'  => 'Term 2'
            ],
            [
                'start_date' => '2026-10-01',
                'end_date'   => '2027-03-31'
            ]
        );

        // 5. Create Calendar Entries
        $entries = [
            // --- Term 1 ---
            [
                'session_id'     => $session->id,
                'term_id'        => $term1->id,
                'title'          => 'New Session Commencement & Orientation',
                'category'       => 'important_date',
                'sub_type'       => 'Orientation',
                'description'    => 'Orientation ceremony and materials distribution for new admissions and returning students.',
                'start_date'     => '2026-04-05',
                'end_date'       => null,
                'class_id'       => null,
                'is_working_day' => true,
                'color_tag'      => 'blue',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term1->id,
                'title'          => 'Unit Test 1',
                'category'       => 'exam',
                'sub_type'       => 'Unit Test',
                'description'    => 'First cycle of monthly unit tests for Classes 6 to 12.',
                'start_date'     => '2026-05-11',
                'end_date'       => '2026-05-16',
                'class_id'       => $classes['Class 10']->id,
                'is_working_day' => true,
                'color_tag'      => 'red',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term1->id,
                'title'          => 'Special Saturday Working Session',
                'category'       => 'working_day_note',
                'sub_type'       => 'Working Saturday',
                'description'    => 'Saturday working session to compensate for summer holidays and complete syllabus.',
                'start_date'     => '2026-05-23',
                'end_date'       => null,
                'class_id'       => null,
                'is_working_day' => true,
                'color_tag'      => 'grey',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term1->id,
                'title'          => 'Parents Teachers Meeting (PTM 1)',
                'category'       => 'important_date',
                'sub_type'       => 'PTM',
                'description'    => 'Discussion of Unit Test 1 results and performance reviews with parents.',
                'start_date'     => '2026-05-28',
                'end_date'       => null,
                'class_id'       => null,
                'is_working_day' => true,
                'color_tag'      => 'blue',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term1->id,
                'title'          => 'Summer Vacation Break',
                'category'       => 'holiday',
                'sub_type'       => 'Summer Vacations',
                'description' => 'School remains closed for summer break. Classes resume on July 11.',
                'start_date'     => '2026-06-01',
                'end_date'       => '2026-07-10',
                'class_id'       => null,
                'is_working_day' => false,
                'color_tag'      => 'yellow',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term1->id,
                'title'          => 'Independence Day Celebration',
                'category'       => 'important_date',
                'sub_type'       => 'National Event',
                'description'    => 'Flag hoisting ceremony and cultural performances by students.',
                'start_date'     => '2026-08-15',
                'end_date'       => null,
                'class_id'       => null,
                'is_working_day' => true,
                'color_tag'      => 'blue',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term1->id,
                'title'          => 'Term 1 Mid-Term Examination',
                'category'       => 'exam',
                'sub_type'       => 'Mid-Term',
                'description'    => 'Half-yearly comprehensive examinations for all classes.',
                'start_date'     => '2026-09-14',
                'end_date'       => '2026-09-24',
                'class_id'       => null,
                'is_working_day' => true,
                'color_tag'      => 'red',
            ],

            // --- Term 2 ---
            [
                'session_id'     => $session->id,
                'term_id'        => $term2->id,
                'title'          => 'Gandhi Jayanti Holiday',
                'category'       => 'holiday',
                'sub_type'       => 'National Holiday',
                'description'    => 'National holiday on account of Mahatma Gandhi Birthday.',
                'start_date'     => '2026-10-02',
                'end_date'       => null,
                'class_id'       => null,
                'is_working_day' => false,
                'color_tag'      => 'yellow',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term2->id,
                'title'          => 'Diwali & Autumn Holidays',
                'category'       => 'holiday',
                'sub_type'       => 'Festival Holiday',
                'description'    => 'School closed for Diwali festival celebration.',
                'start_date'     => '2026-11-07',
                'end_date'       => '2026-11-11',
                'class_id'       => null,
                'is_working_day' => false,
                'color_tag'      => 'yellow',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term2->id,
                'title'          => 'Annual Sports Meet',
                'category'       => 'important_date',
                'sub_type'       => 'Sports Day',
                'description'    => 'Two-day athletic track events and field competitions for all wings.',
                'start_date'     => '2026-11-20',
                'end_date'       => '2026-11-21',
                'class_id'       => null,
                'is_working_day' => true,
                'color_tag'      => 'blue',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term2->id,
                'title'          => 'Annual Day & Cultural Fest',
                'category'       => 'important_date',
                'sub_type'       => 'Annual Function',
                'description'    => 'Grand stage play, music ensemble, and academic award distributions.',
                'start_date'     => '2026-12-18',
                'end_date'       => null,
                'class_id'       => null,
                'is_working_day' => true,
                'color_tag'      => 'blue',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term2->id,
                'title'          => 'Winter Break Holidays',
                'category'       => 'holiday',
                'sub_type'       => 'Winter Vacations',
                'description'    => 'Winter break vacations. School resumes on January 6.',
                'start_date'     => '2026-12-25',
                'end_date'       => '2027-01-05',
                'class_id'       => null,
                'is_working_day' => false,
                'color_tag'      => 'yellow',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term2->id,
                'title'          => 'Pre-Board Examination',
                'category'       => 'exam',
                'sub_type'       => 'Pre-Board',
                'description'    => 'Preparatory mock board exams for Classes 10 and 12.',
                'start_date'     => '2027-01-18',
                'end_date'       => '2027-01-29',
                'class_id'       => $classes['Class 10']->id,
                'is_working_day' => true,
                'color_tag'      => 'red',
            ],
            [
                'session_id'     => $session->id,
                'term_id'        => $term2->id,
                'title'          => 'Final Examination Series',
                'category'       => 'exam',
                'sub_type'       => 'Final Exam',
                'description'    => 'Annual final examinations for academic grading and term wrap-up.',
                'start_date'     => '2027-03-08',
                'end_date'       => '2027-03-24',
                'class_id'       => null,
                'is_working_day' => true,
                'color_tag'      => 'red',
            ],
        ];

        // Also add an upcoming event relative to today's execution date to verify the soft pulse glow animation!
        // The execution is July 2026.
        $entries[] = [
            'session_id'     => $session->id,
            'term_id'        => $term1->id,
            'title'          => 'Upcoming Special Workshop',
            'category'       => 'important_date',
            'sub_type'       => 'Workshop',
            'description'    => 'Interactive STEM workshop and exhibition organized in the science labs.',
            'start_date'     => now()->addDays(2)->toDateString(),
            'end_date'       => null,
            'class_id'       => null,
            'is_working_day' => true,
            'color_tag'      => 'blue',
        ];

        foreach ($entries as $e) {
            $e['created_by'] = $user->id;
            AcademicCalendarEntry::firstOrCreate(
                [
                    'session_id' => $e['session_id'],
                    'title'      => $e['title'],
                    'start_date' => $e['start_date']
                ],
                $e
            );
        }
    }
}
