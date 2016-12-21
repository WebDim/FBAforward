<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Page;
use App\Package;
use App\Feature;

class FrontendController extends Controller
{
    public function index()
    {
        $packages = Package::active()->get();

        $features = Feature::active()->get();

        return view('frontend.welcome')->with(compact('packages', 'features'));
    }

    public function pricing()
    {
        $packages = Package::active()->get();

        $features = Feature::active()->get();

        return view('frontend.pricing')->with(compact('packages', 'features'));
    }

    public function components()
    {
        return view('frontend.components');
    }

    public function contactUsSubmit(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $subject = $request->input('subject');
        $form_message = $request->input('message');

        $to_email = \Config::get('app.contact_email');

        \Mail::send('emails.contact',
            [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'form_message' => $form_message
            ],
            function ($message) use ($to_email) {
                $message->to($to_email, getSetting('SITE_TITLE') . ' Support')->subject('Contact Form Message');
            }
        );

        return redirect('/login')->with(['success' => 'Thanks for contacting us!']);
    }

    public function blog()
    {
        $posts_per_page = getSetting('POSTS_PER_PAGE');

        $posts = Page::published()->post()->paginate($posts_per_page);

        return view('frontend.blog')->with(compact('posts'));
    }

    public function post($slug = '')
    {
        $post = Page::whereSlug($slug)->published()->post()->get()->first();
        if ($post) {
            return view('frontend.post')->with(compact('post'));
        }

        abort(404);
    }

    public function staticPages($slug = '')
    {
        $page = Page::whereSlug($slug)->published()->page()->get()->first();

        if ($page) {
            return view('frontend.page')->with(compact('page'));
        }

        abort(404);
    }
}
