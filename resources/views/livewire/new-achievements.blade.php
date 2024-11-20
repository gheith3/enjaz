<div>
    <div class="container">
        <h4>
            أحدث الإنجازات
        </h4>
        <div class="row">
            @for($i = 1; $i <= 3; $i++)
                <div class="col-md-4">
                    <div class="card m-3 shadow-sm">
                        <div class="card-body">
                            <img src="{{asset("images/swiper/teddy{$i}.png")}}"
                                 class="rounded"
                                 style="width: 100%; height: 250px"
                                 alt="">
                            <div class="d-flex gap-1 mt-3">
                                <h6><span class="badge text-bg-secondary">الثقافة</span></h6>
                                <h6><span class="badge text-bg-secondary">الأدب</span></h6>
                            </div>
                            <div class="mt-3">
                                <p class="text-black-50">2024/09/01</p>
                                <p>جائزة الأدب العربي</p>
                                <p class="mt-1">
                                    حصل مشروع «إزالة الفينول باستخدام كربون نشط مصنع من مخلفات طبيعية » من جامعة السلطان قابوس على إنجازين مشرفين على المستوى الإقليمي والدولي جاء كالآتي: الميدالية الفضية …
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            @endfor
        </div>
        <div class="d-flex justify-content-end mt-3">
            <button class="btn btn-primary mx-3">
                المزيد
            </button>
        </div>
    </div>
</div>
