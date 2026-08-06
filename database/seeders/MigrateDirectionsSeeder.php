<?php

namespace Database\Seeders;

use App\Models\Direction;
use App\Models\Question;
use App\Models\Section;
use Illuminate\Database\Seeder;

class MigrateDirectionsSeeder extends Seeder
{
    /**
     * Migrate existing hardcoded directions data to the new Direction model.
     *
     * This seeder:
     * 1. Creates Direction records from existing Section audio fields (listening)
     * 2. Creates a default Direction for sections without explicit parts
     * 3. Links existing Questions to their new Direction via direction_id
     */
    public function run(): void
    {
        $sections = Section::all();

        foreach ($sections as $section) {
            if ($section->slug === 'listening') {
                // Listening section: create 3 directions from existing audio fields
                $partsConfig = [
                    'A' => [
                        'label' => 'Part A',
                        'title' => 'Short Conversations',
                        'description' => 'Anda akan mendengar percakapan pendek. Setelah audio selesai, pilih jawaban terbaik dari 4 pilihan.',
                        'audio_path' => $section->directions_audio_a,
                        'order' => 1,
                    ],
                    'B' => [
                        'label' => 'Part B',
                        'title' => 'Longer Conversations',
                        'description' => 'Anda akan mendengar percakapan yang lebih panjang. Setelah audio selesai, pilih jawaban terbaik dari 4 pilihan.',
                        'audio_path' => $section->directions_audio_b,
                        'order' => 2,
                    ],
                    'C' => [
                        'label' => 'Part C',
                        'title' => 'Short Talks',
                        'description' => 'Anda akan mendengar pembicaraan singkat. Setelah audio selesai, pilih jawaban terbaik dari 4 pilihan.',
                        'audio_path' => $section->directions_audio_c,
                        'order' => 3,
                    ],
                ];

                foreach ($partsConfig as $partKey => $config) {
                    $direction = Direction::firstOrCreate(
                        ['section_id' => $section->id, 'label' => $config['label']],
                        array_merge($config, ['section_id' => $section->id])
                    );

                    // Link questions with matching part to this direction
                    Question::where('section_id', $section->id)
                        ->where('part', $partKey)
                        ->whereNull('direction_id')
                        ->update(['direction_id' => $direction->id]);
                }

                // Any listening questions without a part, assign to Part A
                $partADirection = Direction::where('section_id', $section->id)
                    ->where('label', 'Part A')
                    ->first();

                if ($partADirection) {
                    Question::where('section_id', $section->id)
                        ->whereNull('direction_id')
                        ->update(['direction_id' => $partADirection->id]);
                }

            } else {
                // Non-listening sections: create a single "Directions" direction
                $direction = Direction::firstOrCreate(
                    ['section_id' => $section->id, 'label' => 'Directions'],
                    [
                        'section_id' => $section->id,
                        'label' => 'Directions',
                        'title' => $section->name,
                        'description' => $section->description ?? 'Baca petunjuk berikut sebelum mengerjakan soal.',
                        'order' => 1,
                    ]
                );

                // Link all questions in this section to the direction
                Question::where('section_id', $section->id)
                    ->whereNull('direction_id')
                    ->update(['direction_id' => $direction->id]);
            }
        }

        $this->command->info('✅ Migrated existing data to directions successfully.');
        $this->command->info('   Directions created: ' . Direction::count());
        $this->command->info('   Questions linked: ' . Question::whereNotNull('direction_id')->count() . '/' . Question::count());
    }
}
