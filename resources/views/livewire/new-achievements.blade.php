<div>
    <div class="container" id="new-achievement">
        <x-section-title :title="'أحدث الإنجازات'" />
        <div class="row" id="new-achievements-boxes">
            @for($i = 1; $i <= 3; $i++)
                <div class="col-md-4">
                    <div class="card m-3 new-achievement-card">
                        <div class="card-body">
                            <img src="{{asset("images/app/person.png")}}"
                                 class="rounded"
                                 style="width: 100%; height: 250px"
                                 alt="">
                            <div class="d-flex gap-1 mt-3">
                                <h6><span class="badge achievement-badge p-2">الثقافة</span></h6>
                                <h6><span class="badge achievement-badge p-2">الأدب</span></h6>
                            </div>
                            <div class="mt-3">
                                <p class="text-black-50">2024/09/01</p>
                                <p class="new-achievement-title">جائزة الأدب العربي</p>
                                <p class="mt-1">
                                    حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية » من جامعة السلطان قابوس على إنجازين مشرفين على المستوى الإقليمي والدولي جاء كالآتي: الميدالية الفضية …
                                </p>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a class="btn app-btn read-more-btn" href="">
                                    اقرأ المزيد
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            @endfor
        </div>
        <div class="d-flex justify-content-end mt-5">
            <button class="btn app-btn px-5 mx-3">
                المزيد
            </button>
        </div>
    </div>
</div>
