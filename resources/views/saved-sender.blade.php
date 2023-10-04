@extends('layouts.app')
@section('title')
    Saved Sender - BES
@endsection

@section('content')
    <div class="alert alert-danger container text-center p-3 h4">
        This section is under construction. Please check back later.
    </div>
    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <div class="az-content-breadcrumb">
                            <span>Email Sender</span>
                            <span>Saved Send</span>
                        </div>
                        <h2 class="az-dashboard-title">Saved Sender</h2>
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

                <div class="mg-b-20">
                    <div class="row row-sm">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="card card-dashboard-pageviews">
                                <h2 class="card-header mb-4 border-bottom">
                                    Email Sender
                                </h2><!-- card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button data-toggle="modal" data-target="#exampleModal"
                                                class="btn btn-primary">Import Excel File</button>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <button class="btn btn-primary">Send Mail</button>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="table-responsive">
                                                <table id="example" class="table table-bordered mg-b-0">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Name</th>
                                                            <th>Position</th>
                                                            <th>Salary</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">1</th>
                                                            <td>Tiger Nixon</td>
                                                            <td>System Architect</td>
                                                            <td>$320,800</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">2</th>
                                                            <td>Garrett Winters</td>
                                                            <td>Accountant</td>
                                                            <td>$170,750</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">3</th>
                                                            <td>Ashton Cox</td>
                                                            <td>Junior Technical Author</td>
                                                            <td>$86,000</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">4</th>
                                                            <td>Cedric Kelly</td>
                                                            <td>Senior Javascript Developer</td>
                                                            <td>$433,060</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">5</th>
                                                            <td>Airi Satou</td>
                                                            <td>Accountant</td>
                                                            <td>$162,700</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div><!-- table-responsive -->
                                        </div>
                                    </div>
                                </div><!-- card-body -->
                            </div><!-- card -->
                        </div><!-- col -->
                    </div>
                </div><!-- az-content-body -->
            </div>
        </div><!-- az-content -->
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Excel Import</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="az-content-label mg-b-5">Import Excel Sheet</div>
                        <p class="mg-b-20 text-danger">Email field should be in the 3rd row.</p>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input"
                                accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" id="customFile">
                            <label class="custom-file-label" for="customFile">Choose excel file</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Import & Save</button>
                    </div>
                </div>
            </div>
        </div>

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
