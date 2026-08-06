<?php

namespace Database\Seeders;

use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use App\Models\Passage;
use App\Models\Question;
use App\Models\Section;
use App\Models\TestSession;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Admin TOEFL',
            'email' => 'admin@toefl.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create Student
        $student = User::create([
            'name' => 'Ahmad Mahasiswa',
            'email' => 'student@toefl.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        // Create Sections
        $listening = Section::create([
            'name' => 'Listening Comprehension',
            'slug' => 'listening',
            'description' => 'This section tests your ability to understand spoken English. You will hear conversations and talks, then answer questions about them.',
            'duration_minutes' => 35,
            'order' => 1,
        ]);

        $structure = Section::create([
            'name' => 'Structure & Written Expression',
            'slug' => 'structure',
            'description' => 'This section tests your ability to recognize correct grammar and usage in standard written English.',
            'duration_minutes' => 25,
            'order' => 2,
        ]);

        $reading = Section::create([
            'name' => 'Reading Comprehension',
            'slug' => 'reading',
            'description' => 'This section tests your ability to understand reading passages and answer questions about them.',
            'duration_minutes' => 55,
            'order' => 3,
        ]);

        // --- LISTENING QUESTIONS ---
        $listeningQuestions = [
            [
                'question_text' => 'What does the woman suggest the man do?',
                'option_a' => 'Visit the library',
                'option_b' => 'Talk to his professor',
                'option_c' => 'Register for a different course',
                'option_d' => 'Drop the class',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'What is the main topic of the conversation?',
                'option_a' => 'A homework assignment',
                'option_b' => 'A campus event',
                'option_c' => 'A research project',
                'option_d' => 'A job interview',
                'correct_answer' => 'C',
            ],
            [
                'question_text' => 'Why does the man go to see the professor?',
                'option_a' => 'To ask about the exam schedule',
                'option_b' => 'To discuss his grade',
                'option_c' => 'To request a letter of recommendation',
                'option_d' => 'To submit a late assignment',
                'correct_answer' => 'C',
            ],
            [
                'question_text' => 'What does the speaker imply about the new policy?',
                'option_a' => 'It will be difficult to implement',
                'option_b' => 'It is long overdue',
                'option_c' => 'It needs more discussion',
                'option_d' => 'It will save money',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'What will the woman probably do next?',
                'option_a' => 'Go to the bookstore',
                'option_b' => 'Call her advisor',
                'option_c' => 'Attend a lecture',
                'option_d' => 'Study in the library',
                'correct_answer' => 'A',
            ],
            [
                'question_text' => 'According to the lecture, what caused the decline?',
                'option_a' => 'Economic recession',
                'option_b' => 'Climate change',
                'option_c' => 'Population growth',
                'option_d' => 'Technological advancement',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'What can be inferred about the student?',
                'option_a' => 'She is a first-year student',
                'option_b' => 'She has taken the course before',
                'option_c' => 'She is majoring in biology',
                'option_d' => 'She works part-time',
                'correct_answer' => 'D',
            ],
            [
                'question_text' => 'What is the professor\'s attitude toward the theory?',
                'option_a' => 'Strongly supportive',
                'option_b' => 'Cautiously optimistic',
                'option_c' => 'Somewhat skeptical',
                'option_d' => 'Completely dismissive',
                'correct_answer' => 'C',
            ],
            [
                'question_text' => 'Why does the speaker mention the 1990s?',
                'option_a' => 'To give a historical example',
                'option_b' => 'To correct a common misconception',
                'option_c' => 'To introduce a new topic',
                'option_d' => 'To compare two time periods',
                'correct_answer' => 'A',
            ],
            [
                'question_text' => 'What does the man mean when he says "That\'s news to me"?',
                'option_a' => 'He watches the news regularly',
                'option_b' => 'He was not previously aware of the information',
                'option_c' => 'He finds the information unbelievable',
                'option_d' => 'He already knew about it',
                'correct_answer' => 'B',
            ],
        ];

        foreach ($listeningQuestions as $i => $q) {
            Question::create(array_merge($q, [
                'section_id' => $listening->id,
                'order' => $i + 1,
            ]));
        }

        // --- STRUCTURE QUESTIONS ---
        $structureQuestions = [
            [
                'question_text' => 'The committee _______ the proposal before the deadline.',
                'option_a' => 'have reviewed',
                'option_b' => 'has reviewed',
                'option_c' => 'reviewing',
                'option_d' => 'were reviewed',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => '_______ the heavy rain, the outdoor event was canceled.',
                'option_a' => 'Because of',
                'option_b' => 'In spite of',
                'option_c' => 'Instead of',
                'option_d' => 'Apart from',
                'correct_answer' => 'A',
            ],
            [
                'question_text' => 'Neither the students nor the teacher _______ aware of the change.',
                'option_a' => 'are',
                'option_b' => 'were',
                'option_c' => 'was',
                'option_d' => 'been',
                'correct_answer' => 'C',
            ],
            [
                'question_text' => 'The research paper, _______ was published last year, received wide acclaim.',
                'option_a' => 'that',
                'option_b' => 'who',
                'option_c' => 'which',
                'option_d' => 'whom',
                'correct_answer' => 'C',
            ],
            [
                'question_text' => 'Had I known about the meeting, I _______ attended it.',
                'option_a' => 'will have',
                'option_b' => 'would have',
                'option_c' => 'should',
                'option_d' => 'could',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'The scientist insisted that the experiment _______ repeated.',
                'option_a' => 'is',
                'option_b' => 'was',
                'option_c' => 'be',
                'option_d' => 'being',
                'correct_answer' => 'C',
            ],
            [
                'question_text' => 'Not only _______ the exam, but she also got the highest score.',
                'option_a' => 'she passed',
                'option_b' => 'did she pass',
                'option_c' => 'she did pass',
                'option_d' => 'passed she',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'The museum, along with its gift shops, _______ open on weekends.',
                'option_a' => 'are',
                'option_b' => 'is',
                'option_c' => 'were',
                'option_d' => 'have been',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => '_______ in 1905, the theory of relativity changed physics forever.',
                'option_a' => 'Publishing',
                'option_b' => 'Published',
                'option_c' => 'Having published',
                'option_d' => 'To publish',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'It is essential that every student _______ the guidelines carefully.',
                'option_a' => 'reads',
                'option_b' => 'read',
                'option_c' => 'reading',
                'option_d' => 'has read',
                'correct_answer' => 'B',
            ],
        ];

        foreach ($structureQuestions as $i => $q) {
            Question::create(array_merge($q, [
                'section_id' => $structure->id,
                'order' => $i + 1,
            ]));
        }

        // --- READING PASSAGE & QUESTIONS ---
        $readingPassage = Passage::create([
            'section_id' => $reading->id,
            'title' => 'The History of Photography',
            'content' => "Photography, as we know it today, is the result of several discoveries in chemistry and optics that took place over centuries. The camera obscura, a device that projects an image of its surroundings onto a screen, was known to ancient civilizations, but it was not until the 19th century that a way was found to preserve the projected image permanently.\n\nIn 1826, Joseph Nicéphore Niépce succeeded in capturing the first permanent photograph using a process he called heliography. The exposure time required was approximately eight hours, which made it impractical for most purposes. His partner, Louis Daguerre, later refined the process and introduced the daguerreotype in 1839, which reduced exposure time to minutes and produced remarkably detailed images.\n\nThe invention of the daguerreotype sparked a revolution in visual culture. For the first time, ordinary people could afford to have their likenesses captured, a privilege that had previously been reserved for the wealthy who could commission painted portraits. Photography studios sprang up in cities across Europe and North America, and the medium quickly became an essential tool for documentation, journalism, and artistic expression.\n\nThroughout the latter half of the 19th century, numerous innovations improved the photographic process. The wet collodion process, introduced in 1851, allowed for much shorter exposure times and could produce both positives and negatives. George Eastman's introduction of roll film in 1888 and the affordable Kodak camera made photography accessible to amateurs for the first time, fundamentally democratizing the medium.\n\nThe 20th century brought further revolutionary changes with the development of color photography, instant film by Polaroid, and eventually digital photography. Today, with cameras built into nearly every smartphone, photography has become perhaps the most ubiquitous form of visual communication in human history.",
            'order' => 1,
        ]);

        $readingQuestions = [
            [
                'question_text' => 'What is the main topic of the passage?',
                'option_a' => 'The chemistry behind photographic film',
                'option_b' => 'The development and evolution of photography',
                'option_c' => 'The life of Joseph Nicéphore Niépce',
                'option_d' => 'Modern digital camera technology',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'According to the passage, what was the camera obscura?',
                'option_a' => 'An early type of photograph',
                'option_b' => 'A chemical process for developing film',
                'option_c' => 'A device that projected images onto a screen',
                'option_d' => 'A painting technique used by artists',
                'correct_answer' => 'C',
            ],
            [
                'question_text' => 'What was the main limitation of Niépce\'s heliography?',
                'option_a' => 'The images were not permanent',
                'option_b' => 'The process was too expensive',
                'option_c' => 'It required approximately eight hours of exposure',
                'option_d' => 'It could only capture landscapes',
                'correct_answer' => 'C',
            ],
            [
                'question_text' => 'The word "privilege" in paragraph 3 is closest in meaning to:',
                'option_a' => 'obligation',
                'option_b' => 'advantage',
                'option_c' => 'responsibility',
                'option_d' => 'challenge',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'What can be inferred about painted portraits before photography?',
                'option_a' => 'They were more accurate than photographs',
                'option_b' => 'They were only available to wealthy individuals',
                'option_c' => 'They were not considered art',
                'option_d' => 'They were commonly available to everyone',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'According to the passage, when was roll film introduced?',
                'option_a' => '1826',
                'option_b' => '1839',
                'option_c' => '1851',
                'option_d' => '1888',
                'correct_answer' => 'D',
            ],
            [
                'question_text' => 'The word "democratizing" in paragraph 4 most likely means:',
                'option_a' => 'making something political',
                'option_b' => 'making something available to everyone',
                'option_c' => 'making something more expensive',
                'option_d' => 'making something more complex',
                'correct_answer' => 'B',
            ],
            [
                'question_text' => 'What does the passage suggest about the wet collodion process?',
                'option_a' => 'It was invented by George Eastman',
                'option_b' => 'It was introduced before the daguerreotype',
                'option_c' => 'It improved upon earlier photographic methods',
                'option_d' => 'It was the first color photography process',
                'correct_answer' => 'C',
            ],
            [
                'question_text' => 'Which of the following is NOT mentioned in the passage as a 20th century development?',
                'option_a' => 'Color photography',
                'option_b' => 'Instant film',
                'option_c' => 'Digital photography',
                'option_d' => 'X-ray photography',
                'correct_answer' => 'D',
            ],
            [
                'question_text' => 'The author\'s purpose in writing this passage is primarily to:',
                'option_a' => 'Argue that digital photography is superior to film',
                'option_b' => 'Provide a chronological overview of photography\'s development',
                'option_c' => 'Persuade readers to take up photography as a hobby',
                'option_d' => 'Compare different types of cameras',
                'correct_answer' => 'B',
            ],
        ];

        foreach ($readingQuestions as $i => $q) {
            Question::create(array_merge($q, [
                'section_id' => $reading->id,
                'passage_id' => $readingPassage->id,
                'order' => $i + 1,
            ]));
        }

        // Create Test Session
        $testSession = TestSession::create([
            'title' => 'TOEFL Practice Test #1',
            'description' => 'Complete practice test with all three sections: Listening, Structure, and Reading.',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addYear(),
        ]);

        $testSession->sections()->attach([$listening->id, $structure->id, $reading->id]);
    }
}
