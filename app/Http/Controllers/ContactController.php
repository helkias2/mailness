<?php

namespace App\Http\Controllers;

use App\Field;
use App\Lists;
use App\Contact;
use Illuminate\Http\Request;
use App\Http\Requests\ContactStoreRequest;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Lists $lists)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Lists $lists)
    {
        return view('contacts.create', [ 'list' => $lists ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Lists $lists, ContactStoreRequest $request)
    {
        // @todo refactor this with update
        $contact = Contact::where('email', $request->email)->where('list_id', $lists->id)->first();
        if(! isset($contact) ) {
            $contact = new Contact();
            $contact->email = $request->email;
            $contact->list_id = $lists->id;
            $contact->save();

            if($request->fields) {
                foreach($request->fields as $key => $value) {
                    if($value) {
                        $field = Field::find($key);
                        $contact->fields()->attach($field, [ 'value' => $value ]);
                    }
                }
            }
        }
        
        return redirect()->route('lists.show', $lists->id); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Lists $lists, Contact $contact)
    {
        return view('contacts.show', [ 'contact' => $contact ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Lists $lists, Contact $contact)
    {
        return view('contacts.edit', [ 'list' => $lists, 'contact' => $contact ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(ContactStoreRequest $request, Lists $lists, Contact $contact)
    {
        // @todo refactor this and save method
        $contact_check = Contact::where('email', $request->email)->where('list_id', $lists->id)->first();
        if(! isset($contact_check) ) {
            $contact->fields()->detach();
            if($request->fields) {
                foreach($request->fields as $key => $value) {
                    if($value) {
                        $field = Field::find($key);
                        $contact->fields()->attach($field, [ 'value' => $value ]);
                    }
                }
            }
            $contact->email = $request->email;
            $contact->save();
        }

        return view('contacts.show', [ 'list' => $lists, 'contact' => $contact ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('lists.index');
    }
}
