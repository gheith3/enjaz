<?php

namespace App\Livewire;

use Livewire\Component;

class ContactUs extends Component
{
    public string $name = '';
    public string $email = '';
    public string $message = '';

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
        return view('livewire.contact-us');
    }
}
