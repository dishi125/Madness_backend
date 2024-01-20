@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Settings</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        {{--<h4 class="card-title">
                            Settings List
                        </h4>--}}
                        <div class="col-lg-12">

                        <div class="table-responsive">
                            <table id="" class="table table-striped table-bordered customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th colspan="3"><h4 class="text-white mb-0">Settings</h4></th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr>
                                    <th style="width: 50%">User Discount Percentage(%)</th>
                                    <td><span id="UserDiscountPerVal">{{ $Settings->user_discount_percentage." %" }}</span></td>
                                    <td class="text-right">
                                        @if($canWrite == true)
                                            <button id="editUserDiscountPerBtn" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#UserDiscountPerModal">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 50%">Shipping Cost</th>
                                    <td><span id="ShippingCostVal"><i class="fa fa-inr" aria-hidden="true"></i> {{ $Settings->shipping_cost }}</span></td>
                                    <td class="text-right">
                                        @if($canWrite == true)
                                            <button id="editShippingCostBtn" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#ShippingCostModal">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 50%">Premium User Membership Fee</th>
                                    <td><span id="PremiumUserMembershipFeeVal"><i class="fa fa-inr" aria-hidden="true"></i> {{ $Settings->premium_user_membership_fee }}</span></td>
                                    <td class="text-right">
                                        @if($canWrite == true)
                                            <button id="editPremiumUserMembershipFeeBtn" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#PremiumUserMembershipFeeModal">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 50%">Minimum Order Amount</th>
                                    <td><span id="MinOrderAmountVal"><i class="fa fa-inr" aria-hidden="true"></i> {{ $Settings->min_order_amount }}</span></td>
                                    <td class="text-right">
                                        @if($canWrite == true)
                                            <button id="editMinOrderAmountBtn" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#MinOrderAmountModal">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="UserDiscountPerModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="UserDiscountPerForm" method="post">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Update User Discount Percentage</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <div id="attr-cover-spin" class="cover-spin"></div>
                    <div class="form-group">
                        <label class="col-form-label" for="user_discount_percentage">User Discount Percentage <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control input-flat" id="user_discount_percentage" name="user_discount_percentage" placeholder="">
                        <div id="user_discount_percentage-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveUserDiscountPerBtn">Save <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ShippingCostModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="ShippingCostForm" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h5 class="modal-title">Update Shipping Cost</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        <div class="form-group">
                            <label class="col-form-label" for="ShippingCost">Shipping Cost <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="shipping_cost" name="shipping_cost" placeholder="">
                            <div id="shipping_cost-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveShippingCostBtn">Save <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="PremiumUserMembershipFeeModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="PremiumUserMembershipFeeForm" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h5 class="modal-title">Update Premium User Membership Fee</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        <div class="form-group">
                            <label class="col-form-label" for="MembershipFee">Premium User Membership Fee <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="premium_user_membership_fee" name="premium_user_membership_fee" placeholder="">
                            <div id="premium_user_membership_fee-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="savePremiumUserMembershipFeeBtn">Save <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="MinOrderAmountModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="MinOrderAmountForm" method="post">
                    {{ csrf_field() }}
                    <div class="modal-header">
                        <h5 class="modal-title">Update Minimum Order Amount</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        <div class="form-group">
                            <label class="col-form-label" for="min_order_amount">Minimum Order Amount <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="min_order_amount" name="min_order_amount" placeholder="">
                            <div id="min_order_amount-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveMinOrderAmountBtn">Save <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- settings JS start -->
<script type="text/javascript">
    $('body').on('click', '#editUserDiscountPerBtn', function () {
        $.get("{{ url('admin/settings/user_discount_percentage/edit') }}", function (data) {
            $('#user_discount_percentage').val(data.user_discount_percentage);
        })
    });

    $('body').on('click', '#editShippingCostBtn', function () {
        $.get("{{ url('admin/settings/shipping_cost/edit') }}", function (data) {
            $('#shipping_cost').val(data.shipping_cost);
        })
    });

    $('body').on('click', '#editPremiumUserMembershipFeeBtn', function () {
        $.get("{{ url('admin/settings/premium_user_membership_fee/edit') }}", function (data) {
            $('#premium_user_membership_fee').val(data.premium_user_membership_fee);
        })
    });

    $('body').on('click', '#saveUserDiscountPerBtn', function () {
        $('#saveUserDiscountPerBtn').prop('disabled',true);
        $('#saveUserDiscountPerBtn').find('.loadericonfa').show();
        var formData = new FormData($("#UserDiscountPerForm")[0]);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/updateUserDiscountPercentage') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if(res.status == 'failed'){
                    $('#saveUserDiscountPerBtn').prop('disabled',false);
                    $('#saveUserDiscountPerBtn').find('.loadericonfa').hide();
                    if (res.errors.user_discount_percentage) {
                        $('#user_discount_percentage-error').show().text(res.errors.user_discount_percentage);
                    } else {
                        $('#user_discount_percentage-error').hide();
                    }
                }

                if(res.status == 200){
                    $("#UserDiscountPerModal").modal('hide');
                    $('#saveUserDiscountPerBtn').prop('disabled',false);
                    $('#saveUserDiscountPerBtn').find('.loadericonfa').hide();
                    $("#UserDiscountPerVal").html(res.user_discount_percentage + " %");
                    toastr.success("Settings Updated",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#UserDiscountPerModal").modal('hide');
                    $('#saveUserDiscountPerBtn').prop('disabled',false);
                    $('#saveUserDiscountPerBtn').find('.loadericonfa').hide();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#UserDiscountPerModal").modal('hide');
                $('#saveUserDiscountPerBtn').prop('disabled',false);
                $('#saveUserDiscountPerBtn').find('.loadericonfa').hide();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    $('body').on('click', '#saveShippingCostBtn', function () {
        $('#saveShippingCostBtn').prop('disabled',true);
        $('#saveShippingCostBtn').find('.loadericonfa').show();
        var formData = new FormData($("#ShippingCostForm")[0]);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/updateShippingCost') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if(res.status == 'failed'){
                    $('#saveShippingCostBtn').prop('disabled',false);
                    $('#saveShippingCostBtn').find('.loadericonfa').hide();
                    if (res.errors.shipping_cost) {
                        $('#shipping_cost-error').show().text(res.errors.shipping_cost);
                    } else {
                        $('#shipping_cost-error').hide();
                    }
                }

                if(res.status == 200){
                    $("#ShippingCostModal").modal('hide');
                    $('#saveShippingCostBtn').prop('disabled',false);
                    $('#saveShippingCostBtn').find('.loadericonfa').hide();
                    $("#ShippingCostVal").html('<i class="fa fa-inr" aria-hidden="true"></i> ' + res.shipping_cost);
                    toastr.success("Settings Updated",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#ShippingCostModal").modal('hide');
                    $('#saveShippingCostBtn').prop('disabled',false);
                    $('#saveShippingCostBtn').find('.loadericonfa').hide();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#ShippingCostModal").modal('hide');
                $('#saveShippingCostBtn').prop('disabled',false);
                $('#saveShippingCostBtn').find('.loadericonfa').hide();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    $('body').on('click', '#savePremiumUserMembershipFeeBtn', function () {
        $('#savePremiumUserMembershipFeeBtn').prop('disabled',true);
        $('#savePremiumUserMembershipFeeBtn').find('.loadericonfa').show();
        var formData = new FormData($("#PremiumUserMembershipFeeForm")[0]);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/updatePremiumUserMembershipFee') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if(res.status == 'failed'){
                    $('#savePremiumUserMembershipFeeBtn').prop('disabled',false);
                    $('#savePremiumUserMembershipFeeBtn').find('.loadericonfa').hide();
                    if (res.errors.premium_user_membership_fee) {
                        $('#premium_user_membership_fee-error').show().text(res.errors.premium_user_membership_fee);
                    } else {
                        $('#premium_user_membership_fee-error').hide();
                    }
                }

                if(res.status == 200){
                    $("#PremiumUserMembershipFeeModal").modal('hide');
                    $('#savePremiumUserMembershipFeeBtn').prop('disabled',false);
                    $('#savePremiumUserMembershipFeeBtn').find('.loadericonfa').hide();
                    $("#PremiumUserMembershipFeeVal").html('<i class="fa fa-inr" aria-hidden="true"></i> ' + res.premium_user_membership_fee);
                    toastr.success("Settings Updated",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#PremiumUserMembershipFeeModal").modal('hide');
                    $('#savePremiumUserMembershipFeeBtn').prop('disabled',false);
                    $('#savePremiumUserMembershipFeeBtn').find('.loadericonfa').hide();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#PremiumUserMembershipFeeModal").modal('hide');
                $('#savePremiumUserMembershipFeeBtn').prop('disabled',false);
                $('#savePremiumUserMembershipFeeBtn').find('.loadericonfa').hide();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    $('#UserDiscountPerModal').on('shown.bs.modal', function (e) {
        $("#user_discount_percentage").focus();
    });

    $('#ShippingCostModal').on('shown.bs.modal', function (e) {
        $("#shipping_cost").focus();
    });

    $('#PremiumUserMembershipFeeModal').on('shown.bs.modal', function (e) {
        $("#premium_user_membership_fee").focus();
    });

    $('#UserDiscountPerModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('#user_discount_percentage-error').html("");
    });

    $('#ShippingCostModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('#shipping_cost-error').html("");
    });

    $('#PremiumUserMembershipFeeModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('#premium_user_membership_fee-error').html("");
    });

    $('body').on('click', '#editMinOrderAmountBtn', function () {
        $.get("{{ url('admin/settings/min_order_amount/edit') }}", function (data) {
            $('#min_order_amount').val(data.min_order_amount);
        })
    });

    $('body').on('click', '#saveMinOrderAmountBtn', function () {
        $('#saveMinOrderAmountBtn').prop('disabled',true);
        $('#saveMinOrderAmountBtn').find('.loadericonfa').show();
        var formData = new FormData($("#MinOrderAmountForm")[0]);

        $.ajax({
            type: 'POST',
            url: "{{ url('admin/updateMinOrderAmount') }}",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if(res.status == 'failed'){
                    $('#saveMinOrderAmountBtn').prop('disabled',false);
                    $('#saveMinOrderAmountBtn').find('.loadericonfa').hide();
                    if (res.errors.min_order_amount) {
                        $('#min_order_amount-error').show().text(res.errors.min_order_amount);
                    } else {
                        $('#min_order_amount-error').hide();
                    }
                }

                if(res.status == 200){
                    $("#MinOrderAmountModal").modal('hide');
                    $('#saveMinOrderAmountBtn').prop('disabled',false);
                    $('#saveMinOrderAmountBtn').find('.loadericonfa').hide();
                    $("#MinOrderAmountVal").html('<i class="fa fa-inr" aria-hidden="true"></i> ' + res.min_order_amount);
                    toastr.success("Settings Updated",'Success',{timeOut: 5000});
                }

                if(res.status == 400){
                    $("#MinOrderAmountModal").modal('hide');
                    $('#saveMinOrderAmountBtn').prop('disabled',false);
                    $('#saveMinOrderAmountBtn').find('.loadericonfa').hide();
                    toastr.error("Please try again",'Error',{timeOut: 5000});
                }
            },
            error: function (data) {
                $("#MinOrderAmountModal").modal('hide');
                $('#saveMinOrderAmountBtn').prop('disabled',false);
                $('#saveMinOrderAmountBtn').find('.loadericonfa').hide();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    });

    $('#MinOrderAmountModal').on('shown.bs.modal', function (e) {
        $("#min_order_amount").focus();
    });

    $('#MinOrderAmountModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $('#min_order_amount-error').html("");
    });
</script>
<!-- settings JS end -->
@endsection
