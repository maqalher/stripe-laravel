<?php

namespace App\Http\Controllers;

use App\Models\Plan as ModelsPlan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Plan;
use Laravel\Cashier\Subscription;

class SubscriptionController extends Controller
{
    public function showPlanForm()
    {   
        return view('stripe.plans.create');
    }

    public function savePlan(Request $request)
    {   
        // dd($request->name);
        \Stripe\Stripe::setApiKey(config(('services.stripe.secret')));
        $amount = ($request->amount * 100);

        try{
            // plan de stripe
            $plan = Plan::create([
                'amount' => $amount,
                'currency' => $request->currency,
                'interval' => $request->billing_period,
                'interval_count' => $request->interval_count,
                'product' => [
                    'name' => $request->name
                ]
            ]);

            // modelo Plan
            ModelsPlan::create([
                'plan_id' => $plan->id,
                'name' => $request->name,
                'price' => $plan->amount,
                'billing_method' => $plan->interval,
                'currency' => $plan->currency,
                'interval_count' => $plan->interval_count
            ]);
        }catch(Exception $ex){
            dd($ex->getMessage());
        }

        return "success";
    }

    public function allPlans()
    {
        $basic = ModelsPlan::where('name', 'Basico')->first();
        $professional = ModelsPlan::where('name', 'Plata')->first();
        $enterprise = ModelsPlan::where('name', 'Oro')->first();
        $plans = ModelsPlan::all();
        return view('stripe.plans.showPlans', compact('basic','professional','enterprise', 'plans'));
    }

    public function checkout($planId)
    {
        // dd($plan);
        $plan = ModelsPlan::where('plan_id', $planId)->first();
        if(!$plan){
            return back()->withErrors([
                'message' => 'Unable to locate the plan'
            ]);
        }

        return view('stripe.plans.checkout', [
            'plan' => $plan,
            'intent' => auth()->user()->createSetupIntent(),
        ]);

    }

    public function processPlan(Request $request)
    {
        // dd($request->all());
        $user = auth()->user();
        $user->createOrGetStripeCustomer();
        $paymentMethod = null;
        $paymentMethod = $request->payment_method;

        if($paymentMethod != null){
            $paymentMethod = $user->addPaymentMethod($paymentMethod);
        }

        $plan = $request->plan_id;

        try{
            // dd($paymentMethod->id);

            // Normal
            $user->newSubscription(
                // 'default', 
                $request->plan_id,  // Nombre del Plan
                $plan
            )->create($paymentMethod->id);

            // Tiral day
            // $user->newSubscription(
            //     // 'default', 
            //     $request->plan_id,  // Nombre del Plan
            //     $plan
            // )->trialEndDay(10)->create($paymentMethod->id);


            // $user->newSubscription('default', 'price_monthly')->createAndSendInvoice([], [
            //     'days_until_due' => 30
            // ]);
        }catch (\Exception $e) {
            return back()->withErrors(['message' => 'Error creating subscription. ' . $e->getMessage()]);
        }

        // $request->session()->flash('alect-success', 'You are subscribe to this plan');
        // return to_route('plans.checkout', $plan->plan_id);
       return redirect('home');
    }

    public function allSubscription()
    {   
        // $subscriptions = Subscription::all();
        $subscriptions = Subscription::where('user_id', auth()->id())->get();
        // dd($subscriptions);
        return view('stripe.subscription.index', compact('subscriptions')
        // , [
        //     'intent' => auth()->user()->createSetupIntent(),
        // ]
        );
    }

    public function cancelSubscription(Request $request )
    {   
        $subscriptionName = $request->subscriptionName;
        if($subscriptionName){
            Auth::user()->subscription($subscriptionName)->cancel();
            // $user = auth()->user();
            // $user->subscription($subscriptionName)->cancel();
            return 'subs is canceled';
        }
    }

    public function resumeSubscription(Request $request)
    {
        $subscriptionName = $request->subscriptionName;
        if($subscriptionName){
            Auth::user()->subscription($subscriptionName)->resume();
            // $user = auth()->user();
            // $user->subscription($subscriptionName)->cancel();
            return 'subs is resemed';
        }
    }

    public function pl(){
        // $key = \Stripe\Stripe::setApiKey(config(('services.stripe.secret')));

        $key = config(('services.stripe.secret'));
        $stripe = new \Stripe\StripeClient($key);
        
        $plansraw =  $stripe->plans->all();
        $plans = $plansraw->data;
        dd($plans);

        foreach($plans as $plan){
            echo $plan->id . "<br>";
            // $prod = $stripe->products->retrieve(
            //     $plan->product, []
            // );
            // $plan->product = $prod;
        }
    }

    public function link(){
        
        // \Stripe\Stripe::setApiKey(config(('services.stripe.secret')));
        // $amount = ($request->amount * 100);

        $key = config(('services.stripe.secret'));
        $stripe = new \Stripe\StripeClient($key);

        try{

            $product = $stripe->products->create(
                [
                  'name' => 'Test 2',
                  'default_price_data' => ['unit_amount' => 100 * 100 , 'currency' => 'mxn'],
                  'expand' => ['default_price'],
                ]
            );

            if($product){
          

                $price = $stripe->prices->create(
                    ['currency' => 'mxn', 'unit_amount' => 100 * 100, 'product' => $product->id]
                ); 

                // dd($price);
                if($price){
                    // $link = $stripe->paymentLinks->create(
                    //     ['line_items' => [['price' => $price->id, 'quantity' => 1]]]
                    // );

                    $link = $stripe->paymentLinks->create(
                        [
                          'line_items' => [['price' => $price->id, 'quantity' => 1]],
                          'after_completion' => [
                            'type' => 'redirect',
                            'redirect' => ['url' => 'https://espanol.yahoo.com/?p=us&guccounter=1'],
                          ],
                        ]
                    );

                    dd($link->url);
                }

            }

 
            // dd($product);
        }catch(Exception $ex){
            dd($ex->getMessage());
        }



    }
}
