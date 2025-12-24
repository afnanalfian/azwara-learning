<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Meeting;
use App\Models\QuestionCategory;
use App\Models\QuestionMaterial;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Exam;
use App\Models\Product;
use App\Models\PricingRule;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\UserEntitlement;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BimbelAfnanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data existing untuk menghindari duplikasi
        $this->truncateTables();

        // Jalankan seeder provinsi dan kabupaten (jika belum dijalankan)
        $this->call(ProvinceSeeder::class);
        $this->call(RegencySeeder::class);

        // 1. Buat Role dan Permission
        $this->createRolesAndPermissions();

        // 2. Buat User
        $users = $this->createUsers();

        // 3. Buat Course
        $courses = $this->createCourses();

        // 4. Assign Teacher ke Course
        $this->assignTeachersToCourses($users['tentors'], $courses);

        // 5. Buat Meeting
        $meetings = $this->createMeetings($courses);

        // 6. Buat Kategori dan Soal
        $questions = $this->createQuestionsAndCategories();

        // 7. Buat Exam (Tryout dan Quiz)
        $exams = $this->createExams($questions);

        // 8. Buat Products
        $products = $this->createProducts($courses, $meetings, $exams);

        // 9. Buat Pricing Rules
        $this->createPricingRules();

        // 10. Buat Discounts
        $discounts = $this->createDiscounts();

        // 11. Buat Transaksi
        $this->createTransactions($users['students'], $products, $courses, $meetings, $exams, $discounts);

        // 12. Buat Quiz Attempts
        $this->createQuizAttempts($users['students'], $exams['quiz']);

        $this->command->info('Seeder berhasil dijalankan! Data dummy Bimbel Afnan telah dibuat.');
    }

    private function truncateTables()
    {
        // Hanya truncate jika dalam environment testing/local
        if (app()->environment(['local', 'production'])) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            $tables = [
                'users',
                'teachers',
                'courses',
                'meetings',
                'question_categories',
                'question_materials',
                'questions',
                'question_options',
                'exams',
                'exam_questions',
                'exam_attempts',
                'exam_answers',
                'products',
                'productables',
                'pricing_rules',
                'discounts',
                'discountables',
                'carts',
                'cart_items',
                'orders',
                'order_items',
                'payments',
                'user_entitlements',
                'order_discounts',
                'user_discounts',
                'product_bonuses',
                'course_teacher',
                'model_has_roles',
                'model_has_permissions',
                'role_has_permissions',
                'roles',
                'permissions',
            ];

            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    private function createRolesAndPermissions()
    {
        // Buat role
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $studentRole = Role::create(['name' => 'siswa', 'guard_name' => 'web']);
        $teacherRole = Role::create(['name' => 'tentor', 'guard_name' => 'web']);

        // Buat permission dasar
        $permissions = [
            'view dashboard',
            'manage courses',
            'manage meetings',
            'manage questions',
            'manage exams',
            'manage users',
            'view reports',
            'purchase products',
            'take exams',
            'view materials',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign permission ke role
        $adminRole->givePermissionTo($permissions);
        $teacherRole->givePermissionTo(['view dashboard', 'manage meetings', 'view materials', 'view reports']);
        $studentRole->givePermissionTo(['purchase products', 'take exams', 'view materials']);
    }

    private function createUsers()
    {
        $users = [
            'admin' => null,
            'students' => [],
            'tentors' => []
        ];

        // 1. Admin
        $admin = User::create([
            'name' => 'Admin Bimbel',
            'email' => 'admin@bimbel.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'avatar' => null,
            'is_active' => true,
            'province_id' => 73, // Jawa Barat
            'regency_id' => 7309, // Kota Bandung
        ]);
        $admin->assignRole('admin');
        $users['admin'] = $admin;

        // 2. Siswa (5 orang)
        $studentNames = [
            ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@example.com'],
            ['name' => 'Siti Nurhaliza', 'email' => 'siti@example.com'],
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com'],
            ['name' => 'Dewi Anggraini', 'email' => 'dewi@example.com'],
            ['name' => 'Rizky Ramadhan', 'email' => 'rizky@example.com'],
        ];

        foreach ($studentNames as $index => $student) {
            $user = User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone' => '0812' . rand(1000000, 9999999),
                'avatar' => null,
                'is_active' => true,
                'province_id' => 73,
                'regency_id' => 7309,
            ]);
            $user->assignRole('siswa');
            $users['students'][] = $user;
        }

        // 3. Tentor (2 orang)
        $teacherNames = [
            ['name' => 'Dr. Andi Wijaya, M.Pd', 'email' => 'andi@tentor.com'],
            ['name' => 'Maya Sari, S.Si', 'email' => 'maya@tentor.com'],
        ];

        foreach ($teacherNames as $index => $teacher) {
            $user = User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone' => '0813' . rand(1000000, 9999999),
                'avatar' => null,
                'is_active' => true,
                'province_id' => 6, // Jawa Barat
                'regency_id' => 67, // Kota Bandung
            ]);
            $user->assignRole('tentor');

            // Buat record di tabel teachers
            $teacherRecord = Teacher::create([
                'user_id' => $user->id,
                'bio' => 'Tentor berpengalaman dengan pengajaran lebih dari 5 tahun.',
            ]);

            $users['tentors'][] = $user;
        }

        return $users;
    }

    private function createCourses()
    {
        $courses = [];

        $courseData = [
            [
                'name' => 'Matematika',
                'slug' => 'matematika',
                'thumbnail' => null,
                'description' => 'Kursus matematika lengkap untuk SMA, mencakup aljabar, kalkulus, dan statistika.',
            ],
            [
                'name' => 'IPA',
                'slug' => 'ipa',
                'thumbnail' => null,
                'description' => 'Kursus IPA terpadu mencakup Fisika, Kimia, dan Biologi untuk persiapan ujian.',
            ],
        ];

        foreach ($courseData as $data) {
            $courses[$data['name']] = Course::create($data);
        }

        return $courses;
    }

    private function assignTeachersToCourses($tentors, $courses)
    {
        // Assign tentor pertama ke Matematika
        DB::table('course_teacher')->insert([
            'course_id' => $courses['Matematika']->id,
            'teacher_id' => Teacher::where('user_id', $tentors[0]->id)->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign tentor kedua ke IPA
        DB::table('course_teacher')->insert([
            'course_id' => $courses['IPA']->id,
            'teacher_id' => Teacher::where('user_id', $tentors[1]->id)->first()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createMeetings($courses)
    {
        $meetings = [
            'Matematika' => [],
            'IPA' => []
        ];

        $mathMeetings = [
            [
                'title' => 'Persamaan Kuadrat',
                'slug' => 'persamaan-kuadrat',
                'scheduled_at' => now()->addDays(2)->setTime(10, 0),
            ],
            [
                'title' => 'Logaritma',
                'slug' => 'logaritma',
                'scheduled_at' => now()->addDays(5)->setTime(10, 0),
            ],
            [
                'title' => 'Akar Pangkat',
                'slug' => 'akar-pangkat',
                'scheduled_at' => now()->addDays(9)->setTime(10, 0),
            ],
        ];

        $scienceMeetings = [
            [
                'title' => 'Hukum Newton',
                'slug' => 'hukum-newton',
                'scheduled_at' => now()->addDays(3)->setTime(14, 0),
            ],
            [
                'title' => 'Asas Black',
                'slug' => 'asas-black',
                'scheduled_at' => now()->addDays(6)->setTime(14, 0),
            ],
            [
                'title' => 'Tumbuhan',
                'slug' => 'tumbuhan',
                'scheduled_at' => now()->addDays(10)->setTime(14, 0),
            ],
        ];

        foreach ($mathMeetings as $meetingData) {
            $meeting = Meeting::create(array_merge($meetingData, [
                'course_id' => $courses['Matematika']->id,
                'status' => 'upcoming',
                'zoom_link' => 'https://zoom.us/j/1234567890',
                'created_by' => User::where('email', 'andi@tentor.com')->first()->id,
            ]));
            $meetings['Matematika'][] = $meeting;
        }

        foreach ($scienceMeetings as $meetingData) {
            $meeting = Meeting::create(array_merge($meetingData, [
                'course_id' => $courses['IPA']->id,
                'status' => 'upcoming',
                'zoom_link' => 'https://zoom.us/j/0987654321',
                'created_by' => User::where('email', 'maya@tentor.com')->first()->id,
            ]));
            $meetings['IPA'][] = $meeting;
        }

        return $meetings;
    }

    private function createQuestionsAndCategories()
    {
        $questions = [
            'Matematika' => [],
            'IPA' => []
        ];

        // 1. Buat Kategori
        $mathCategory = QuestionCategory::create([
            'name' => 'Matematika',
            'slug' => 'matematika',
            'description' => 'Soal-soal matematika untuk SMA',
        ]);

        $scienceCategory = QuestionCategory::create([
            'name' => 'IPA',
            'slug' => 'ipa',
            'description' => 'Soal-soal IPA terpadu',
        ]);

        // 2. Buat Material dan Soal Matematika
        $mathMaterials = [
            [
                'name' => 'Persamaan Kuadrat',
                'slug' => 'persamaan-kuadrat-math',
                'questions' => [
                    [
                        'type' => 'mcq',
                        'question_text' => 'Akar-akar persamaan kuadrat \(x^2 - 5x + 6 = 0\) adalah:',
                        'explanation' => 'Dengan menggunakan rumus faktorisasi: \(x^2 - 5x + 6 = (x-2)(x-3) = 0\), maka akar-akarnya adalah x = 2 dan x = 3.',
                        'options' => [
                            ['text' => '2 dan 3', 'correct' => true],
                            ['text' => '1 dan 6', 'correct' => false],
                            ['text' => '-2 dan -3', 'correct' => false],
                            ['text' => '2 dan -3', 'correct' => false],
                        ]
                    ],
                    [
                        'type' => 'mcma',
                        'question_text' => 'Manakah pernyataan yang benar tentang persamaan kuadrat \(ax^2 + bx + c = 0\)?',
                        'explanation' => 'Diskriminan menentukan jenis akar: D > 0 (2 akar real berbeda), D = 0 (1 akar real), D < 0 (tidak ada akar real). Jumlah akar = -b/a, hasil kali akar = c/a.',
                        'options' => [
                            ['text' => 'Diskriminan = \(b^2 - 4ac\)', 'correct' => true],
                            ['text' => 'Jumlah akar = \(-\frac{b}{a}\)', 'correct' => true],
                            ['text' => 'Hasil kali akar = \(\frac{c}{a}\)', 'correct' => true],
                            ['text' => 'Grafiknya selalu berbentuk parabola', 'correct' => true],
                        ]
                    ],
                    [
                        'type' => 'truefalse',
                        'question_text' => 'Persamaan kuadrat \(x^2 + 4x + 5 = 0\) memiliki akar real.',
                        'explanation' => 'Diskriminan D = \(4^2 - 4(1)(5) = 16 - 20 = -4 < 0\), sehingga tidak memiliki akar real.',
                        'options' => [
                            ['text' => 'Benar', 'correct' => false],
                            ['text' => 'Salah', 'correct' => true],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Logaritma',
                'slug' => 'logaritma-math',
                'questions' => [
                    [
                        'type' => 'mcq',
                        'question_text' => 'Nilai dari \(^2\log 8 + ^2\log 4\) adalah:',
                        'explanation' => '\(^2\log 8 = 3\) karena \(2^3 = 8\), \(^2\log 4 = 2\) karena \(2^2 = 4\). Maka 3 + 2 = 5.',
                        'options' => [
                            ['text' => '5', 'correct' => true],
                            ['text' => '6', 'correct' => false],
                            ['text' => '4', 'correct' => false],
                            ['text' => '3', 'correct' => false],
                        ]
                    ],
                    [
                        'type' => 'mcma',
                        'question_text' => 'Sifat-sifat logaritma yang benar adalah:',
                        'explanation' => 'Sifat logaritma: \(^a\log b + ^a\log c = ^a\log(bc)\), \(^a\log b - ^a\log c = ^a\log(b/c)\), \(^a\log b^n = n \cdot ^a\log b\).',
                        'options' => [
                            ['text' => '\(^a\log b + ^a\log c = ^a\log(bc)\)', 'correct' => true],
                            ['text' => '\(^a\log b^n = n \cdot ^a\log b\)', 'correct' => true],
                            ['text' => '\(^a\log 1 = 0\)', 'correct' => true],
                            ['text' => '\(^a\log a = 2\)', 'correct' => false],
                        ]
                    ],
                    [
                        'type' => 'truefalse',
                        'question_text' => '\(^3\log 27 = 4\)',
                        'explanation' => '\(3^3 = 27\), sehingga \(^3\log 27 = 3\), bukan 4.',
                        'options' => [
                            ['text' => 'Benar', 'correct' => false],
                            ['text' => 'Salah', 'correct' => true],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Akar Pangkat',
                'slug' => 'akar-pangkat-math',
                'questions' => [
                    [
                        'type' => 'mcq',
                        'question_text' => 'Nilai dari \(\sqrt{64} + \sqrt[3]{27}\) adalah:',
                        'explanation' => '\(\sqrt{64} = 8\), \(\sqrt[3]{27} = 3\), maka 8 + 3 = 11.',
                        'options' => [
                            ['text' => '11', 'correct' => true],
                            ['text' => '10', 'correct' => false],
                            ['text' => '9', 'correct' => false],
                            ['text' => '12', 'correct' => false],
                        ]
                    ],
                    [
                        'type' => 'mcma',
                        'question_text' => 'Manakah pernyataan yang benar tentang akar pangkat?',
                        'explanation' => '\(\sqrt{a^2} = |a|\), \(\sqrt{ab} = \sqrt{a} \cdot \sqrt{b}\), \(\sqrt{\frac{a}{b}} = \frac{\sqrt{a}}{\sqrt{b}}\) untuk a,b ≥ 0.',
                        'options' => [
                            ['text' => '\(\sqrt{9} = 3\)', 'correct' => true],
                            ['text' => '\(\sqrt{25} = \pm 5\)', 'correct' => false],
                            ['text' => '\(\sqrt{16} = 4\)', 'correct' => true],
                            ['text' => '\(\sqrt{0} = 1\)', 'correct' => false],
                        ]
                    ],
                    [
                        'type' => 'truefalse',
                        'question_text' => '\(\sqrt{4} = \pm 2\)',
                        'explanation' => 'Akar kuadrat utama selalu non-negatif, sehingga \(\sqrt{4} = 2\). ±2 adalah solusi dari persamaan x² = 4.',
                        'options' => [
                            ['text' => 'Benar', 'correct' => false],
                            ['text' => 'Salah', 'correct' => true],
                        ]
                    ],
                ]
            ],
        ];

        // 3. Buat Material dan Soal IPA
        $scienceMaterials = [
            [
                'name' => 'Hukum Newton',
                'slug' => 'hukum-newton-science',
                'questions' => [
                    [
                        'type' => 'mcq',
                        'question_text' => 'Hukum I Newton menyatakan bahwa:',
                        'explanation' => 'Hukum I Newton (Hukum Inersia): "Setiap benda akan tetap dalam keadaan diam atau bergerak lurus beraturan, kecuali jika dipaksa untuk mengubah keadaan itu oleh gaya-gaya yang bekerja padanya."',
                        'options' => [
                            ['text' => 'Benda diam akan tetap diam kecuali ada gaya luar', 'correct' => true],
                            ['text' => 'F = m × a', 'correct' => false],
                            ['text' => 'Aksi = -Reaksi', 'correct' => false],
                            ['text' => 'Energi tidak dapat diciptakan atau dimusnahkan', 'correct' => false],
                        ]
                    ],
                    [
                        'type' => 'mcma',
                        'question_text' => 'Manakah contoh penerapan Hukum III Newton?',
                        'explanation' => 'Hukum III Newton: "Untuk setiap aksi, ada reaksi yang sama besar dan berlawanan arah." Contoh: roket mendorong gas ke belakang, gas mendorong roket ke depan.',
                        'options' => [
                            ['text' => 'Roket meluncur ke atas', 'correct' => true],
                            ['text' => 'Berenang di kolam renang', 'correct' => true],
                            ['text' => 'Mendorong tembok', 'correct' => true],
                            ['text' => 'Buah jatuh dari pohon', 'correct' => false],
                        ]
                    ],
                    [
                        'type' => 'truefalse',
                        'question_text' => 'Satuan gaya dalam SI adalah Newton (N)',
                        'explanation' => 'Benar. 1 Newton = 1 kg·m/s²',
                        'options' => [
                            ['text' => 'Benar', 'correct' => true],
                            ['text' => 'Salah', 'correct' => false],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Asas Black',
                'slug' => 'asas-black-science',
                'questions' => [
                    [
                        'type' => 'mcq',
                        'question_text' => 'Asas Black menyatakan bahwa:',
                        'explanation' => 'Asas Black: "Jumlah kalor yang dilepas oleh benda yang bersuhu lebih tinggi sama dengan jumlah kalor yang diterima oleh benda yang bersuhu lebih rendah."',
                        'options' => [
                            ['text' => 'Q lepas = Q terima', 'correct' => true],
                            ['text' => 'Energi tetap konstan', 'correct' => false],
                            ['text' => 'Tekanan berbanding terbalik dengan volume', 'correct' => false],
                            ['text' => 'Suhu berbanding lurus dengan kalor', 'correct' => false],
                        ]
                    ],
                    [
                        'type' => 'mcma',
                        'question_text' => 'Faktor-faktor yang mempengaruhi perpindahan kalor menurut Asas Black:',
                        'explanation' => 'Q = m × c × ΔT, dimana m = massa, c = kalor jenis, ΔT = perubahan suhu.',
                        'options' => [
                            ['text' => 'Massa benda', 'correct' => true],
                            ['text' => 'Kalor jenis', 'correct' => true],
                            ['text' => 'Perubahan suhu', 'correct' => true],
                            ['text' => 'Warna benda', 'correct' => false],
                        ]
                    ],
                    [
                        'type' => 'truefalse',
                        'question_text' => 'Kalor jenis air adalah 1 kal/g°C',
                        'explanation' => 'Benar. Kalor jenis air = 1 kal/g°C = 4184 J/kg°K',
                        'options' => [
                            ['text' => 'Benar', 'correct' => true],
                            ['text' => 'Salah', 'correct' => false],
                        ]
                    ],
                ]
            ],
            [
                'name' => 'Tumbuhan',
                'slug' => 'tumbuhan-science',
                'questions' => [
                    [
                        'type' => 'mcq',
                        'question_text' => 'Proses fotosintesis terjadi di:',
                        'explanation' => 'Fotosintesis terjadi di kloroplas, khususnya di membran tilakoid untuk reaksi terang dan stroma untuk reaksi gelap.',
                        'options' => [
                            ['text' => 'Kloroplas', 'correct' => true],
                            ['text' => 'Mitokondria', 'correct' => false],
                            ['text' => 'Inti sel', 'correct' => false],
                            ['text' => 'Ribosom', 'correct' => false],
                        ]
                    ],
                    [
                        'type' => 'mcma',
                        'question_text' => 'Faktor yang mempengaruhi laju fotosintesis:',
                        'explanation' => 'Faktor utama: intensitas cahaya, konsentrasi CO₂, suhu, ketersediaan air, dan klorofil.',
                        'options' => [
                            ['text' => 'Intensitas cahaya', 'correct' => true],
                            ['text' => 'Konsentrasi CO₂', 'correct' => true],
                            ['text' => 'Suhu', 'correct' => true],
                            ['text' => 'Kelembaban udara', 'correct' => false],
                        ]
                    ],
                    [
                        'type' => 'truefalse',
                        'question_text' => 'Tumbuhan monokotil memiliki biji berkeping satu',
                        'explanation' => 'Benar. Monokotil (monocotyledonae) memiliki biji dengan satu keping biji, contoh: padi, jagung, kelapa.',
                        'options' => [
                            ['text' => 'Benar', 'correct' => true],
                            ['text' => 'Salah', 'correct' => false],
                        ]
                    ],
                ]
            ],
        ];

        // Simpan soal matematika
        foreach ($mathMaterials as $materialData) {
            $material = QuestionMaterial::create([
                'category_id' => $mathCategory->id,
                'name' => $materialData['name'],
                'slug' => $materialData['slug'],
            ]);

            foreach ($materialData['questions'] as $qData) {
                $question = Question::create([
                    'material_id' => $material->id,
                    'type' => $qData['type'],
                    'question_text' => $qData['question_text'],
                    'explanation' => $qData['explanation'],
                    'image' => null,
                ]);

                $questions['Matematika'][] = $question;

                $order = 1;
                foreach ($qData['options'] as $optData) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optData['text'],
                        'is_correct' => $optData['correct'],
                        'order' => $order++,
                    ]);
                }
            }
        }

        // Simpan soal IPA
        foreach ($scienceMaterials as $materialData) {
            $material = QuestionMaterial::create([
                'category_id' => $scienceCategory->id,
                'name' => $materialData['name'],
                'slug' => $materialData['slug'],
            ]);

            foreach ($materialData['questions'] as $qData) {
                $question = Question::create([
                    'material_id' => $material->id,
                    'type' => $qData['type'],
                    'question_text' => $qData['question_text'],
                    'explanation' => $qData['explanation'],
                    'image' => null,
                ]);

                $questions['IPA'][] = $question;

                $order = 1;
                foreach ($qData['options'] as $optData) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optData['text'],
                        'is_correct' => $optData['correct'],
                        'order' => $order++,
                    ]);
                }
            }
        }

        return $questions;
    }

    private function createExams($questions)
    {
        $exams = [];

        // 1. Buat Tryout Matematika
        $tryoutMath = Exam::create([
            'type' => 'tryout',
            'title' => 'Tryout Matematika',
            'exam_date' => now()->addDays(7)->setTime(9, 0),
            'duration_minutes' => 90,
            'status' => 'active',
            'created_by' => User::where('email', 'admin@bimbel.com')->first()->id,
        ]);

        // 2. Buat Tryout IPA
        $tryoutScience = Exam::create([
            'type' => 'tryout',
            'title' => 'Tryout IPA',
            'exam_date' => now()->addDays(8)->setTime(9, 0),
            'duration_minutes' => 90,
            'status' => 'active',
            'created_by' => User::where('email', 'admin@bimbel.com')->first()->id,
        ]);

        // 3. Buat Quiz Harian
        $quiz = Exam::create([
            'type' => 'quiz',
            'title' => 'Quiz Harian #1',
            'exam_date' => now(),
            'duration_minutes' => 30,
            'status' => 'active',
            'created_by' => User::where('email', 'admin@bimbel.com')->first()->id,
        ]);

        // Ambil 5 soal random dari semua soal
        $allQuestions = array_merge($questions['Matematika'], $questions['IPA']);
        shuffle($allQuestions);
        $selectedQuestions = array_slice($allQuestions, 0, 5);

        // Assign soal ke quiz
        foreach ($selectedQuestions as $index => $question) {
            DB::table('exam_questions')->insert([
                'exam_id' => $quiz->id,
                'question_id' => $question->id,
                'order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $exams = [
            'tryout_math' => $tryoutMath,
            'tryout_science' => $tryoutScience,
            'quiz' => $quiz,
        ];

        return $exams;
    }

    private function createProducts($courses, $meetings, $exams)
    {
        $products = [];

        // 1. Course Packages
        $courseMathProduct = Product::create([
            'type' => 'course_package',
            'name' => 'Paket Kursus Matematika Lengkap',
            'description' => 'Paket lengkap kursus Matematika termasuk semua pertemuan dan materi',
            'is_active' => true,
        ]);

        DB::table('productables')->insert([
            'product_id' => $courseMathProduct->id,
            'productable_type' => Course::class,
            'productable_id' => $courses['Matematika']->id,
        ]);

        $courseScienceProduct = Product::create([
            'type' => 'course_package',
            'name' => 'Paket Kursus IPA Lengkap',
            'description' => 'Paket lengkap kursus IPA termasuk semua pertemuan dan materi',
            'is_active' => true,
        ]);

        DB::table('productables')->insert([
            'product_id' => $courseScienceProduct->id,
            'productable_type' => Course::class,
            'productable_id' => $courses['IPA']->id,
        ]);

        // 2. Individual Meetings (Matematika)
        foreach ($meetings['Matematika'] as $meeting) {
            $product = Product::create([
                'type' => 'meeting',
                'name' => 'Pertemuan: ' . $meeting->title,
                'description' => 'Akses pertemuan ' . $meeting->title,
                'is_active' => true,
            ]);

            DB::table('productables')->insert([
                'product_id' => $product->id,
                'productable_type' => Meeting::class,
                'productable_id' => $meeting->id,
            ]);

            $products['meetings'][$meeting->id] = $product;
        }

        // 3. Individual Meetings (IPA)
        foreach ($meetings['IPA'] as $meeting) {
            $product = Product::create([
                'type' => 'meeting',
                'name' => 'Pertemuan: ' . $meeting->title,
                'description' => 'Akses pertemuan ' . $meeting->title,
                'is_active' => true,
            ]);

            DB::table('productables')->insert([
                'product_id' => $product->id,
                'productable_type' => Meeting::class,
                'productable_id' => $meeting->id,
            ]);

            $products['meetings'][$meeting->id] = $product;
        }

        // 4. Tryouts
        $tryoutMathProduct = Product::create([
            'type' => 'tryout',
            'name' => 'Tryout Matematika',
            'description' => 'Tryout lengkap Matematika dengan durasi 90 menit',
            'is_active' => true,
        ]);

        DB::table('productables')->insert([
            'product_id' => $tryoutMathProduct->id,
            'productable_type' => Exam::class,
            'productable_id' => $exams['tryout_math']->id,
        ]);

        $tryoutScienceProduct = Product::create([
            'type' => 'tryout',
            'name' => 'Tryout IPA',
            'description' => 'Tryout lengkap IPA dengan durasi 90 menit',
            'is_active' => true,
        ]);

        DB::table('productables')->insert([
            'product_id' => $tryoutScienceProduct->id,
            'productable_type' => Exam::class,
            'productable_id' => $exams['tryout_science']->id,
        ]);

        // 5. Addon Quiz
        $quizProduct = Product::create([
            'type' => 'addon',
            'name' => 'Quiz Harian Tambahan',
            'description' => 'Akses quiz harian untuk latihan tambahan',
            'is_active' => true,
        ]);

        DB::table('productables')->insert([
            'product_id' => $quizProduct->id,
            'productable_type' => Exam::class,
            'productable_id' => $exams['quiz']->id,
        ]);

        // 6. Tambahkan bonus ke course packages
        // Bonus untuk Matematika: semua meeting matematika
        foreach ($meetings['Matematika'] as $meeting) {
            DB::table('product_bonuses')->insert([
                'product_id' => $courseMathProduct->id,
                'bonus_type' => 'meeting',
                'bonus_id' => $meeting->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Bonus untuk IPA: semua meeting IPA
        foreach ($meetings['IPA'] as $meeting) {
            DB::table('product_bonuses')->insert([
                'product_id' => $courseScienceProduct->id,
                'bonus_type' => 'meeting',
                'bonus_id' => $meeting->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return [
            'course_math' => $courseMathProduct,
            'course_science' => $courseScienceProduct,
            'tryout_math' => $tryoutMathProduct,
            'tryout_science' => $tryoutScienceProduct,
            'quiz' => $quizProduct,
            'meetings' => $products['meetings'],
        ];
    }

    private function createPricingRules()
    {
        // 1. Course packages
        PricingRule::create([
            'product_type' => 'course_package',
            'min_qty' => 1,
            'max_qty' => null,
            'fixed_price' => 350000,
            'is_active' => true,
        ]);

        // 2. Meeting pricing tiers
        PricingRule::create([
            'product_type' => 'meeting',
            'min_qty' => 1,
            'max_qty' => 2,
            'price_per_unit' => 49000,
            'is_active' => true,
        ]);

        PricingRule::create([
            'product_type' => 'meeting',
            'min_qty' => 3,
            'max_qty' => 4,
            'price_per_unit' => 29000,
            'is_active' => true,
        ]);

        PricingRule::create([
            'product_type' => 'meeting',
            'min_qty' => 5,
            'max_qty' => null,
            'price_per_unit' => 19000,
            'is_active' => true,
        ]);

        // 3. Tryout
        PricingRule::create([
            'product_type' => 'tryout',
            'min_qty' => 1,
            'max_qty' => null,
            'price_per_unit' => 25000,
            'is_active' => true,
        ]);

        // 4. Addon
        PricingRule::create([
            'product_type' => 'addon',
            'min_qty' => 1,
            'max_qty' => null,
            'price_per_unit' => 39000,
            'is_active' => true,
        ]);
    }

    private function createDiscounts()
    {
        $discounts = [];

        // 1. Discount 10% untuk pembelian pertama
        $discount1 = Discount::create([
            'name' => 'Welcome Discount',
            'code' => 'WELCOME10',
            'type' => 'percentage',
            'value' => 10,
            'max_discount' => 50000,
            'min_order_amount' => 100000,
            'quota' => 100,
            'used' => 0,
            'starts_at' => now()->subDays(30),
            'ends_at' => now()->addDays(30),
            'is_active' => true,
        ]);

        // 2. Discount flat Rp 25.000 untuk tryout
        $discount2 = Discount::create([
            'name' => 'Tryout Special',
            'code' => 'TRYOUT25',
            'type' => 'fixed',
            'value' => 25000,
            'max_discount' => 25000,
            'min_order_amount' => 0,
            'quota' => 50,
            'used' => 0,
            'starts_at' => now(),
            'ends_at' => now()->addDays(60),
            'is_active' => true,
        ]);

        // 3. Discount 15% untuk course package
        $discount3 = Discount::create([
            'name' => 'Course Bundle',
            'code' => 'COURSE15',
            'type' => 'percentage',
            'value' => 15,
            'max_discount' => 75000,
            'min_order_amount' => 300000,
            'quota' => 30,
            'used' => 0,
            'starts_at' => now(),
            'ends_at' => now()->addDays(90),
            'is_active' => true,
        ]);

        // 4. Discount tanpa kode (automatic) untuk meeting bundle
        $discount4 = Discount::create([
            'name' => 'Meeting Bundle Discount',
            'code' => null,
            'type' => 'percentage',
            'value' => 20,
            'max_discount' => 100000,
            'min_order_amount' => 100000,
            'quota' => null,
            'used' => 0,
            'starts_at' => now()->subDays(7),
            'ends_at' => now()->addDays(180),
            'is_active' => true,
        ]);

        return [
            'welcome' => $discount1,
            'tryout' => $discount2,
            'course' => $discount3,
            'meeting_bundle' => $discount4,
        ];
    }

    private function createTransactions($students, $products, $courses, $meetings, $exams, $discounts)
    {
        // Hari 1: Tanggal 3 hari yang lalu
        $day1 = now()->subDays(3);

        // 1. Siswa pertama beli course_package Matematika
        $this->createOrder(
            $students[0],
            $day1,
            [
                ['product' => $products['course_math'], 'qty' => 1, 'price' => 350000]
            ],
            $discounts['course'],
            52500, // 15% dari 350000 = 52500 (tapi max discount 75000)
            'Siswa 1 beli course matematika'
        );

        // 2. Siswa kedua beli tryout IPA plus addon quiz
        $this->createOrder(
            $students[1],
            $day1,
            [
                ['product' => $products['tryout_science'], 'qty' => 1, 'price' => 25000],
                ['product' => $products['quiz'], 'qty' => 1, 'price' => 39000]
            ],
            $discounts['tryout'],
            25000, // Discount flat 25000 untuk tryout
            'Siswa 2 beli tryout IPA + quiz'
        );

        // Hari 2: Tanggal 2 hari yang lalu
        $day2 = now()->subDays(2);

        // 3. Siswa ketiga beli pertemuan 1 dan 3 di course IPA dengan addon quiz
        $ipaMeetings = $meetings['IPA'];
        $this->createOrder(
            $students[2],
            $day2,
            [
                ['product' => $products['meetings'][$ipaMeetings[0]->id], 'qty' => 1, 'price' => 49000],
                ['product' => $products['meetings'][$ipaMeetings[2]->id], 'qty' => 1, 'price' => 49000],
                ['product' => $products['quiz'], 'qty' => 1, 'price' => 39000]
            ],
            $discounts['meeting_bundle'],
            27400, // 20% dari 137000 = 27400
            'Siswa 3 beli 2 meeting IPA + quiz'
        );

        // Hari 3: Tanggal kemarin
        $day3 = now()->subDays(1);

        // 4. Siswa keempat beli course matematika
        $this->createOrder(
            $students[3],
            $day3,
            [
                ['product' => $products['course_math'], 'qty' => 1, 'price' => 350000]
            ],
            null,
            0,
            'Siswa 4 beli course matematika'
        );

        // 5. Siswa kelima beli course IPA
        $this->createOrder(
            $students[4],
            $day3,
            [
                ['product' => $products['course_science'], 'qty' => 1, 'price' => 350000]
            ],
            $discounts['course'],
            52500, // 15% dari 350000 = 52500
            'Siswa 5 beli course IPA'
        );
    }

    private function createOrder($student, $date, $items, $discount = null, $discountAmount = 0, $description = '')
    {
        // Hitung total amount
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['qty'];
        }
        $totalAfterDiscount = $total - $discountAmount;

        // Buat order
        $order = Order::create([
            'user_id' => $student->id,
            'total_amount' => $totalAfterDiscount,
            'status' => 'verified',
            'expires_at' => $date->copy()->addDays(2),
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        // Buat order items
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'qty' => $item['qty'],
                'price' => $item['price'],
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Berikan entitlement berdasarkan produk
            $this->createEntitlement($student, $item['product'], $date);
        }

        // Buat payment
        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => 'midtrans',
            'proof_image' => null,
            'paid_at' => $date->copy()->addHours(1),
            'verified_at' => $date->copy()->addHours(2),
            'verified_by' => User::where('email', 'admin@bimbel.com')->first()->id,
            'status' => 'verified',
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        // Tambahkan discount jika ada
        if ($discount) {
            DB::table('order_discounts')->insert([
                'order_id' => $order->id,
                'discount_id' => $discount->id,
                'amount' => $discountAmount,
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Update used count di discount
            $discount->increment('used');
        }

        return $order;
    }

    private function createEntitlement($student, $product, $date)
    {
        // Ambil productable untuk menentukan entitlement type
        $productable = DB::table('productables')
            ->where('product_id', $product->id)
            ->first();

        if (!$productable) return;

        $entitlementType = '';
        $entitlementId = 0;

        // Tentukan entitlement type berdasarkan productable_type
        if ($productable->productable_type === 'App\Models\Course') {
            $entitlementType = 'course';
            $entitlementId = $productable->productable_id;
        } elseif ($productable->productable_type === 'App\Models\Meeting') {
            $entitlementType = 'meeting';
            $entitlementId = $productable->productable_id;
        } elseif ($productable->productable_type === 'App\Models\Exam') {
            $exam = Exam::find($productable->productable_id);
            if ($exam) {
                switch ($exam->type) {
                    case 'tryout':
                        $entitlementType = 'tryout';
                        break;
                    case 'quiz':
                        $entitlementType = 'quiz';
                        break;
                    default:
                        return;
                }
                $entitlementId = $exam->id;
            }
        }

        if ($entitlementType) {
            UserEntitlement::create([
                'user_id' => $student->id,
                'entitlement_type' => $entitlementType,
                'entitlement_id' => $entitlementId,
                'source' => 'purchase',
                'expires_at' => $date->copy()->addYear(),
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }

    private function createQuizAttempts($students, $quiz)
    {
        // Ambil semua soal dari quiz
        $quizQuestions = DB::table('exam_questions')
            ->where('exam_id', $quiz->id)
            ->orderBy('order')
            ->get();

        if ($quizQuestions->isEmpty()) return;

        // Untuk setiap siswa, buat attempt dan answer
        foreach ($students as $student) {
            // Buat exam attempt
            $attempt = ExamAttempt::create([
                'exam_id' => $quiz->id,
                'user_id' => $student->id,
                'started_at' => now()->subHours(2),
                'submitted_at' => now()->subHours(1),
                'duration_seconds' => 1800, // 30 menit
                'score' => rand(60, 95),
                'is_submitted' => true,
            ]);

            $correctAnswers = 0;
            $totalQuestions = count($quizQuestions);

            // Buat jawaban untuk setiap soal
            foreach ($quizQuestions as $examQuestion) {
                $question = Question::find($examQuestion->question_id);
                $correctOptions = QuestionOption::where('question_id', $question->id)
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->toArray();

                // Pilih jawaban random (untuk simulasi)
                $isCorrect = rand(0, 1); // 50% kemungkinan benar

                if ($isCorrect) {
                    $selectedOptions = $correctOptions;
                    $correctAnswers++;
                } else {
                    // Pilih opsi yang salah
                    $wrongOptions = QuestionOption::where('question_id', $question->id)
                        ->where('is_correct', false)
                        ->pluck('id')
                        ->toArray();
                    $selectedOptions = count($wrongOptions) > 0 ? [$wrongOptions[0]] : [];
                }

                ExamAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'selected_options' => json_encode($selectedOptions),
                    'is_correct' => $isCorrect,
                ]);
            }

            // Update score berdasarkan jawaban benar
            $score = ($correctAnswers / $totalQuestions) * 100;
            $attempt->update(['score' => $score]);
        }
    }
}
