@extends('layouts.app')
@section('title')
One Time Email Sender - BES
@endsection
@section('content')
<div class="az-content az-content-dashboard">
    <div class="container">
        <div class="az-content-body">
            <div class="az-dashboard-one-title">

                <div>
                    <div class="az-content-breadcrumb">
                        <span>Email Sender</span>
                        <span>One Time Sender</span>
                    </div>
                    <h2 class="az-dashboard-title">One Time Sender</h2>
                </div>
                <div class="az-content-header-right">
                    <div class="media">
                        <div class="media-body">
                            <label>Time</label>
                            <h6>Oct 10, 2018</h6>
                        </div><!-- media-body -->
                    </div><!-- media -->
                    <div class="media">
                        <div class="media-body">
                            <label>Date</label>
                            <h6>Oct 23, 2018</h6>
                        </div><!-- media-body -->
                    </div><!-- media -->
                </div>
            </div><!-- az-dashboard-one-title -->

            <div class="az-dashboard-nav">
            </div>
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
            @endif

            @if(session()->has('error'))
            <div class="alert alert-danger">
                {{ session()->get('error') }}
            </div>
            @endif
            <div class="mg-b-20">
                <div class="row row-sm">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="card card-dashboard-pageviews">
                            <h2 class="card-header mb-4 border-bottom">
                                Email Sender
                            </h2><!-- card-header -->
                            <div class="card-body">
                                <form action="{{ route('ots.import') }}" method="POST" name="importform" enctype="multipart/form-data">
                                    @csrf
                                    <div class="az-content-label mg-b-5">Import Excel Sheet</div>
                                    <p class="mg-b-20 text-danger">Email field should be in the 3rd row.</p>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" accept=".xlsx, .xls, .csv" F id="customFile" name="file">
                                        <label class="custom-file-label" for="customFile">Choose excel file</label>
                                    </div>
                                    <div class="mt-4 form-group">
                                        <div class="az-content-label mg-b-5">Subject</div>
                                        <p class="mg-b-20 text-danger">Email field should be in the 3rd row.</p>
                                        <input name="subject" class="form-control" placeholder="Lorem ipsum dolor sit, amet consectetur adipisicing." type="text" spellcheck="false">
                                    </div>
                                    <div class="mt-4 form-group">
                                        <div class="az-content-label mg-b-5">Body</div>
                                        <p class="mg-b-20 text-danger">Email field should be in the 3rd row.</p>
                                        <textarea id="mytxtarea" name="body" class="form-control" placeholder="Lorem ipsum, dolor sit amet consectetur adipisicing elit. Porro nulla culpa temporibus eveniet aliquid facilis veritatis aliquam facere minus fugit!" spellcheck="false" data-ms-editor="true" cols="30" rows="10"></textarea>
                                    </div>
                                    <div class="mt-4 form-group">
                                        <button type="submit" class="btn btn-primary">Import & Send</button>
                                    </div>
                                </form>
                            </div><!-- card-body -->
                        </div><!-- card -->
                    </div><!-- col -->
                </div>
            </div><!-- az-content-body -->
        </div>
    </div><!-- az-content -->
    <script>
        $(document).ready(function() {
            $('#mytxtarea').richText();
        });
        </script>
    @endsection
