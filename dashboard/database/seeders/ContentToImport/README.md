# دليل تعبئة ملف المحتوى / Content File Guide

ضع ملفات JSON في هذا المجلد (`database/seeders/ContentToImport/`) — يمكن أن يكون أكثر من ملف (مثلاً ملف لكل مجال: `football.json`, `culture.json`...). كل ملف يحتوي مصفوفة `achievers` و/أو `achievements` بنفس الصيغة الموضحة في `achievers-achievements.example.json`. الملفات المنتهية بـ `.example.json` لا تُستورد.

Place JSON files in this folder (`database/seeders/ContentToImport/`) — multiple files are supported (e.g. one per field). Each file may contain an `achievers` and/or `achievements` array in the format shown in `achievers-achievements.example.json`. Files ending in `.example.json` are not imported.

## القواعد العامة / General rules

- لا تغيّر أسماء الحقول (keys). / Do not rename keys.
- الحقول العربية (`*_ar`) إلزامية. الحقول الإنجليزية (`*_en`) اختيارية — اتركها `null` إذا لم تتوفر.
- `slug`: معرّف فريد بالإنجليزية (حروف صغيرة وأرقام وشرطات فقط) يُستخدم في روابط الموقع. لا تكرره بين السجلات.
- `sort_order`: رقم صحيح للترتيب (الأصغر يظهر أولاً).
- `is_published`: `true` ليظهر في الموقع، `false` لإخفائه.
- الصور توضع في مجلد مرفق (مثلاً مجلد `images/` داخل هذا المجلد) وتُكتب مساراتها النسبية في الحقول (مثال: `"photos/jokha.jpg"`).

## المنجزون / Achievers

| الحقل | الوصف |
|---|---|
| `slug` | معرّف فريد (مثال: `jokha-alharthi`) |
| `name_ar` / `name_en` | الاسم بالعربية (إلزامي) والإنجليزية (اختياري) |
| `headline_ar` / `headline_en` | سطر التعريف المهني تحت الاسم (مثال: أكاديمية - كاتبة - فائزة بجائزة...) |
| `description_ar` / `description_en` | نبذة تعريفية |
| `story_ar` / `story_en` | قصة المنجز الكاملة (قسم "قصتي") |
| `main_type` | المجال الرئيسي — واحدة من القيم في الجدول أدناه |
| `is_featured` | `true` ليظهر في البانر المميز بالصفحة الرئيسية (منجز واحد فقط يُفضّل) |
| `in_home_carousel` | `true` ليظهر في سلايدر الصور بالصفحة الرئيسية |
| `images.image` | الصورة الشخصية العامة |
| `images.tile` | صورة البطاقة — نسبة 351×427 (بورتريه) |
| `images.cutout` | صورة مقصوصة بخلفية شفافة PNG — 848×477 (تظهر في هيدر الصفحة الشخصية) |
| `images.banner` | صورة البانر العريض (للمنجز المميز) |
| `social.*` | روابط التواصل — `null` لما هو غير متوفر |
| `achievements` | مصفوفة slugs لإنجازاته (ربط تلقائي) |

## الإنجازات / Achievements

| الحقل | الوصف |
|---|---|
| `slug` | معرّف فريد |
| `title_ar` / `title_en` | عنوان الإنجاز |
| `excerpt_ar` / `excerpt_en` | مقتطف قصير لبطاقة الإنجاز |
| `description_ar` / `description_en` | نص المقال الكامل |
| `scope` | `international` (دولي) أو `local` (محلي) |
| `main_type` | المجال الرئيسي — من الجدول أدناه |
| `sub_type` | النوع الفرعي (slug) أو `null` — يجب أن يكون موجوداً مسبقاً في لوحة التحكم |
| `achieved_at` | تاريخ الإنجاز بصيغة `YYYY-MM-DD` |
| `country_ar` / `country_en` | الدولة (تظهر كوسم ذهبي في المقال) |
| `cover_image` | صورة الغلاف — نسبة 473×283 |
| `video_url` | رابط فيديو أو `null` |
| `achievers` | مصفوفة slugs للمنجزين المرتبطين |
| `organizations` | مصفوفة slugs للمؤسسات المرتبطة (يجب أن تكون موجودة مسبقاً في لوحة التحكم) |

## قيم المجالات الرئيسية / main_type values

| slug | العربية |
|---|---|
| `sports` | الرياضة |
| `culture` | الثقافة |
| `science` | العلوم |
| `military` | العسكرية |
| `medical` | الطبي |
| `innovations` | الإبتكارات |
| `literature` | الأدب |
| `students` | طلبة المدارس |
| `arts` | الفنون |
| `entrepreneurship` | ريادة الأعمال |

## بعد التسليم / After delivery

يُستورد الملف بالأمر:
The file is imported with:

```sh
php artisan db:seed --class=ContentImportSeeder
```

الاستيراد idempotent: يحدّث السجلات الموجودة (حسب slug) دون تكرار.
The import is idempotent — records are updated by slug, never duplicated.
