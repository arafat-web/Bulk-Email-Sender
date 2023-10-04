@extends('layouts.app')
@section('title')
    One Time Email Sender - BES
@endsection
@section('content')
    <style>
        .richText-toolbar ul {
            margin: 0;
            padding: 0;
        }

        .richText-toolbar a {
            color: black;
        }
    </style>
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
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session()->get('message') }}
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
                                    <form action="{{ route('ots.import') }}" method="POST" name="importform"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="az-content-label mg-b-5">Import Excel Sheet</div>
                                        <p class="mg-b-20 text-info">Email field should be in the 3rd row.</p>
                                        <div class="custom-file">
                                            <input id="file" type="file" class="custom-file-input"
                                                accept=".xlsx, .xls, .csv" F id="customFile" name="file">
                                            <label class="custom-file-label" for="customFile">Choose excel file</label>
                                            @error('file')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mt-4 form-group">
                                            <div class="az-content-label mg-b-5 mb-3">Subject</div>
                                            <input id="subject" name="subject" class="form-control"
                                                placeholder="Lorem ipsum dolor sit, amet consectetur adipisicing."
                                                type="text" spellcheck="false">

                                            @error('subject')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mt-4 form-group mytxtarea">
                                            <div class="az-content-label mg-b-5 mb-3">Body</div>
                                            <textarea id="mytxtarea" name="body" class="form-control"
                                                placeholder="Lorem ipsum, dolor sit amet consectetur adipisicing elit. Porro nulla culpa temporibus eveniet aliquid facilis veritatis aliquam facere minus fugit!"
                                                spellcheck="false" data-ms-editor="true" cols="30" rows="10"></textarea>

                                            @error('body')
                                                <div class="alert alert-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mt-4 form-group">
                                            <button type="submit" id="email_sending" class="btn btn-primary">Import &
                                                Send</button>
                                        </div>
                                    </form>
                                </div><!-- card-body -->
                            </div><!-- card -->
                        </div><!-- col -->
                    </div>
                </div><!-- az-content-body -->
            </div>
        </div><!-- az-content -->

        <!-- Modal for Instructions -->
        <div class="modal fade" id="instructionModal" tabindex="-1" role="dialog" aria-labelledby="instructionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="instructionModalLabel">Instructions</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>1. Email field should be in the 3rd row.</p>
                        <p>2. Add Subject and Body input fields. Body input field is editable as HTML.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="understoodBtn">I Understand</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#mytxtarea').richText();

                $('#file').change(function() {
                    // var i = $(this).prev('label').clone();
                    // var file = $('#file')[0].files[0].name;
                    // $(this).prev('label').text(file);
                });
                $('#email_sending').click(function() {
                    var file = $('#file')[0].files[0].name;
                    var subject = $('#subject').val();
                    var body = $('#mytxtarea').val();
                    if (file == '') {
                        alert('Please select excel file');
                        return false;
                    } else if (subject == '') {
                        alert('Please enter subject');
                        return false;
                    } else if (body == '') {
                        alert('Please enter body');
                        return false;
                    } else {
                        Swal.fire({
                            title: 'Sending Emails',
                            html: 'Please wait while we send your emails. This process may take a moment to complete.',
                            timer: null, // Set timer to null for infinite duration
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    }
                });

                if (!localStorage.getItem('instructionUnderstood')) {
                    $('#instructionModal').modal('show');
                }
                $('#understoodBtn').click(function() {
                    localStorage.setItem('instructionUnderstood', 'true');
                    $('#instructionModal').modal('hide');
                });
            });


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
