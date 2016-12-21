<?php

namespace App\Http\Controllers;

use App\Package;
use Illuminate\Http\Request;

use App\Http\Requests;

class SubscriptionController extends Controller
{
    /**
     * SubscriptionController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getSubscribe($package_id)
    {
        $package = Package::findOrfail($package_id);

        if (\Auth::user()->subscribed('MEMBERSHIP') || $package->id == getSetting('DEFAULT_PACKAGE_ID')) {
            return view('member.confirm_subscription')->with(compact('package'));
        } else {
            return view('member.card_configuration')->with(compact('package'));
        }
    }

    public function getCard()
    {
        if (\Auth::user()->subscribed('MEMBERSHIP')) {
            return view('member.card_configuration');
        } else {
            return back()->with('error', ' You must have a Stripe Subscription to continue');
        }
    }

    public function postCard(Request $request)
    {
        $creditCardToken = $request->input('creditCardToken');

        $user = \Auth::user();

        $user->updateCard($creditCardToken);

        $user->save();

        return redirect('member/profile')->with('success', 'Your Card Details Updated Successfully');
    }

    public function postSetSubscription(Request $request)
    {
        $creditCardToken = $request->input('creditCardToken');

        $package = Package::findOrfail($request->input('package'));

        $user = \Auth::user();

        $user->newSubscription('MEMBERSHIP', $package->plan)->create($creditCardToken);

        $user->package_id = $package->id;

        $user->save();

        return redirect('member/profile')->with('success', $package->name . ' Package has been selected Successfully');
    }

    public function postSwapSubscription(Request $request)
    {
        $package = Package::findOrFail($request->input('package_id'));

        $user = \Auth::user();
        if ($package->id == getSetting('DEFAULT_PACKAGE_ID')) {
            /**
             * this handle changing package to free package
             */
            if ($user->subscribed('MEMBERSHIP')) {
                $user->subscription('MEMBERSHIP')->cancel();
            }
            $user->package_id = getSetting('DEFAULT_PACKAGE_ID');

            $user->save();

            return redirect('member/profile')->with('success', $package->name . ' Package has been selected Successfully');
        } elseif ($user && $user->subscribed('MEMBERSHIP')) {

            $user->subscription('MEMBERSHIP')->swap($package->plan);

            $user->package_id = $package->id;

            $user->save();

            return redirect('member/profile')->with('success', $package->name . ' Package has been selected Successfully');
        } else {
            return redirect('member/profile')->with('error', 'you are facing some errors');
        }
    }

    public function getInvoices(Request $request)
    {
        $user = \Auth::user();

        $invoices = [];

        if ($user->stripe_id) {
            $invoices = $user->invoices();
        }

        return view('member.invoices')->with(compact('invoices'));
    }

    public function getDownloadInvoice($invoice)
    {
        return \Auth::user()->downloadInvoice($invoice, [
            'vendor' => getSetting('SITE_TITLE'),
            'product' => getSetting('SITE_TITLE'),
        ]);
    }

    public function getCancel()
    {
        return view('member.confirm_cancel_subscription');
    }

    public function deleteCancel()
    {
        $user = \Auth::user();

        $package = Package::findOrfail($user->package->id);

        if ($user->subscribed('MEMBERSHIP')) {
            $user->subscription('MEMBERSHIP')->cancel();
        }

        $user->package_id = 0;

        $user->save();

        return redirect('member/profile')->with('success', $package->name . ' Package has been cancelled Successfully');
    }

}
