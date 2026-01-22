@extends('layouts.app')

@section('title','Posts')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h5>Products</h5>
        
    </div>

    <div class="card-body">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#productModal" id="btnAdd" style="float:right">Add Product</button>
        <button class="btn btn-danger btn-sm" id="bulkDeleteBtn" style="float:right">Delete Selected Product</button><br><br>
        <a class="btn btn-dark" href="{{ route('product.export') }}" style="float:left">Excel</a><br><br>
        <table class="table table-bordered table-striped" id="datatable" width="100%">
            <thead>
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Delete</th>
                <th width="150">Action</th>
            </tr>
            </thead>
            <tbody>
            
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="productModal">
    <div class="modal-dialog modal-lg">
        <form id="productForm">
            @csrf
            <input type="hidden" id="id" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label>Category</label>
                            <select name="category_id" id="category_id" class="form-control">
                                @foreach ($categories as $category)
                                    <option value="{{$category->id }}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label>Price</label>
                            <input type="number" class="form-control" name="price" id="price" required>
                        </div>
                        <div class="col-6">
                            <label>Stock</label>
                            <input type="number" class="form-control" name="stock" id="stock" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label>Description</label>
                            <textarea name="description" id="description" class="form-control" required></textarea>
                        </div>
                        <div class="col-6">
                            <label>Status</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="enabled"
                                    value="1" checked>
                                <label class="form-check-label" for="enabled">Enabled</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="disabled"
                                    value="0">
                                <label class="form-check-label" for="disabled">Disabled</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger" id="deleteBtn" type="button" hidden onclick="deleteProduct()">Delete</button>
                    <button class="btn btn-success" type="submit">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
$(function () {
    table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        'destroy':true,
        "ajax": {
            'url': '/product/create',
        },
        "order": [],
        dom: 'Bfrtip',
        
    });
});

$('#btnAdd').on('click',function($q){
    $('#id').val('')
    $('#deleteBtn').attr('hidden',true)
    $('#productForm')[0].reset();
})



$('#productForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: '/api/product',
            method: 'POST',
            headers: {
                'Authorization': 'Bearer {{ session('api_token') }}'
            },
            data: formData,
            contentType: false, 
            processData: false, 
            success: function (data) {
                $('#productModal').modal('hide');
                table.ajax.reload(); // reload to show updated table
            },
            error: function (xhr) {
                if (xhr.status === 422) { // validation error
                    let errors = xhr.responseJSON.errors;
                    let message = Object.values(errors)[0][0];
                    alert(message); // simple alert
                } else {
                    alert('Something went wrong!');
                }
            }
        });
    });

    function editProduct(id)
    {
        
        $.ajax({
            url: "/api/product/"+id,
            type: "GET",
            headers: {
                'Authorization': 'Bearer {{ session('api_token') }}'
            },
            success: function(response) {
                $('#deleteBtn').attr('hidden',false)
                $('#id').val(response.data.id)
                $('#name').val(response.data.name)
                $('#category_id').val(response.data.category_id)
                $('#price').val(response.data.price)
                $('#stock').val(response.data.stock)
                $('#description').val(response.data.description)
                if(response.data.status == 1){
                    $('#enabled').prop('checked',true)
                    $('#disabled').prop('checked',false)
                }else{
                    $('#enabled').prop('checked',false)
                    $('#disabled').prop('checked',true)
                }
            }
        });
    }

    function deleteProduct()
    {
        $.ajax({
            url: "/api/product/"+$('#id').val(),
            type: "DELETE",
            headers: {
                'Authorization': 'Bearer {{ session('api_token') }}'
            },
            success: function(response) {
                $('#productModal').modal('hide');
                table.ajax.reload();
            }
        });
    }

    $('#bulkDeleteBtn').click(function() {
        let ids = [];
        $('.product-checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if(ids.length > 0) {
            $.ajax({
                url: '/api/product',
                type: 'DELETE',
                headers: {
                    'Authorization': 'Bearer {{ session('api_token') }}'
                },
                data: {
                    ids: ids,
                },
                success: function(response) {
                    $('#productModal').modal('hide');
                    table.ajax.reload();
                }
            });
        } else {
            alert('Please select at least one product.');
        }
    });
</script>
@endpush
