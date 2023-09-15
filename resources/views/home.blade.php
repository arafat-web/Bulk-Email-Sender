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
                        <p class="az-dashboard-text">Your web analytics dashboard template.</p>
                    </div>
                    <div class="az-content-header-right">
                        <div class="media">
                            <div class="media-body">
                                <label>Start Date</label>
                                <h6>Oct 10, 2018</h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <div class="media">
                            <div class="media-body">
                                <label>End Date</label>
                                <h6>Oct 23, 2018</h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                    </div>
                </div><!-- az-dashboard-one-title -->

                <div class="az-dashboard-nav">
                </div>

                <div class="row row-sm mg-b-20">
                    <div class="col-lg-4">
                        <div class="card card-dashboard-pageviews">
                            <div class="card-header">
                                <h6 class="card-title">Page Views by Page Title</h6>
                                <p class="card-text">This report is based on 100% of sessions.</p>
                            </div><!-- card-header -->
                            <div class="card-body">

                            </div><!-- card-body -->
                        </div><!-- card -->

                    </div><!-- col -->
                </div><!-- az-content-body -->
            </div>
        </div><!-- az-content -->
    @endsection
