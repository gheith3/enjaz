<div class="about-section">
    {{view("components.layouts.nav")}}
    <div class="container py-5">
        <div style="margin-top: 80px" class="row align-items-center">
            <!-- Text Content - Left Half -->
            <div class="col-lg-6">
                <p class="lead mb-4 text-white">
                    من منطلقِ الواجب الوطني، وفخرًا بهذا
                     البلد العظيم ومُنْجِزيه، واسترشادًا بقائده الداعم الأول
                     للشباب. انبثقت فكرةُ هذه المبادرةِ الوطنية التي سَعَيْنا
                    جاهدِين فيها أن تحتضنَ إنجازاتِ شباب عُمانَ، ولنسلطَ الضَّوْءَ
                     عليها ونخلدَها، ولتكون إرثًا ثقافيًّا ومعرفيًا للأجيال
                     القادمة.</br> نطمح بهذا المشروعِ الوطني أن نوثقَ نجاحاتِ
                    أبناء هذا البلد المشرِّفةَ داخليًّا وخارجيًّا؛ ليكون مرجعًا
                    معرفيًّا وقاعدةً أساسيةً لتخليد إنجازاتِ المنجزين الذين
                    رفعوا راياتِ عُمانَ في المحافل الإقليمية والدولية،
                     وحققوا بنجاحاتهم رُؤَى سلطانِنا المعظمِ. فهذه
                    المبادرة ما هي إلا استجابةٌ لدعوته حفظه الله
                     بالاهتمام بالإنسان العُماني كصانعِ حضارةٍ،
                     وهدفًا للتنمية وأداةً للتطوير والتعمير.


                </p>
            </div>

            <!-- Images Grid - Right Half -->
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <img src="{{ asset('images/app/person.png') }}" alt="About Us Image 1" class="img-fluid rounded shadow">
                    </div>
                    <div class="col-6">
                        <img src="{{ asset('images/app/person.png') }}" alt="About Us Image 2" class="img-fluid rounded shadow">
                    </div>
                    <div class="col-6">
                        <img src="{{ asset('images/app/person.png') }}" alt="About Us Image 3" class="img-fluid rounded shadow">
                    </div>
                    <div class="col-6">
                        <img src="{{ asset('images/app/person.png') }}" alt="About Us Image 4" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.about-section {
    position: relative;
    width: 100%;
    min-height: 50vh;
    border: 1px solid #707070;
    background: transparent linear-gradient(180deg, #0B626A 0%, #063135 100%) 0% 0% no-repeat padding-box;
    border-radius: 0px 0px 250px 0px;
    opacity: 1;
}
</style>
@endpush
