<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Management System</title>

    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">

    <link rel="stylesheet" href="{{asset('custom/style.css')}}">

    <script src="{{'custom/jquery-3.7.1.min.js'}}"></script>


</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-success bg-success">
        <div class="container text-center">
            <h2 class="navbar-brand fw-bold">DATA MANAGEMENT SYSTEM</h2>
        </div>
    </nav>

    <main>
        <div class="container">
            <div class="row mt-2 align-items-end">
                <div class="col-12 col-md-4">
                    <form id="fileUploadForm" enctype="multipart/form-data" class="d-flex">
                        @csrf
                        <div class="form-group">
                            <label for="file" class="form-label fw-bold">Upload CSV File</label>
                            <div class="d-flex">
                                <input type="file" name="file" class="form-control" id="file">
                                <button class="btn btn-outline-primary ms-1" type="submit" id="upload-btn">Upload</button>
                            </div>

                            <div id="file-error-field" class="text-danger fw-bold"></div>

                            <div id="uploadStatus"></div>

                        </div>
                    </form>

                </div>
                <div class="col-12 col-md-8 mt-3 mt-md-0 px-3 px-md-0">
                    <div class="row">
                        <div class="col-8 ">
                            <input type="search" name="search" id="search-field" class="form-control d-none"
                                placeholder="Search here..." autofocus>
                        </div>
                        <div class="col-4  d-flex justify-content-between">

                            <!-- search component-->
                            <x-search />

                            <!-- Sort component-->
                            <x-sort />

                            <!-- filter component -->
                            <x-filter />

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt-3">
            <div class="table-responsive">
                <!-- table component -->
                <x-table />

            </div>
            <div class="row my-3">
                <div class="col-4">
                    <button id="export-xls" class="btn btn-success">Export Excel File</button>
                </div>
                <div id="download-status" class="ms-1 col-8"></div>
            </div>
        </div>
    </main>

    <script src="{{'js/bootstrap.bundle.min.js'}}"></script>
    <script src="{{'custom/welcome.js'}}"></script>
</body>

</html>