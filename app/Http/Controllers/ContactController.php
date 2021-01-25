<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Address;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::all();
        return view('contacts.index', ['contacts' => $contacts]);
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $req)
    {
        $req->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required','email']
        ]);

        $address = new Address([
            'country' => $req->country,
            'state' => $req->state,
            'city' => $req->city,
            'street' => $req->street,
            'number' => $req->number,
            'additional_info' => $req->additional_info
        ]);

        $contact = new Contact([
            'first_name' => $req->first_name,
            'last_name' => $req->last_name,
            'phone' => $req->phone,
            'email' => $req->email
        ]);

        if ($req->photo) {
            $file = $req->photo->store('public');
            $path = Storage::url($file);
            $contact->fill(['photo' => $path]);
        }

        $address->save();
        $address->contacts()->save($contact);
        $contact->address()->associate($address);

        return redirect(route('contacts.index'));
    }
}
