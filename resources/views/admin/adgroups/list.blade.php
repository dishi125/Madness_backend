@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Application</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Ad Group</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            Ad Group Lists
                        </h4>

                        <div class="action-section">
                            <div class="d-flex">
                                <?php $page_id = \App\Models\ProjectPage::where('route_url','admin.adgroups.list')->pluck('id')->first(); ?>
                                @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                    <button type="button" class="btn btn-primary" id="AddAdgroupBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                {{-- <button class="btn btn-danger" onclick="deleteMultipleAttributes()"><i class="fa fa-trash" aria-hidden="true"></i></button>--}}
                            </div>
                        </div>

                        <div class="custom-tab-1">
                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item adgroup_page_tabs" data-tab="all_adgroup_tab"><a class="nav-link active show" data-toggle="tab" href="">All</a>
                                </li>
                                <li class="nav-item adgroup_page_tabs" data-tab="active_adgroup_tab"><a class="nav-link" data-toggle="tab" href="">Active</a>
                                </li>
                                <li class="nav-item adgroup_page_tabs" data-tab="deactive_adgroup_tab"><a class="nav-link" data-toggle="tab" href="">Deactive</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="all_adgroup_tab">
                            <table id="adgroups" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Category Page</th>
                                    <th>Ad Group Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Category Page</th>
                                    <th>Ad Group Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteAdGroupModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Ad Group</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Ad Group?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemoveAdGroupSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @include('admin.adgroups.adgroup_js')
@endsection
