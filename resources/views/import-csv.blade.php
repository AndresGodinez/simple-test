@extends('layouts/base')
@section('content')
    <div class="container mt-3">
        <div class="row">
            <div class="card  col-md-6 p-0">
                <div class="card-body">
                    <form action="{{ route('processFile') }}"
                          method="post"
                          enctype="multipart/form-data"
                    >
                        <div class="form-group">
                            @csrf
                            <label for="csv_file">Subir Archivo</label>
                            <input type="file"
                                   id="csv_file"
                                   name="csv_file"
                                   class="form-control-file"
                                   required
                            >
                        </div>
                        <div class="form-group mt-3">
                            <input type="submit" class="btn btn-info" value="Procesar">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
