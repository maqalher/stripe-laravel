@extends('layouts.app')

@section('styles')
    <style>
        /* The switch - the box around the slider */
        .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
        }

        /* Hide default HTML checkbox */
        .switch input {
        opacity: 0;
        width: 0;
        height: 0;
        }

        /* The slider */
        .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
        }

        .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
        }

        input:checked + .slider {
        background-color: #2196F3;
        }

        input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
        border-radius: 34px;
        }

        .slider.round:before {
        border-radius: 50%;
        }
    </style>
@endsection

@section('styles')
    <style>
        .StripeElement {
            background-color: white;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid transparent;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }
        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }
        .StripeElement--invalid {
            border-color: #fa755a;
        }
        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
    </style>
@endsection


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">


            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('alert-success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('alert-success') }}
                        </div>
                    @endif
                    @if(count($subscriptions) > 0)
                    <h4><b>Your Subscriptions</b></h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Plan Name</th>
                                    <th scope="col">Subs Name</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Trial Start At</th>
                                    <th scope="col">Trial Ends At</th>
                                    <th>Auto Renew</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $subscription)
                                    <tr>
                                        <td>{{ $subscription->plan->name}}</td>
                                        <td>{{ $subscription->name}}</td>
                                        <td>{{ $subscription->plan->price}}</td>
                                        <td>{{ $subscription->quantity}}</td>
                                        <td>{{ $subscription->trial_ends_at}}</td>
                                        <td>{{ $subscription->created_at}}</td>
                                        <td>
                                            <label class="switch">
                                                @if($subscription->ends_at == null)
                                                    <input type="checkbox" class="switcher" value="{{$subscription->name}}" checked="checked">
                                                    @else
                                                    <input type="checkbox" class="switcher" value="{{$subscription->name}}">    
                                                @endif
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <h4>Are you not subscribed to any plan, </h4>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(document).ready(function(){
            $('.switcher').click(function() {
                var subscriptionName = $(this).val();
                console.log(subscriptionName);
                if($(this).is(':checked')){
                    console.log('no check')
                    $.ajax({
                        url:'{{route("subscription.resume")}}',
                        data: {subscriptionName},
                        type:"GET",
                        success:function(response)
                        {

                        },
                        error:function(response)
                        {

                        }
                    });
                }else{
                    $.ajax({
                        url:'{{route("subscription.cancel")}}',
                        data: {subscriptionName},
                        type:"GET",
                        success:function(response)
                        {
                            console.log(response)
                        },
                        error:function(response)
                        {

                        }
                    });
                }
            });
        });
    </script>
@endsection


                  