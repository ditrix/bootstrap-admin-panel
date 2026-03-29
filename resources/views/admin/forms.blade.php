@extends('admin.layouts.sb-admin')

@section('title', 'Forms - SB Admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Forms</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Forms</li>
        </ol>
        <div class="card mb-4">
            <div class="card-header">Vertical form example</div>
            <div class="card-body">
                <form method="get" action="#">
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com" />
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlSelect1" class="form-label">Department</label>
                        <select class="form-select" id="exampleFormControlSelect1">
                            <option>Engineering</option>
                            <option>Support</option>
                            <option>Sales</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">Notes</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1" />
                        <label class="form-check-label" for="exampleCheck1">Send copy to manager</label>
                    </div>
                    <button type="submit" class="btn btn-primary" disabled>Submit</button>
                </form>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Input groups</div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">@</span>
                    <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1" />
                </div>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2" />
                    <span class="input-group-text" id="basic-addon2">@example.com</span>
                </div>
            </div>
        </div>
    </div>
@endsection
