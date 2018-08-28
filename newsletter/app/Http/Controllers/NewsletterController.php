<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Newsletter;

class NewsletterController extends Controller
{
    public function create()
    {
        return view('newsletter');
    }

    public function store(Request $request)
    {
        if ( ! Newsletter::isSubscribed($request->email) )
        {
            Newsletter::subscribePending($request->email);
            return redirect('newsletter')->with('success', 'Thanks For Subscribe');
        }
        return redirect('newsletter')->with('failure', 'Sorry! You have already subscribed ');

    }

    public function notify(Request $request){
        //List ID from .env
        $listId = env('MAILCHIMP_LIST_ID');

        //Mailchimp instantiation with Key
        $mailchimp =  Newsletter::getApi();
//        dd($mailchimp);
        //Create a Campaign $mailchimp->campaigns->create($type, $options, $content)
        $result = $mailchimp->post("campaigns", [
            'type' => 'regular',
            'list_id' => $listId,
            'subject' => 'New Article from Scotch',
            'from_email' => 'ahmed180468@eng.zu.edu.eg',
            'from_name' => 'Ahmed fouad',
            'to_name' => 'FoushWare Subscriber'

        ], [
            'html' => "<h1>HI Foushware users</h1>",
//            'text' => strip_tags($request->input('content'))
        ]);


        $response = $mailchimp->getLastResponse();
        $responseObj = json_decode($response['body']);
//        dd($response);
//        dd(json_decode($response['body'])->id);

        $result = $mailchimp->post('campaigns/' . $responseObj->id . '/actions/send');

        dd($result);
//        //Send campaign
//        $mailchimp->campaigns->send($campaign['id']);

//        return response()->json(['status' => 'Success']);
    }
}