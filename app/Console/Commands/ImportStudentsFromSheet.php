<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Student;

class ImportStudentsFromSheet extends Command
{
    protected $signature   = 'students:import';
    protected $description = 'Google Sheet se students import karo';

    public function handle()
    {
        $this->info('📥 Google Sheet se data fetch ho raha hai...');

        // ─── Google Sheet se Data Fetch ────────────
        $url      = env('GOOGLE_SHEET_URL');
        $response = Http::get($url);

        if (!$response->successful()) {
            $this->error('❌ Google Sheet se data nahi aaya!');
            return;
        }

        $students = $response->json()['data'];

        if (empty($students)) {
            $this->error('❌ Sheet mein koi data nahi!');
            return;
        }

        $this->info('✅ ' . count($students) . ' students mile — DB mein save ho rahe hain...');

        // ─── Progress Bar ──────────────────────────
        $bar = $this->output->createProgressBar(count($students));
        $bar->start();

        $inserted = 0;
        $skipped  = 0;

        foreach ($students as $student) {

            // Already exist karta hai toh skip karo
            $exists = Student::where('student_id', $student['studentId'] ?? '')
                             ->orWhere('email', $student['email'] ?? '')
                             ->exists();

            if ($exists) {
                $skipped++;
                $bar->advance();
                continue;
            }

            Student::create([
                'student_id' => $student['studentId']   ?? '',
                'name'       => $student['studentName'] ?? '',
                'college'    => $student['college']     ?? '',
                'department' => $student['department']  ?? '',
                'course'     => $student['course']      ?? '',
                'year'       => $student['year']        ?? '',
                'location'   => $student['location']    ?? '',
                'city'       => $student['city']        ?? '',
                'state'      => $student['state']       ?? '',
                'email'      => $student['email']       ?? '',
                'phone'      => $student['phone']       ?? '',
                'gpa'        => $student['gpa']         ?? 0,
            ]);

            $inserted++;
            $bar->advance();
        }

        $bar->finish();

        // ─── Summary ───────────────────────────────
        $this->newLine();
        $this->info("✅ Inserted : $inserted students");
        $this->warn("⚠️  Skipped  : $skipped students (already exist)");
        $this->info('🎉 Import complete!');
    }
}