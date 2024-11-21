<?php

namespace App\Livewire;

use Livewire\Component;

class AboutSectionMessages extends Component
{
    public array $messages = [
        [
            'icon' => 'images/app/vision.svg',
            'title' => 'الرؤية',
            'description' => '  أن نكون المِنَصَّةَ الأساسية والمرجعَ الأولَ لتوثيق إنجازات الشباب العُماني في سلطنة عُمان'
        ],
        [
            'icon' => 'images/app/msg.svg',
            'title' => 'الرسالة',
            'description' => 'توثيقُ الإنجازاتِ العُمانية للأفراد والمؤسسات، وتسليطُ الضَّوْءِ على إبداعاتهم المتميزة وتجاربهم الناجحة، ونشرُ ثقافة الإنجاز والابتكار في بيئة مثالية لإلهامِ الأجيالِ القادمة'
        ],
        [
            'icon' => 'images/app/values.svg',
            'title' => 'القيم',
            'description' => ' انظر إلى الأشياءِ بشكل مختلف عن الآخرين حتى تصل إلى نقطة الإلهام؛ فالقليل من التفكير يولد الكثير من الشَّغَفِ، لا تكتفِ بالمشاهدة، كُن فضوليًّا، وحاوِلِ البحثَ وتعلَّمْ ما يدفعك للابتكار؛ هكذا يجب أن يكونَ تفكيرُك حتى تجد نفسَك في مراتب الإنجاز'
        ]
    ];

    public function render()
    {
        return view('livewire.about-section-messages');
    }
}