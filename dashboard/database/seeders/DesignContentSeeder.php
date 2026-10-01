<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\AchievementMainType;
use App\Models\AchievementScope;
use App\Models\ImageMedia;
use App\Models\News;
use App\Models\Organization;
use App\Models\OrganizationType;
use App\Models\PublicationMedia;
use App\Models\PublicFigure;
use App\Models\SubAchievementType;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\VideoMedia;
use App\Settings\AboutSettings;
use App\Settings\GeneralSettings;
use App\Settings\HomeSettings;
use App\Settings\MediaSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Seeds the exact sample content shown in design/ so a fresh install renders
 * like the static design. See design/DESIGN.md section 10.9.
 *
 * Idempotent: every record is keyed on its slug via updateOrCreate.
 */
class DesignContentSeeder extends Seeder
{
    private string $photos = 'seed/photos';

    private string $logos = 'seed/logos';

    private string $hero = 'seed/hero';

    private string $about = 'seed/about';

    private string $documents = 'seed/documents';

    public function run(): void
    {
        $this->command?->info('📄 Seeding design content (design/DESIGN.md 10.9)...');

        $this->copyDesignImages();
        $this->seedPlaceholderDocuments();
        $this->updateMainTypes();

        $subTypes = $this->seedSubTypes();
        $figures = $this->seedPublicFigures();
        $organizations = $this->seedOrganizations();
        $this->seedAchievements($figures, $organizations, $subTypes);
        $this->seedNews($organizations);
        $this->seedTestimonials();
        $this->seedTeamMembers();
        $this->seedMedia($figures);
        $this->seedSettings();
    }

    /**
     * Copy design/assets/images/{photos,logos,hero,about/*.png} into the
     * public disk under seed/ (icons and brand assets stay in public/).
     */
    private function copyDesignImages(): void
    {
        $source = dirname(base_path()).'/design/assets/images';

        if (! File::isDirectory($source)) {
            $this->command?->warn("Design assets not found at {$source}; seed records will reference missing images.");

            return;
        }

        $copied = 0;

        foreach (['photos', 'logos', 'hero'] as $dir) {
            foreach (File::files("{$source}/{$dir}") as $file) {
                Storage::disk('public')->put("seed/{$dir}/".$file->getFilename(), File::get($file->getPathname()));
                $copied++;
            }
        }

        // Only the about photos (founder, team member) are seed data; icon-*.svg are static assets.
        foreach (File::files("{$source}/about") as $file) {
            if ($file->getExtension() !== 'png') {
                continue;
            }

            Storage::disk('public')->put("{$this->about}/".$file->getFilename(), File::get($file->getPathname()));
            $copied++;
        }

        $this->command?->info("  Copied {$copied} design images to storage/app/public/seed/ (run `php artisan storage:link`).");
    }

    /**
     * Minimal valid PDF placeholders so publication/profile download links work.
     */
    private function seedPlaceholderDocuments(): void
    {
        $pdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
            ."2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n"
            ."3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]>>endobj\n"
            ."trailer<</Root 1 0 R>>\n%%EOF";

        foreach (['publication-1.pdf', 'publication-2.pdf', 'profile.pdf'] as $name) {
            $path = "{$this->documents}/{$name}";

            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, $pdf);
            }
        }
    }

    /**
     * Icons and home rows for the main types seeded by AchievementMainTypeSeeder.
     * The design home page shows the rows الرياضة / الثقافة / الإبتكارات.
     */
    private function updateMainTypes(): void
    {
        $icons = [
            'sports' => 'sports',
            'culture' => 'culture',
            'science' => 'science',
            'military' => 'military',
            'medical' => 'medical',
            'innovations' => 'innovation',
            'literature' => 'literature',
            'students' => 'students',
            'arts' => 'arts',
            'entrepreneurship' => 'business',
        ];

        foreach ($icons as $slug => $icon) {
            AchievementMainType::query()->where('slug', $slug)->update([
                'icon_path' => "/images/icons/cat/{$icon}.svg",
            ]);
        }

        AchievementMainType::query()->whereIn('slug', ['sports', 'culture', 'innovations'])
            ->update(['show_on_home' => true]);
        AchievementMainType::query()->whereNotIn('slug', ['sports', 'culture', 'innovations'])
            ->update(['show_on_home' => false]);
    }

    /**
     * @return array<string, SubAchievementType>
     */
    private function seedSubTypes(): array
    {
        $mainTypeId = fn (string $slug) => AchievementMainType::query()->where('slug', $slug)->value('id');

        $definitions = [
            'football' => ['title_ar' => 'كرة القدم', 'title_en' => 'Football', 'main' => 'sports'],
            'basketball' => ['title_ar' => 'كرة السلة', 'title_en' => 'Basketball', 'main' => 'sports'],
            'adab' => ['title_ar' => 'الأدب', 'title_en' => 'Literature', 'main' => 'culture'],
            'inventions' => ['title_ar' => 'الاختراعات', 'title_en' => 'Inventions', 'main' => 'innovations'],
        ];

        $subTypes = [];

        foreach ($definitions as $slug => $data) {
            $subTypes[$slug] = SubAchievementType::updateOrCreate(
                ['slug' => $slug],
                [
                    'achievement_main_type_id' => $mainTypeId($data['main']),
                    'title_ar' => $data['title_ar'],
                    'title_en' => $data['title_en'],
                    'is_published' => true,
                    'sort_order' => count($subTypes) + 1,
                ]
            );
        }

        return $subTypes;
    }

    /**
     * @return array<string, PublicFigure>
     */
    private function seedPublicFigures(): array
    {
        $mainTypeId = fn (string $slug) => AchievementMainType::query()->where('slug', $slug)->value('id');

        $jokhaDescription = 'جوخة الحارثي كاتبة وأكاديمية عمانية. تعلمت في عمان والمملكة المتحدة. حصلت علي درجة الدكتوراه في الأدب العربي الكلاسيكي في جامعة إدنبرة. هي حاليًا أستاذ مشارك بقسم اللغة العربية بجامعة السلطان قابوس. وهي أول كاتبة أو كاتب عربي يفوز بجائزة المان بوكر العالمية. نشرت جوخة الحارثي ثلاث مجموعات من القصص القصيرة (مقاطع من سيرة لبنى إذ آن الرحيل، وصبي على السطح، وفي مديح الحب)، وأربع روايات (منامات - سيدات القمر - نارنجة - حرير الغزالة)......';

        $figures = [];

        $definitions = [
            'jokha-alharthi' => [
                'achievement_main_type_id' => $mainTypeId('culture'),
                'name_ar' => 'د. جوخة الحارثية',
                'name_en' => 'Dr. Jokha Al-Harithiya',
                'headline_ar' => 'أكاديمية بجامعة السلطان قابوس - كاتبة وروائية - فائزة بجائزة البوكر العالمية',
                'headline_en' => 'Associate Professor at Sultan Qaboos University - Novelist - Winner of the International Booker Prize',
                'description_ar' => $jokhaDescription,
                'description_en' => 'Jokha Alharthi is an Omani writer and academic, and the first Arabic-language writer to win the International Booker Prize.',
                'story_ar' => $jokhaDescription.' تُرجمت أعمالها إلى عدة لغات، وحازت روايتها «سيدات القمر» على جائزة مان بوكر الدولية عام 2019.',
                'image_path' => "{$this->photos}/tile-jokha.jpg",
                'tile_image_path' => "{$this->photos}/tile-jokha-clean.jpg",
                'cutout_image_path' => "{$this->photos}/jokha-hero.png",
                'banner_image_path' => "{$this->hero}/featured-banner.jpg",
                'is_featured' => true,
                'in_home_carousel' => false,
                'linkedin_url' => 'https://www.linkedin.com/in/alharthi2023',
                'twitter_url' => 'https://twitter.com/alharthi2023',
                'instagram_url' => 'https://instagram.com/alharthi2023',
                'email' => 'alharthi2023@gmail.com',
            ],
            'ali-alhabsi' => [
                'achievement_main_type_id' => $mainTypeId('sports'),
                'name_ar' => 'علي الحبسي',
                'name_en' => 'Ali Al-Habsi',
                'headline_ar' => 'حارس مرمى المنتخب العماني والمحترف السابق في الدوري الإنجليزي',
                'headline_en' => 'Goalkeeper of the Oman national team, formerly in the English Premier League',
                'description_ar' => 'علي الحبسي حارس مرمى عماني، من أبرز الأسماء الرياضية العمانية على المستوى الدولي، احترف في الدوري الإنجليزي الممتاز وحقق مع نادي ويغان أتلتيك لقب كأس الاتحاد الإنجليزي.',
                'image_path' => "{$this->photos}/carousel-1.jpg",
                'tile_image_path' => "{$this->photos}/tile-ali.jpg",
                'in_home_carousel' => true,
            ],
            'aisha-alsaifi' => [
                'achievement_main_type_id' => $mainTypeId('culture'),
                'name_ar' => 'عائشة السيفي',
                'name_en' => 'Aisha Al-Saifi',
                'headline_ar' => 'كاتبة وقاصة عمانية',
                'description_ar' => 'عائشة السيفي كاتبة وقاصة عمانية، صدرت لها مجموعات قصصية لاقت صدى واسعًا في المحافل الأدبية المحلية والعربية.',
                'image_path' => "{$this->photos}/carousel-4.jpg",
                'tile_image_path' => "{$this->photos}/tile-aisha.jpg",
                'in_home_carousel' => true,
            ],
            'maryam-albalushi' => [
                'achievement_main_type_id' => $mainTypeId('science'),
                'name_ar' => 'د. مريم البلوشية',
                'name_en' => 'Dr. Maryam Al-Balushi',
                'headline_ar' => 'منجزة في مجال العلوم',
                'description_ar' => 'د. مريم البلوشية باحثة عمانية في مجال العلوم، مثّلت سلطنة عمان في عدد من المحافل العلمية الدولية.',
                'image_path' => "{$this->photos}/carousel-2.jpg",
                'tile_image_path' => "{$this->photos}/carousel-2.jpg",
                'in_home_carousel' => true,
            ],
            'bashayer-alhinai' => [
                'achievement_main_type_id' => $mainTypeId('science'),
                'name_ar' => 'د. بشائر الهنائية',
                'name_en' => 'Dr. Bashayer Al-Hinai',
                'headline_ar' => 'باحثة عمانية',
                'description_ar' => 'د. بشائر الهنائية باحثة عمانية، حصدت أبحاثها جوائز وتكريمات في مؤتمرات علمية إقليمية ودولية.',
                'image_path' => "{$this->photos}/carousel-3.jpg",
                'tile_image_path' => "{$this->photos}/carousel-3.jpg",
                'in_home_carousel' => true,
            ],
            'khalid-alameri' => [
                'achievement_main_type_id' => $mainTypeId('medical'),
                'name_ar' => 'د. خالد العامري',
                'name_en' => 'Dr. Khalid Al-Amri',
                'headline_ar' => 'طبيب عماني',
                'description_ar' => 'د. خالد العامري طبيب عماني، قدّم إسهامات متميزة في تخصصه ورفع اسم سلطنة عمان في المحافل الطبية الدولية.',
                'image_path' => "{$this->photos}/carousel-5.jpg",
                'tile_image_path' => "{$this->photos}/carousel-5.jpg",
                'in_home_carousel' => true,
            ],
            'mohammed-almusallami' => [
                'achievement_main_type_id' => $mainTypeId('sports'),
                'name_ar' => 'محمد المسلمي',
                'name_en' => 'Mohammed Al-Musallami',
                'headline_ar' => 'لاعب المنتخب العماني لكرة القدم',
                'description_ar' => 'محمد المسلمي لاعب كرة قدم عماني، مثّل المنتخب الوطني في البطولات الآسيوية والخليجية.',
                'image_path' => "{$this->photos}/tile-ali.jpg",
                'tile_image_path' => "{$this->photos}/tile-ali.jpg",
            ],
            'eid-alfarsi' => [
                'achievement_main_type_id' => $mainTypeId('sports'),
                'name_ar' => 'عيد الفارسي',
                'name_en' => 'Eid Al-Farsi',
                'headline_ar' => 'لاعب المنتخب العماني لكرة القدم',
                'description_ar' => 'عيد الفارسي لاعب كرة قدم عماني، من ركائز المنتخب الوطني في الاستحقاقات القارية.',
                'image_path' => "{$this->photos}/tile-ali.jpg",
                'tile_image_path' => "{$this->photos}/tile-ali.jpg",
            ],
            'fayez-alrushaidi' => [
                'achievement_main_type_id' => $mainTypeId('sports'),
                'name_ar' => 'فايز الرشيدي',
                'name_en' => 'Fayez Al-Rushaidi',
                'headline_ar' => 'حارس مرمى المنتخب العماني لكرة القدم',
                'description_ar' => 'فايز الرشيدي حارس مرمى عماني، تألق مع المنتخب الوطني في البطولات الخليجية والآسيوية.',
                'image_path' => "{$this->photos}/tile-ali.jpg",
                'tile_image_path' => "{$this->photos}/tile-ali.jpg",
            ],
            'said-alsiqlawi' => [
                'achievement_main_type_id' => $mainTypeId('culture'),
                'name_ar' => 'سعيد الصقلاوي',
                'name_en' => 'Said Al-Siqlawi',
                'headline_ar' => 'شاعر عماني',
                'description_ar' => 'سعيد الصقلاوي شاعر عماني، حصدت قصائده جوائز أدبية في محافل شعرية عربية.',
                'image_path' => "{$this->photos}/small-poet.jpg",
                'tile_image_path' => "{$this->photos}/small-poet.jpg",
            ],
            'huda-hamed' => [
                'achievement_main_type_id' => $mainTypeId('culture'),
                'name_ar' => 'هدى حمد',
                'name_en' => 'Huda Hamed',
                'headline_ar' => 'روائية وكاتبة عمانية',
                'description_ar' => 'هدى حمد روائية وكاتبة عمانية، صدرت لها روايات ومجموعات قصصية ترجمت إلى لغات أخرى.',
                'image_path' => "{$this->photos}/small-aisha.jpg",
                'tile_image_path' => "{$this->photos}/small-aisha.jpg",
            ],
            'hamad-alkharousi' => [
                'achievement_main_type_id' => $mainTypeId('innovations'),
                'name_ar' => 'م. حمد الخروصي',
                'name_en' => 'Eng. Hamad Al-Kharousi',
                'headline_ar' => 'مخترع عماني',
                'description_ar' => 'المهندس حمد الخروصي مخترع عماني، مثّل سلطنة عمان في معارض الاختراعات الدولية وحصد ميداليات ذهبية وفضية.',
                'image_path' => "{$this->photos}/small-boy.jpg",
                'tile_image_path' => "{$this->photos}/small-boy.jpg",
            ],
            'muhannad-alkindi' => [
                'achievement_main_type_id' => $mainTypeId('innovations'),
                'name_ar' => 'م. مهند الكندي',
                'name_en' => 'Eng. Muhannad Al-Kindi',
                'headline_ar' => 'مبتكر عماني',
                'description_ar' => 'المهندس مهند الكندي مبتكر عماني، قدّم ابتكارات تقنية نالت تكريمات في محافل دولية.',
                'image_path' => "{$this->photos}/small-speaker.jpg",
                'tile_image_path' => "{$this->photos}/small-speaker.jpg",
            ],
            'alwaleed-alhinai' => [
                'achievement_main_type_id' => $mainTypeId('innovations'),
                'name_ar' => 'أ. الوليد الهنائي',
                'name_en' => 'Al-Waleed Al-Hinai',
                'headline_ar' => 'مبتكر عماني',
                'description_ar' => 'الوليد الهنائي مبتكر عماني، شارك بابتكاراته في معارض علمية إقليمية ودولية.',
                'image_path' => "{$this->photos}/small-boy.jpg",
                'tile_image_path' => "{$this->photos}/small-boy.jpg",
            ],
            'amal-alshukaili' => [
                'achievement_main_type_id' => $mainTypeId('innovations'),
                'name_ar' => 'م. أمل الشكيلية',
                'name_en' => 'Eng. Amal Al-Shukaili',
                'headline_ar' => 'مبتكرة عمانية',
                'description_ar' => 'المهندسة أمل الشكيلية مبتكرة عمانية، حصدت ابتكاراتها جوائز في معارض الاختراعات الدولية.',
                'image_path' => "{$this->photos}/small-aisha.jpg",
                'tile_image_path' => "{$this->photos}/small-aisha.jpg",
            ],
        ];

        $sort = 0;

        foreach ($definitions as $slug => $data) {
            $figures[$slug] = PublicFigure::updateOrCreate(
                ['slug' => $slug],
                $data + [
                    'is_featured' => false,
                    'in_home_carousel' => false,
                    'is_published' => true,
                    'sort_order' => ++$sort,
                ]
            );
        }

        return $figures;
    }

    /**
     * @return array<string, Organization>
     */
    private function seedOrganizations(): array
    {
        $typeId = fn (string $slug) => OrganizationType::query()->where('slug', $slug)->value('id');

        $ministryDescription = 'تعمل وزارة الثقافة والرياضة والشباب جاهدة على توفير كافة الخدمات والتسهيلات والمرافق التي تلبّي احتياجات القطاعات الثقافية والرياضية والشبابية، جنبًا إلى جنب مع تنفيذ البرامج والأنشطة المختلفة بما يتلاءم مع تطلعات الفئات المستهدفة وبما يلبي احتياجاتهم المتعددة والمتنوعة.'
            ."\n\n"
            .'ومنذ أن كانت الأندية حاضنة أولى للشباب لممارسة أنشطتهم الرياضية والثقافية والفنية والأدبية المختلفة، ونواة العمل الثقافي الرياضي والشبابي في السلطنة، توالت السياسات والخطط والبرامج في المجالات الثلاثة، لتجد اليوم ما يواكبها على نطاقٍ رقميّ يعمل على توفير مساحات للأفراد والمؤسسات والشركات والمجتمع المدني المرتبط بالإبداع والاقتصاد الثقافي وغيره؛ من خلال تشكيل مجتمعٍ رقميٍ متكاملٍ يوفّر فرصًا للأعمال، ويشارك في الأنشطة والفعاليات، ويعمل على إبراز المواهب والاستفادة من الخدمات.';

        $definitions = [
            'ministry-culture-sports-youth' => [
                'organization_type_id' => $typeId('government'),
                'name_ar' => 'وزارة الثقافة والرياضة والشباب',
                'name_en' => 'Ministry of Culture, Sports and Youth',
                'description_ar' => $ministryDescription,
                'logo_path' => "{$this->logos}/ministry-culture-sports-youth.jpg",
                'logo_white_path' => "{$this->logos}/ministry-culture-white.png",
                'website_url' => 'https://www.csy.gov.om',
                'manager_name_ar' => 'ذي يزن بن هيثم آل سعيد',
                'manager_name_en' => 'His Highness Sayyid Theyazin bin Haitham Al Said',
                'manager_message_ar' => "الشكر موصول لكل من ساهم في إخراج هذا العمل وأرشفة\nهذه الإنجازات النيرة لإيصالها للمجتمع المحلي والدولي\nبصورة تليق بحجم إنجازات شباب عمان",
                'manager_image_path' => "{$this->photos}/minister-portrait.png",
            ],
            'ministry-heritage-tourism' => [
                'organization_type_id' => $typeId('government'),
                'name_ar' => 'وزارة التراث والسياحة',
                'name_en' => 'Ministry of Heritage and Tourism',
                'description_ar' => 'تعمل وزارة التراث والسياحة على صون التراث العماني وتطوير القطاع السياحي، ودعم المبدعين والمتميزين في المجالات التراثية والسياحية.',
                'logo_path' => "{$this->logos}/ministry-heritage-tourism.jpg",
            ],
            'oman-air' => [
                'organization_type_id' => $typeId('government'),
                'name_ar' => 'الطيران العماني',
                'name_en' => 'Oman Air',
                'description_ar' => 'الطيران العماني هو الناقل الوطني لسلطنة عمان، ويحمل اسم عمان إلى مطارات العالم دعمًا لإنجازات أبنائها في المحافل الدولية.',
                'logo_path' => "{$this->logos}/oman-air.jpg",
            ],
            'al-wisal' => [
                'organization_type_id' => $typeId('private'),
                'name_ar' => 'الوصال',
                'name_en' => 'Al-Wisal',
                'description_ar' => 'إذاعة الوصال إذاعة عمانية خاصة، تواكب إنجازات الشباب العماني وتسلط الضوء عليها عبر برامجها المختلفة.',
                'logo_path' => "{$this->logos}/al-wisal.jpg",
            ],
        ];

        $organizations = [];
        $sort = 0;

        foreach ($definitions as $slug => $data) {
            $organizations[$slug] = Organization::updateOrCreate(
                ['slug' => $slug],
                $data + [
                    'is_published' => true,
                    'sort_order' => ++$sort,
                ]
            );
        }

        return $organizations;
    }

    /**
     * @param  array<string, PublicFigure>  $figures
     * @param  array<string, Organization>  $organizations
     * @param  array<string, SubAchievementType>  $subTypes
     */
    private function seedAchievements(array $figures, array $organizations, array $subTypes): void
    {
        $scopeId = fn (string $slug) => AchievementScope::query()->where('slug', $slug)->value('id');
        $mainTypeId = fn (string $slug) => AchievementMainType::query()->where('slug', $slug)->value('id');

        // Card excerpt used across the design's achievement cards.
        $phenolExcerpt = 'حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية » من جامعة السلطان قابوس على إنجازين مشرفين على المستوى الإقليمي والدولي جاء كالآتي: الميدالية الفضية …';

        $bookerBody = implode("\n\n", [
            'فازت الروائية جوخة الحارثي بجائزة مان بوكر العالمية لعام 2019عن روايتها ” سيدات القمر” في نسختها المترجمة” الأجرام السماوية” وفق ما أعلنت لجنة الجائزة في وقت متأخر من ليل الثلاثاء وبذلك تكون الحارثي اول عربية تفوز بهذه الجائزة الدولية المهمة.',
            'وتتناول رواية “سيدات القمر” للكاتبة العمانية جوخة الحارثي تحوّلات الماضي والحاضر وتَجْمع، بلغةٍ رشيقةٍ بين مآسي بشر لا ينقصهم شيء ومآسي آخرين ينقصهم كلُّ شيء وكتبت الروائية العمانية من قبل مجموعتين من الروايات القصيرة وكتابا للأطفال وثلاث روايات باللغة العربية.',
            'وقد نشرت الحارثية ثلاث مجموعات من القصص القصيرة و ثلاث روايات “منامات” و “سيدة القمر” و “نارنجة” والرواية الاخيرة كانت قد فازت بجائزة السلطان قابوس للثقافة والفون والاداب.',
            'وتتكون لجنة تحكيم الجائزة العالمية مان بوكر 2019 من لجنة مؤلفة من خمسة قضاة برئاسة بيتانى هيوز مؤرخ ومؤلف ومذيع حائز على الجوائز وهى مؤلفة من كاتب ومترجم ورئيس للغة الإنجليزية PEN Maureen Freely الفيلسوف البروفسور انجى هوبس الروائى والساخر ناثان جون والكاتب والروائى بانكاج ميشرا.',
            'وستحصل جوخة على قيمة الجائزة وهي 60 ألف دولار بالمشاركة مع الأكاديمية الأمريكية مارلين بوث التي ترجمت رواية “سيدات القمر” من العربية إلى الإنجليزية ومارلين أستاذة في الأدب العربي في جامعة أكسفورد.',
            'ودرست جوخة الشعر العربي الكلاسيكي في جامعة إدنبرة باستكلندا. وتعمل حاليا مدرسة في جامعة السلطان قابوس بالعاصمة العمانية مسقط..',
            'وتفوقت الكاتبة العمانية، 41 عاما، على 5 منافسين ضمتهم القائمة القصيرة للجائزة، من فرنسا وألمانيا وبولندا وكولومبيا وتشيلي وتحكي رواية “سيدات القمر” قصة ثلاث شقيقات تواجهن تغييرات تحدث في المجتمع العماني وكيف تتعامل عائلتهن مع هذه التغيرات.',
            'تدور أحداث الرواية في قرية “العوافي” في سلطنة عمان، حيث تواجه ثلاث شقيقات: مايا، التي تزوجت من عبد الله بعد مشاكل كثيرة وأسماء التي تزوجت فقط كواجب اجتماعي وخولة التي تنتظر حبيبها الذي هاجر إلى كندا وتعكس قصص الشقيقات الثلاث تطور عمان من مجتمع تقليدي يمتلك العبيد إلى حداثة معقدة.',
            'وقال المحكمون في بيان “إنها (الرواية) منظمة ومبنية بأناقة وهي تحكي عن فترة زمنية في عمان من خلال مشاعر الحب والفقدان داخل أسرة واحدة”.',
            'ووصفت صحيفة الغارديان الرواية بأنها تقدم “لمحة عن ثقافة غير معروفة نسبيا في الغرب”، بينما قالت صحيفة ذي ناشيونال إنها تشير إلى “ظهور موهبة أدبية كبرى” ووصفت الرواية بأنها “إنجاز قوي ومنسوجة بحرفية وبها خيال عميق”.',
            'وقالت رئيسة لجنة التحكيم بيتاني هيوز، إن الرواية أظهرت “فنا حساسا وجوانب مقلقة من تاريخنا المشترك”. وأضافت أن “الأسلوب يقاوم بمهارة العبارات المبتذلة عن العرق والعبودية والجنس”.',
        ]);

        $ministry = $organizations['ministry-culture-sports-youth'];

        $definitions = [
            'man-booker-2019' => [
                'achievement_scope_id' => $scopeId('international'),
                'achievement_main_type_id' => $mainTypeId('culture'),
                'sub_achievement_type_id' => $subTypes['adab']->id,
                'title_ar' => 'جوخة الحارثي أول عربية تحقق جائزة مان بوكر العالمية',
                'title_en' => 'Jokha Alharthi, the first Arab to win the International Booker Prize',
                'excerpt_ar' => 'فازت الروائية جوخة الحارثي بجائزة مان بوكر العالمية لعام 2019 عن روايتها «سيدات القمر» في نسختها المترجمة «الأجرام السماوية»، وبذلك تكون أول عربية تفوز بهذه الجائزة الدولية المهمة.',
                'description_ar' => $bookerBody,
                'cover_image_path' => "{$this->photos}/article-hero.jpg",
                'achieved_at' => '2019-05-21',
                'country_ar' => 'سلطنة عمان',
                'country_en' => 'Sultanate of Oman',
                'figures' => ['jokha-alharthi'],
                'organizations' => ['ministry-culture-sports-youth'],
            ],
            'gcc-honor' => [
                'achievement_scope_id' => $scopeId('international'),
                'achievement_main_type_id' => $mainTypeId('culture'),
                'sub_achievement_type_id' => $subTypes['adab']->id,
                'title_ar' => 'تكريم مجلس التعاون',
                'title_en' => 'GCC honoring',
                'excerpt_ar' => $phenolExcerpt,
                'description_ar' => 'كرّمت الأمانة العامة لمجلس التعاون لدول الخليج العربية المنجزين العمانيين تقديرًا لإسهاماتهم الثقافية والأدبية في المحافل الخليجية والدولية.',
                'cover_image_path' => "{$this->photos}/ach-booker.jpg",
                'achieved_at' => '2023-05-23',
                'country_ar' => 'سلطنة عمان',
                'country_en' => 'Sultanate of Oman',
                'figures' => ['jokha-alharthi'],
                'organizations' => ['ministry-culture-sports-youth'],
            ],
            'arab-literature-award' => [
                'achievement_scope_id' => $scopeId('international'),
                'achievement_main_type_id' => $mainTypeId('culture'),
                'sub_achievement_type_id' => $subTypes['adab']->id,
                'title_ar' => 'جائزة الأدب العربي',
                'title_en' => 'Arabic Literature Award',
                'excerpt_ar' => $phenolExcerpt,
                'description_ar' => 'نالت الأديبة العمانية جائزة الأدب العربي عن مجمل أعمالها الروائية والقصصية التي عبّرت عن المجتمع العماني وقضاياه.',
                'cover_image_path' => "{$this->photos}/ach-red-award.jpg",
                'achieved_at' => '2023-05-23',
                'country_ar' => 'سلطنة عمان',
                'country_en' => 'Sultanate of Oman',
                'figures' => ['jokha-alharthi'],
                'organizations' => [],
            ],
            'celestial-bodies-translation' => [
                'achievement_scope_id' => $scopeId('international'),
                'achievement_main_type_id' => $mainTypeId('culture'),
                'sub_achievement_type_id' => $subTypes['adab']->id,
                'title_ar' => 'صدور الترجمة الإنجليزية لرواية سيدات القمر (الأجرام السماوية)',
                'title_en' => 'English translation of Celestial Bodies published',
                'excerpt_ar' => $phenolExcerpt,
                'description_ar' => 'صدرت الترجمة الإنجليزية لرواية «سيدات القمر» بعنوان «الأجرام السماوية» بترجمة الأكاديمية مارلين بوث، ولاقت صدى واسعًا في الصحافة العالمية.',
                'cover_image_path' => "{$this->photos}/ach-celestial.jpg",
                'achieved_at' => '2018-04-10',
                'country_ar' => 'سلطنة عمان',
                'country_en' => 'Sultanate of Oman',
                'figures' => ['jokha-alharthi'],
                'organizations' => [],
            ],
            'squ-culture-award' => [
                'achievement_scope_id' => $scopeId('local'),
                'achievement_main_type_id' => $mainTypeId('culture'),
                'sub_achievement_type_id' => $subTypes['adab']->id,
                'title_ar' => 'جائزة السلطان قابوس للثقافة والفنون والآداب عن رواية نارنجة',
                'title_en' => 'Sultan Qaboos Award for Culture, Arts and Literature for the novel Naranjah',
                'excerpt_ar' => $phenolExcerpt,
                'description_ar' => 'فازت رواية «نارنجة» لجوخة الحارثي بجائزة السلطان قابوس للثقافة والفنون والآداب، تتويجًا لمسيرة أدبية متميزة على المستوى المحلي.',
                'cover_image_path' => "{$this->photos}/ach-red-award.jpg",
                'achieved_at' => '2016-11-15',
                'country_ar' => 'سلطنة عمان',
                'country_en' => 'Sultanate of Oman',
                'figures' => ['jokha-alharthi'],
                'organizations' => [],
            ],
            'writers-association-honor' => [
                'achievement_scope_id' => $scopeId('local'),
                'achievement_main_type_id' => $mainTypeId('culture'),
                'sub_achievement_type_id' => $subTypes['adab']->id,
                'title_ar' => 'تكريم من جمعية الكتاب والأدباء العمانية',
                'title_en' => 'Honoring by the Omani Writers and Authors Association',
                'excerpt_ar' => $phenolExcerpt,
                'description_ar' => 'كرّمت جمعية الكتاب والأدباء العمانية الروائية جوخة الحارثي تقديرًا لإسهاماتها في المشهد الأدبي العماني والعربي.',
                'cover_image_path' => "{$this->photos}/ach-booker.jpg",
                'achieved_at' => '2021-03-15',
                'country_ar' => 'سلطنة عمان',
                'country_en' => 'Sultanate of Oman',
                'figures' => ['jokha-alharthi'],
                'organizations' => [],
            ],
            'muscat-bookfair-honor' => [
                'achievement_scope_id' => $scopeId('local'),
                'achievement_main_type_id' => $mainTypeId('culture'),
                'sub_achievement_type_id' => $subTypes['adab']->id,
                'title_ar' => 'تكريم في معرض مسقط الدولي للكتاب',
                'title_en' => 'Honoring at Muscat International Book Fair',
                'excerpt_ar' => $phenolExcerpt,
                'description_ar' => 'شهد معرض مسقط الدولي للكتاب تكريم الأديبة جوخة الحارثي احتفاءً بمنجزها الأدبي وحضورها العربي والدولي.',
                'cover_image_path' => "{$this->photos}/ach-celestial.jpg",
                'achieved_at' => '2020-02-22',
                'country_ar' => 'سلطنة عمان',
                'country_en' => 'Sultanate of Oman',
                'figures' => ['jokha-alharthi'],
                'organizations' => [],
            ],
            'itex-silver-medal' => [
                'achievement_scope_id' => $scopeId('international'),
                'achievement_main_type_id' => $mainTypeId('innovations'),
                'sub_achievement_type_id' => $subTypes['inventions']->id,
                'title_ar' => 'الميدالية الفضية في معرض ماليزيا الدولي للاختراعات (ITEX)',
                'title_en' => 'Silver medal at Malaysia International Invention Expo (ITEX)',
                'excerpt_ar' => $phenolExcerpt,
                'description_ar' => 'حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية» من جامعة السلطان قابوس على الميدالية الفضية في معرض ماليزيا الدولي للاختراعات ITEX، ضمن مشاركة عمانية متميزة بين مخترعي العالم.',
                'cover_image_path' => "{$this->photos}/ach-itex.jpg",
                'achieved_at' => '2023-05-23',
                'country_ar' => 'ماليزيا',
                'country_en' => 'Malaysia',
                'figures' => ['hamad-alkharousi'],
                'organizations' => [],
            ],
            'malaysia-invention-gold' => [
                'achievement_scope_id' => $scopeId('international'),
                'achievement_main_type_id' => $mainTypeId('innovations'),
                'sub_achievement_type_id' => $subTypes['inventions']->id,
                'title_ar' => 'ذهبية معرض ماليزيا للاختراعات',
                'title_en' => 'Gold medal at Malaysia Invention Expo',
                'excerpt_ar' => $phenolExcerpt,
                'description_ar' => 'تُوّج المخترع العماني بالميدالية الذهبية في معرض ماليزيا للاختراعات عن ابتكاره المتميز، رافعًا اسم سلطنة عمان في المحافل العلمية الدولية.',
                'cover_image_path' => "{$this->photos}/ach-inventor.jpg",
                'achieved_at' => '2023-05-11',
                'country_ar' => 'ماليزيا',
                'country_en' => 'Malaysia',
                'figures' => ['muhannad-alkindi'],
                'organizations' => [],
            ],
            'basketball-championship' => [
                'achievement_scope_id' => $scopeId('local'),
                'achievement_main_type_id' => $mainTypeId('sports'),
                'sub_achievement_type_id' => $subTypes['basketball']->id,
                'title_ar' => 'بطولة كرة السلة',
                'title_en' => 'Basketball championship',
                'excerpt_ar' => $phenolExcerpt,
                'description_ar' => 'حقق الفريق العماني لقب بطولة كرة السلة بعد مشوار متميز في البطولة، ليضيف إنجازًا رياضيًا جديدًا إلى سجل الرياضة العمانية.',
                'cover_image_path' => "{$this->photos}/ach-basketball.jpg",
                'achieved_at' => '2023-05-23',
                'country_ar' => 'سلطنة عمان',
                'country_en' => 'Sultanate of Oman',
                'figures' => ['fayez-alrushaidi'],
                'organizations' => ['ministry-culture-sports-youth'],
            ],
            'asia-best-goalkeeper' => [
                'achievement_scope_id' => $scopeId('international'),
                'achievement_main_type_id' => $mainTypeId('sports'),
                'sub_achievement_type_id' => $subTypes['football']->id,
                'title_ar' => 'علي الحبسي ضمن قائمة أفضل حراس القارة الآسيوية',
                'title_en' => 'Ali Al-Habsi among Asia\'s best goalkeepers',
                'excerpt_ar' => $phenolExcerpt,
                'description_ar' => 'اختير الحارس العماني علي الحبسي ضمن قائمة أفضل حراس المرمى في القارة الآسيوية، تقديرًا لمسيرته الاحترافية الحافلة في الدوري الإنجليزي ومع المنتخب الوطني.',
                'cover_image_path' => "{$this->photos}/carousel-1.jpg",
                'achieved_at' => '2016-12-01',
                'country_ar' => 'سلطنة عمان',
                'country_en' => 'Sultanate of Oman',
                'figures' => ['ali-alhabsi'],
                'organizations' => [],
            ],
        ];

        $sort = 0;

        foreach ($definitions as $slug => $data) {
            $figureSlugs = $data['figures'];
            $organizationSlugs = $data['organizations'];
            unset($data['figures'], $data['organizations']);

            $achievement = Achievement::updateOrCreate(
                ['slug' => $slug],
                $data + [
                    'is_published' => true,
                    'sort_order' => ++$sort,
                ]
            );

            // DatabaseSeeder mutes model events (WithoutModelEvents), so the pivot
            // models' UUID-generating `creating` hooks don't fire here — pass ids explicitly.
            $pivotIds = fn (array $slugs, array $models) => collect($slugs)
                ->mapWithKeys(fn (string $s) => [$models[$s]->id => ['id' => (string) Str::uuid()]])
                ->all();

            $achievement->publicFigures()->sync($pivotIds($figureSlugs, $figures));
            $achievement->organizations()->sync($pivotIds($organizationSlugs, $organizations));
        }
    }

    /**
     * @param  array<string, Organization>  $organizations
     */
    private function seedNews(array $organizations): void
    {
        $ministryId = $organizations['ministry-culture-sports-youth']->id;

        $definitions = [
            'book-launch-ceremony' => [
                'title_ar' => 'إقامة حفل تدشين الكتاب بمجلس عُمان تحت رعاية معالي أمين عام شؤون البلاط السلطاني.',
                'cover_image_path' => "{$this->photos}/about-news-launch.jpg",
                'published_at' => '2023-05-23 10:00:00',
                'show_on_about' => true,
            ],
            'book-copies-gift' => [
                'title_ar' => 'نشر المعرفة بإهداء 9000 نسخة للمنجزين والمؤسسات التعليمية والحكومية والخاصة.',
                'cover_image_path' => "{$this->photos}/about-news-gift.jpg",
                'published_at' => '2023-05-20 10:00:00',
                'show_on_about' => true,
            ],
            'social-media-spotlight' => [
                'title_ar' => 'تسليط الضوء على أكثر من ١٥٠٠ منجز عبر مواقع التواصل الاجتماعي',
                'cover_image_path' => "{$this->photos}/about-news-spotlight.jpg",
                'published_at' => '2023-05-18 10:00:00',
                'show_on_about' => true,
            ],
            'hockey-draw-2024' => [
                'title_ar' => 'قرعة متكافئة لبطولة كأس العالم لخماسيات الهوكي مسقط ٢٠٢٤',
                'cover_image_path' => "{$this->photos}/about-news-hockey.jpg",
                'published_at' => '2023-05-15 10:00:00',
                'show_on_about' => true,
                'organization_id' => $ministryId,
            ],
            'saudi-culture-mou' => [
                'title_ar' => 'مذكرة تفاهم مع وزارة الثقافة بالمملكة العربية السعودية',
                'cover_image_path' => "{$this->photos}/news-mou.jpg",
                'published_at' => '2023-05-12 10:00:00',
                'organization_id' => $ministryId,
            ],
            'riyadh-bookfair-visit' => [
                'title_ar' => 'سمو السيد ذي يزن يزور معرض الكتاب في الرياض',
                'cover_image_path' => "{$this->photos}/news-bookfair.jpg",
                'published_at' => '2023-05-10 10:00:00',
                'organization_id' => $ministryId,
            ],
            'doha-culture-ministers' => [
                'title_ar' => 'سلطنة عمان تشارك في مؤتمر وزراء الثقافة في العالم الاسلامي بالدوحة',
                'cover_image_path' => "{$this->photos}/news-conference.jpg",
                'published_at' => '2023-05-08 10:00:00',
                'organization_id' => $ministryId,
            ],
            'hockey-opening' => [
                'title_ar' => 'حضور واسع في افتتاح بطولة كأس العالم لخماسيات الهوكي',
                'cover_image_path' => "{$this->photos}/news-hockey.jpg",
                'published_at' => '2023-05-05 10:00:00',
                'organization_id' => $ministryId,
            ],
        ];

        $sort = 0;

        foreach ($definitions as $slug => $data) {
            News::updateOrCreate(
                ['slug' => $slug],
                $data + [
                    'body_ar' => $data['title_ar']."\n\n".'تفاصيل الخبر كاملة تُعرض هنا، وتوثق المبادرة هذا النشاط ضمن جهودها في أرشفة إنجازات شباب عمان وإيصالها للمجتمع المحلي والدولي.',
                    'excerpt_ar' => $data['title_ar'],
                    'is_published' => true,
                    'sort_order' => ++$sort,
                ]
            );
        }
    }

    private function seedTestimonials(): void
    {
        $definitions = [
            'asaad-al-said' => [
                'person_name_ar' => 'صاحب السمو السيد أسعد بن طارق آل سعيد',
                'person_name_en' => 'His Highness Sayyid Asaad bin Tariq Al Said',
                'position_ar' => 'نائب رئيس مجلس الوزراء لشؤون التعاون الخارجي والممثل الخاص لجلالة السلطان',
                'quote_ar' => 'الشكر موصول لكل من ساهم في إخراج هذا العمل وأرشفة هذه الإنجازات النيرة لإيصالها للمجتمع المحلي والدولي بصورة تليق بحجم إنجازات شباب عمان',
                'photo_path' => "{$this->photos}/quote-asaad.png",
            ],
            'nasr-alkindi' => [
                'person_name_ar' => 'معالي نصر بن حمود الكندي',
                'person_name_en' => 'Nasr bin Hamoud Al-Kindi',
                'position_ar' => 'أمين عام شؤون البلاط السلطاني',
                'quote_ar' => 'توثيق هذه الإنجازات مطلب مهم، لتعلم الأجيال القادمة ما حققته الأجيال السابقة والحالية، عليه أبارك هذا العمل وأشكر كل من ساهم فيه متمنيا التوفيق للجميع.',
                'photo_path' => "{$this->photos}/quote-nasr.png",
            ],
            'sultan-alnumani' => [
                'person_name_ar' => 'معالي الفريق أول سلطان بن محمد النعماني',
                'person_name_en' => 'Lieutenant General Sultan bin Mohammed Al-Nuamani',
                'position_ar' => 'وزير المكتب السلطاني',
                'quote_ar' => 'الشكر والتقدير على إنجاز هذا الكتاب القيم والثناء على الجهود الواضحة فيه كسجل وطني توثيقي للإنجازات شباب عمان.',
                'photo_path' => "{$this->photos}/quote-asaad.png",
            ],
        ];

        $sort = 0;

        foreach ($definitions as $slug => $data) {
            Testimonial::updateOrCreate(
                ['slug' => $slug],
                $data + [
                    'is_published' => true,
                    'sort_order' => ++$sort,
                ]
            );
        }
    }

    private function seedTeamMembers(): void
    {
        $definitions = [
            'mundhir-almaamari' => ['name_ar' => 'منذر بن إبراهيم المعمري', 'name_en' => 'Mundhir bin Ibrahim Al-Maamari', 'position_ar' => 'المؤسس والرئيس الفريق', 'position_en' => 'Founder and Team President'],
            'ahmed-albalushi' => ['name_ar' => 'أحمد بن سالم البلوشي', 'name_en' => 'Ahmed bin Salem Al-Balushi', 'position_ar' => 'نائب الرئيس', 'position_en' => 'Vice President'],
            'maryam-alhinai' => ['name_ar' => 'مريم بنت راشد الهنائية', 'name_en' => 'Maryam bint Rashid Al-Hinai', 'position_ar' => 'مسؤولة التوثيق', 'position_en' => 'Documentation Officer'],
            'khalid-alamri-team' => ['name_ar' => 'خالد بن محمد العامري', 'name_en' => 'Khalid bin Mohammed Al-Amri', 'position_ar' => 'مسؤول الإعلام', 'position_en' => 'Media Officer'],
            'salem-alkharousi' => ['name_ar' => 'سالم بن حمد الخروصي', 'name_en' => 'Salem bin Hamad Al-Kharousi', 'position_ar' => 'المنسق العام', 'position_en' => 'General Coordinator'],
            'fatma-albalushi' => ['name_ar' => 'فاطمة بنت علي البلوشية', 'name_en' => 'Fatma bint Ali Al-Balushi', 'position_ar' => 'مسؤولة العلاقات العامة', 'position_en' => 'Public Relations Officer'],
            'abdullah-alharthi' => ['name_ar' => 'عبدالله بن ناصر الحارثي', 'name_en' => 'Abdullah bin Nasser Al-Harthi', 'position_ar' => 'مسؤول التحرير', 'position_en' => 'Editing Officer'],
            'hilal-almaamari' => ['name_ar' => 'هلال بن سيف المعمري', 'name_en' => 'Hilal bin Saif Al-Maamari', 'position_ar' => 'مسؤول تقنية المعلومات', 'position_en' => 'IT Officer'],
        ];

        $sort = 0;

        foreach ($definitions as $slug => $data) {
            TeamMember::updateOrCreate(
                ['slug' => $slug],
                $data + [
                    'photo_path' => "{$this->about}/team-member.png",
                    'is_published' => true,
                    'sort_order' => ++$sort,
                ]
            );
        }
    }

    /**
     * @param  array<string, PublicFigure>  $figures
     */
    private function seedMedia(array $figures): void
    {
        $jokhaId = $figures['jokha-alharthi']->id;

        $publications = [
            'first-publication' => [
                'title_ar' => 'الإصدار الأول',
                'title_en' => 'First Publication',
                'description_ar' => 'الإصدار الأول من كتاب إنجازات شباب عمان',
                'cover_image_path' => "{$this->photos}/media-book-1.jpg",
                'file_path' => "{$this->documents}/publication-1.pdf",
                'sort_order' => 1,
            ],
            'second-publication' => [
                'title_ar' => 'الاصدار الثاني',
                'title_en' => 'Second Publication',
                'description_ar' => 'الإصدار الثاني - ملهمون من عمان',
                'cover_image_path' => "{$this->photos}/media-book-2.jpg",
                'file_path' => "{$this->documents}/publication-2.pdf",
                'sort_order' => 2,
            ],
        ];

        foreach ($publications as $slug => $data) {
            PublicationMedia::updateOrCreate(
                ['slug' => $slug],
                $data + ['is_active' => true, 'is_published' => true]
            );
        }

        $videos = [
            'jokha-booker-interview' => [
                'public_figure_id' => $jokhaId,
                'title_ar' => 'لقاء مع د. جوخة الحارثية عن فوزها بجائزة مان بوكر',
                'title_en' => 'Interview with Dr. Jokha Al-Harithiya on winning the Booker Prize',
                'url' => 'https://www.youtube.com/@injazat_o',
                'cover_image_path' => "{$this->photos}/media-red-award.jpg",
                'sort_order' => 1,
            ],
            'jokha-booker-ceremony' => [
                'public_figure_id' => $jokhaId,
                'title_ar' => 'حفل تتويج جائزة مان بوكر العالمية 2019',
                'title_en' => 'International Booker Prize 2019 award ceremony',
                'url' => 'https://www.youtube.com/@injazat_o',
                'cover_image_path' => "{$this->photos}/media-booker.jpg",
                'sort_order' => 2,
            ],
            'recorded-interviews' => [
                'public_figure_id' => null,
                'title_ar' => 'لقاءات مسجلة مع المنجزين',
                'title_en' => 'Recorded interviews with achievers',
                'url' => 'https://www.youtube.com/@injazat_o',
                'cover_image_path' => "{$this->photos}/about-4.jpg",
                'sort_order' => 3,
            ],
        ];

        foreach ($videos as $slug => $data) {
            VideoMedia::updateOrCreate(
                ['slug' => $slug],
                $data + ['is_active' => true, 'is_published' => true]
            );
        }

        $albums = [
            'booker-ceremony-album' => [
                'public_figure_id' => $jokhaId,
                'title_ar' => 'صور من حفل جائزة مان بوكر العالمية',
                'title_en' => 'Photos from the Booker Prize ceremony',
                'images' => [
                    "{$this->photos}/media-celestial.jpg",
                    "{$this->photos}/media-booker.jpg",
                    "{$this->photos}/media-red-award.jpg",
                ],
                'sort_order' => 1,
            ],
            'various-photos' => [
                'public_figure_id' => null,
                'title_ar' => 'صور متنوعة',
                'title_en' => 'Various photos',
                'images' => [
                    "{$this->photos}/about-1.jpg",
                    "{$this->photos}/about-2.jpg",
                    "{$this->photos}/about-3.jpg",
                    "{$this->photos}/about-4.jpg",
                ],
                'sort_order' => 2,
            ],
        ];

        foreach ($albums as $slug => $data) {
            ImageMedia::updateOrCreate(
                ['slug' => $slug],
                $data + ['is_active' => true, 'is_published' => true]
            );
        }
    }

    private function seedSettings(): void
    {
        $general = app(GeneralSettings::class);
        $general->name_ar = 'إنجازات شباب عمان';
        $general->name_en = 'Omani Youth Achievements';
        $general->address_ar = 'محافظة مسقط - سلطنة عمان';
        $general->address_en = 'Muscat Governorate - Sultanate of Oman';
        $general->phone = '90229422';
        $general->phone_secondary = '99555564';
        $general->email = 'injazat2040@gmail.com';
        $general->social_media_accounts = [
            ['platform' => 'instagram', 'url' => 'https://instagram.com/injazat_o'],
            ['platform' => 'twitter', 'url' => 'https://twitter.com/injazat_o'],
            ['platform' => 'linkedin', 'url' => 'https://www.linkedin.com/company/injazat_o'],
        ];
        $general->map_image = "{$this->photos}/contact-map.jpg";
        $general->map_url = 'https://www.google.com/maps/search/?api=1&query=Muscat,Oman';
        $general->save();

        $home = app(HomeSettings::class);
        $home->hero_image = "{$this->photos}/hero-portrait.jpg";
        $home->quote_ar = 'إن الشباب هم ثروة الأمم وموردها الذي لا ينضب وسواعدها التي تبني، هم حاضر الأمة ومستقبلها، وسوف نحرص على الاستماع لهم وتلمس احتياجاتهم واهتماماتهم وتطلعاتهم ولا شك أنها ستجد العناية التي تستحق';
        $home->quote_en = 'Youth are the wealth of nations, their inexhaustible resource and the arms that build. They are the present and the future of the nation, and we will be keen to listen to them and to attend to their needs, interests and aspirations, which will no doubt receive the care they deserve.';
        $home->quote_author_ar = 'حضرة صاحب الجلالة السلطان هيثم حفظه الله ورعاه';
        $home->quote_author_en = 'His Majesty Sultan Haitham bin Tariq, may Allah protect him';
        $home->save();

        $about = app(AboutSettings::class);
        $about->intro_text_ar = implode("\n", [
            'من منطلقِ الواجب الوطني، وفخرًا بهذا البلد العظيم ومُنْجِزيه، واسترشادًا بقائده الداعم الأول للشباب.',
            'انبثقت فكرةُ هذه المبادرةِ الوطنية التي سَعَيْنا جاهدِين فيها أن تحتضنَ إنجازاتِ شباب عُمانَ، ولنسلطَ الضَّوْءَ عليها ونخلدَها، ولتكون إرثًا ثقافيًّا ومعرفيًا للأجيال القادمة.',
            'نطمح بهذا المشروعِ الوطني أن نوثقَ نجاحاتِ أبناء هذا البلد المشرِّفةَ داخليًّا وخارجيًّا؛ ليكون مرجعًا معرفيًّا وقاعدةً أساسيةً لتخليد إنجازاتِ المنجزين الذين رفعوا راياتِ عُمانَ في المحافل الإقليمية والدولية، وحققوا بنجاحاتهم رُؤَى سلطانِنا المعظمِ. فهذه المبادرة ما هي إلا استجابةٌ لدعوته حفظه الله بالاهتمام بالإنسان العُماني كصانعِ حضارةٍ، وهدفًا للتنمية وأداةً للتطوير والتعمير.',
        ]);
        $about->intro_text_en = 'Out of national duty, and in pride of this great country and its achievers, this national initiative was born to embrace the achievements of Omani youth, shed light on them and immortalize them as a cultural and knowledge legacy for generations to come.';
        $about->intro_image_one = "{$this->photos}/about-1.jpg";
        $about->intro_image_two = "{$this->photos}/about-2.jpg";
        $about->intro_image_three = "{$this->photos}/about-3.jpg";
        $about->intro_image_four = "{$this->photos}/about-4.jpg";
        $about->vision_ar = 'أن نكون المِنَصَّةَ الأساسية والمرجعَ الأولَ لتوثيق إنجازات الشباب العُماني في سلطنة عُمان';
        $about->vision_en = 'To be the primary platform and first reference for documenting the achievements of Omani youth in the Sultanate of Oman.';
        $about->message_ar = 'توثيقُ الإنجازاتِ العُمانية للأفراد والمؤسسات، وتسليطُ الضَّوْءِ على إبداعاتهم المتميزة وتجاربهم الناجحة، ونشرُ ثقافة الإنجاز والابتكار في بيئة مثالية لإلهامِ الأجيالِ القادمة';
        $about->message_en = 'Documenting Omani achievements of individuals and institutions, highlighting their distinguished creativity and successful experiences, and spreading a culture of achievement and innovation to inspire future generations.';
        $about->values_ar = 'انظر إلى الأشياءِ بشكل مختلف عن الآخرين حتى تصل إلى نقطة الإلهام؛ فالقليل من التفكير يولد الكثير من الشَّغَفِ، لا تكتفِ بالمشاهدة، كُن فضوليًّا، وحاوِلِ البحثَ وتعلَّمْ ما يدفعك للابتكار؛ هكذا يجب أن يكونَ تفكيرُك حتى تجد نفسَك في مراتب الإنجاز';
        $about->values_en = 'Look at things differently until you reach the point of inspiration; be curious, search, and learn what drives you to innovate, until you find yourself among the achievers.';
        $about->founder_message_ar = implode("\n", [
            'إن الشباب هم نهضةُ الأوطان ومحرك الحضارة وصمام الأمان، فبهم الأممُ تقوم وعلى عقولهم النيرة تعلقُ الآمال وعلى يديهم تُبنى ركائزُ الوطن. وإن اهتمام مولانا حضرةِ صاحب الجلالة السلطان هيثم بن طارق المعظم (حفظهُ الله ورعاه) بالشباب كان جليًا؛ إذ آمن جلالتهُ (أبقاهُ الله) بأهميةِ الاستثمار في طاقات شباب عُمان وتوجيهها توجيهًا صحيحًا كما وشدد على أهميةِ النهوضِ بهم علميًا وفكريًا وعمليًا وثقافيًا ورياضيًا وفنيًا، فكان الشاب العُماني منذُ البدأ شريكُا جوهريًا في عمليةِ التنميةِ الشاملة وركنًا أسياسيًا للنهوض بهذا الوطن العزيز. وعليه فإن عُمان ستصل لغاياتها المنشودة وسيتحقق لها ما تصبو إليه من مكانةٍ عاليةٍ مرموقةٍ على يد شبابنا الطموح المثابر.',
            'أخواني وأخواتي شباب عُمان المجيدين؛ إيمانًا مني بجهودكم الملموسة في سبيل رقي وخدمة هذا الوطن العزيز، ولأنكم حملتم راية العلمِ والعمل فأحسنتم واجتهدتم ونِعمَ الجهد الذي بذلتموه ليرفرف علم بلادنا عاليًا بين الشعوب، وسعيًا مني لحفظ إنجازاتكم النيرة وأسمائكم التي سطعت في سماءِ الإنجازِ والإبداع؛ كرس فريق العمل وقتهُ وجهدهُ بعد بتوفيقٍ من الله تعالى على مدار ٨ أعوام لتوثيق إنجازاتكم المشرفة، وقد تمثل ذلك في الإصدار الأول والثاني من كتاب إنجازات شباب عُمان، وكتاب ملهمون من عُمان، إضافة إلى الموقع الإلكتروني الذي سيضم ما بين أقسامه أكثر من ١٥٠٠ مجيدة ومجيدة.',
            'شباب عُمان، سيروا على بركةٍ من الله وتوفيقهِ في دروبِ العلا والمجد، فعلى يديكم سينبتُ في جبين الأفقِ ألفُ إنجازٍ وإنجاز.',
        ]);
        $about->founder_message_en = 'Youth are the renaissance of nations and the engine of civilization. For eight years our team has dedicated its time to documenting your honorable achievements, embodied in the first and second editions of the Omani Youth Achievements book, the Inspirers from Oman book, and this website.';
        $about->founder_name_ar = 'منذر بن إبراهيم المعمري';
        $about->founder_name_en = 'Mundhir bin Ibrahim Al-Maamari';
        $about->founder_position_ar = 'المؤسس والرئيس الفريق';
        $about->founder_position_en = 'Founder and Team President';
        $about->founder_photo = "{$this->about}/founder.png";
        $about->profile_document = "{$this->documents}/profile.pdf";
        $about->save();

        $media = app(MediaSettings::class);
        $media->intro_text_ar = 'تجمع المكتبة الإعلامية إصدارات المبادرة ولقاءاتها المسجلة وألبومات الصور من مختلف المناسبات.';
        $media->intro_text_en = 'The media library brings together the initiative\'s publications, recorded interviews and photo albums.';
        $media->save();
    }
}
