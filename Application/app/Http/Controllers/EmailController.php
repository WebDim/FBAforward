<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class EmailController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
    }
    public function  send(Request $request)
    {
        $title = $request->input('title');
        $content = $request->input('content');

        Mail::send('emails.send', ['title' => $title, 'content' => $content], function ($message)
        {

            $message->from('megha.n.jogi@gmail.com', 'testing');

            $message->to('parmarriddhi21@yahoo.com');

        });

        return response()->json(['message' => 'Request completed']);
    }
    public function getnotify()
    {
        return view('example');
    }
    public function notify(Request $request){

        //List ID from .env
        $listId = env('MAILCHIMP_LIST_ID');

        //Mailchimp instantiation with Key
        $mailchimp = new \Mailchimp(env('MAILCHIMP_KEY'));

        //Create a Campaign  $mailchimp->campaigns->create($type, $options, $content)

        $campaign = $mailchimp->campaigns->create('regular', [
            'list_id' => $listId,
            'subject' => 'New Article from Scotch',
            'from_email' => 'webdimensionsindia@gmail.com',
            'from_name' => 'test',
            'to_name' => 'test'

        ], [
            'html' => $request->input('content'),
            'text' => strip_tags($request->input('content'))
        ]);

        //Send campaign
        $mailchimp->campaigns->send($campaign['id']);

        return response()->json(['status' => 'Success']);
    }
}
