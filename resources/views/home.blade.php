@extends('layouts.app')
@section('title')
    Dashboard - BES
@endsection
@section('content')
    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">Hi, welcome back!</h2>
                        <p class="az-dashboard-text">Your bulk email sender dashboard.</p>
                    </div>
                    <div class="az-content-header-right">
                        <div class="media">
                            <div class="media-body">
                                <label>Time</label>
                                <h6 id="currentTime"></h6>
                            </div>
                        </div>
                        <div class="media">
                            <div class="media-body">
                                <label>Date</label>
                                <h6 id="currentDate"></h6>
                            </div>
                        </div>
                    </div>
                </div><!-- az-dashboard-one-title -->

                <div class="az-dashboard-nav">
                </div>

                <div class="row row-sm mg-b-20">
                    <div class="col-lg-4">
                        <div class="card card-dashboard-pageviews">
                            <div class="card-header">
                                <h6 class="card-title">Total Operations</h6>
                                <p class="card-text"> {{ $total_time }} Times </p>
                            </div>
                            <div class="card-body">

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card card-dashboard-pageviews">
                            <div class="card-header">
                                <h6 class="card-title">Total Email Sent</h6>
                                <p class="card-text"> {{ $total_sent }} Emails </p>
                            </div>
                            <div class="card-body">

                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card card-dashboard-pageviews">
                            <div class="card-header">
                                <h6 class="card-title">Total User</h6>
                                <p class="card-text"> {{ $total_user }} Users </p>
                            </div>
                            <div class="card-body">

                            </div>
                        </div>
                    </div>
                </div><!-- az-content-body -->
                <div class="row row-sm mg-b-20">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                               <h5>Recent Operations</h5> 
                            </div>
                            <div class="card-body pb-0 pl-0 pr-0">
                                <table class="table table-stripped mb-0 text-center">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Emails</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                         $i = 1;   
                                        @endphp
                                        @foreach ($operations as $operation)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $operation->total_email_address }}</td>
                                                <td>{{ $operation->created_at->format('M d, Y') }}</td>
                                                <td>{{ $operation->created_at->format('h:i A') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- az-content -->

        <script>
            function updateDateTime() {
                var currentDate = new Date().toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                var currentTime = new Date().toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                document.getElementById('currentDate').innerText = currentDate;
                document.getElementById('currentTime').innerText = currentTime;
            }
            updateDateTime();
            setInterval(updateDateTime, 1000);
        </script>
    @endsection
