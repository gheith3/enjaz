<?php

namespace App\Livewire;

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
        // For now, we'll just reset the fields
        $this->reset();

        // Optionally, you can add a success message
        session()->flash('success', 'Message sent successfully!');
    }

    public function render()
    {
        return view('livewire.contact-us')
            ->with([
                'socialMedia' => [
                    'x' => [
                        'title' => 'x',
                        'image' => asset('images/app/X.png'),
                        'link' => 'https://www.x.com',
                        'account' => '@some'
                    ],
                    'instagram' => [
                        'title' => 'instagram',
                        'image' => asset('images/app/IG.svg'),
                        'link' => 'https://www.instagram.com',
                        'account' => '@thing'
                    ],
                    'linkedin' => [
                        'title' => 'linkedIn',
                        'image' => asset('images/app/LI.png'),
                        'link' => 'https://www.linkedin.com',
                        'account' => '@here'
                    ]
                ]
            ])
            ->layout('components.layouts.app');
    }
}
