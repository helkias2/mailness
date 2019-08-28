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
        return view('contacts.index', [ 'list' => $lists ]);
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
        $contact = Contact::where('email', $request->email)->where('list_id', $lists->id)->first();
        if(! isset($contact) ) {
            $contact = new Contact();
            $contact->email = $request->email;
            $contact->list_id = $lists->id;
            $contact->save();
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
        $conditions = [
            [ 'key' => 3, 'value' => 'Travnik', 'condition' => '=' ],
            [ 'key' => 2, 'value' => 'Emir', 'condition' => '=' ]
        ];

        $contacts = Contact::where('list_id', $lists->id);

        foreach($conditions as $condition) {
            $contacts->whereHas('fields', function($query) use ($condition) {
                $query->where('field_id',$condition['key']);
                $query->where('value', $condition['condition'] , $condition['value']);
            });
        }

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
    public function update(Request $request, Lists $lists, Contact $contact)
    {
        // @todo validation
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