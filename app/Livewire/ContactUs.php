<?php

namespace App\Livewire;

use App\Models\ContactUsMessage;
use App\Settings\CompanySettings;
use Livewire\Component;

class ContactUs extends Component
{
    public string $name = '';
    public string $email = '';
    public string $message = '';
    public string $backgroundImage = 'images/app/ContactUsBG.svg'; // Update with your actual image path
    public string $decorationImage = 'images/app/ABG.svg'; // Update with your actual decoration image path


    public function submit()
    {
        // Validate the input
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:500',
        ]);

        // Handle the form submission (e.g., send an email, save to database, etc.)
        ContactUsMessage::query()
            ->create([
                'name' => $this->name,
                'email' => $this->email,
                'message' => $this->message,
                'ip_address' => request()->ip(),
            ]);
        // For now, we'll just reset the fields
        $this->reset();

        // Optionally, you can add a success message
        session()->flash('success', 'تم ارسال رسالتك بنجاح');
    }

    public function render()
    {
        return view('livewire.contact-us')
            ->with([
                'settings' => app(CompanySettings::class),
            ]);
    }
}
