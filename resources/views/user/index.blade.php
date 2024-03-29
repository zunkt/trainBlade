@extends('main.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css"
      integrity="sha256-2XFplPlrFClt0bIdPgpz8H7ojnk10H69xRqd9+uTShA=" crossorigin="anonymous"/>

@section('content')
    <div class="container mt-3 mb-4">
        <div class="mt-4 mt-lg-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="user-dashboard-info-box table-responsive mb-0 p-4 shadow-sm">
                        <div class="justify-content-between" style="display: flex">
                            <a href="{{ route('auth.logout') }}" type="button">Logout</a>
                            @if (auth()->user()->hasPermission())
                                <button type="button" id="add-user">ADD USER</button>
                            @endif
                        </div>
                        <table class="table manage-candidates-top mb-0">
                            <thead>
                            <tr class="text-white">
                                <th>Name</th>
                                <th class="text-center">Email</th>
                                <th class="action text-right">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr class="text-white candidates-list">
                                    <td class="title">
                                        <div class="thumb">
                                            <img class="img-fluid"
                                                 src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="">
                                        </div>
                                        <div class="candidate-list-details">
                                            <div class="candidate-list-info">
                                                <div class="candidate-list-title">
                                                    <h5 class="mb-0"><a href="#">{{ $user->name }}</a></h5>
                                                </div>
                                                <div class="candidate-list-option">
                                                    <ul class="list-unstyled">
                                                        <li><i class="fas fa-filter pr-1"></i>Information Technology
                                                        </li>
                                                        <li><i class="fas fa-map-marker-alt pr-1"></i>Rolling Meadows,
                                                            IL 60008
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="candidate-list-favourite-time text-center">
                                        <a class="candidate-list-favourite order-2 text-danger" href="#"><i
                                                class="fas fa-heart"></i></a>
                                        <span class="candidate-list-time order-1">{{ $user->email }}</span>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0 d-flex justify-content-end">
                                            @if ($user->id == auth()->user()->id)
                                                <li><a href="{{ route('user.show', $user->id) }}" class="text-info"
                                                       data-toggle="tooltip" title="" data-original-title="Edit"><i
                                                            class="fas fa-pencil-alt"></i></a></li>
                                            @elseif (auth()->user()->hasPermission())
                                                <li><a href="{{ route('user.show', $user->id) }}" class="text-info"
                                                       data-toggle="tooltip" title="" data-original-title="Edit"><i
                                                            class="fas fa-pencil-alt"></i></a></li>
                                            @endif

                                            @if (auth()->user()->hasPermission())
                                                @if ($user->id != auth()->user()->id)
                                                    <li><a href="#" data-id="{{ $user->id }}" id="delete"
                                                           class="text-danger" data-toggle="tooltip"
                                                           title="" data-original-title="Delete"><i
                                                                class="far fa-trash-alt"></i></a></li>
                                                @endif
                                            @endif
                                        </ul>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $users->links('pagination.default', ['user' => $user]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script type="text/javascript">
    $(document).ready()
    {
        $(document).on('click', '#add-user', function () {
            window.location.href = '{{ route('user.add') }}'
        })

        $(document).on('click', '#delete', function () {
            var that = $(this);
            var idDelete = that.attr('data-id');

            var url = '{{ route('user.delete') }}'

            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: idDelete
                },
                success: function (response) {
                    console.log(response)
                    if (response.status == true) {
                        window.location.href = '{{ route('user.index') }}';
                    }
                }
            });
        })
    }
</script>

<style>
    /* Made with love by Mutiullah Samim*/

    @import url('https://fonts.googleapis.com/css?family=Numans');

    html, body {
        background-image: url('http://getwallpapers.com/wallpaper/full/a/5/d/544750.jpg');
        background-size: cover;
        background-repeat: no-repeat;
        height: 100%;
        font-family: 'Numans', sans-serif;
    }

    .container {
        height: 100%;
        align-content: center;
    }

    .card {
        height: 370px;
        margin-top: auto;
        margin-bottom: auto;
        width: 800px;
        background-color: rgba(0, 0, 0, 0.5) !important;
    }

    .social_icon span {
        font-size: 60px;
        margin-left: 10px;
        color: #FFC312;
    }

    .social_icon span:hover {
        color: white;
        cursor: pointer;
    }

    .card-header h3 {
        color: white;
    }

    .social_icon {
        position: absolute;
        right: 20px;
        top: -45px;
    }

    .input-group-prepend span {
        width: 50px;
        background-color: #FFC312;
        color: black;
        border: 0 !important;
    }

    input:focus {
        outline: 0 0 0 0 !important;
        box-shadow: 0 0 0 0 !important;

    }

    .remember {
        color: white;
    }

    .remember input {
        width: 20px;
        height: 20px;
        margin-left: 15px;
        margin-right: 5px;
    }

    .login_btn {
        color: black;
        background-color: #FFC312;
        width: 100px;
    }

    .login_btn:hover {
        color: black;
        background-color: white;
    }

    .links {
        color: white;
    }

    .links a {
        margin-left: 4px;
    }

    .user-dashboard-info-box .candidates-list .thumb {
        margin-right: 20px;
    }

    .user-dashboard-info-box .candidates-list .thumb img {
        width: 80px;
        height: 80px;
        -o-object-fit: cover;
        object-fit: cover;
        overflow: hidden;
        border-radius: 50%;
    }

    .user-dashboard-info-box .title {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        padding: 30px 0;
    }

    .user-dashboard-info-box .candidates-list td {
        vertical-align: middle;
    }

    .user-dashboard-info-box td li {
        margin: 0 4px;
    }

    .user-dashboard-info-box .table thead th {
        border-bottom: none;
    }

    .table.manage-candidates-top th {
        border: 0;
    }

    .user-dashboard-info-box .candidate-list-favourite-time .candidate-list-favourite {
        margin-bottom: 10px;
    }

    .table.manage-candidates-top {
        min-width: 650px;
    }

    .user-dashboard-info-box .candidate-list-details ul {
        color: #969696;
    }

    /* Candidate List */
    .candidate-list {
        background: #ffffff;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        border-bottom: 1px solid #eeeeee;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        padding: 20px;
        -webkit-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }

    .candidate-list:hover {
        -webkit-box-shadow: 0px 0px 34px 4px rgba(33, 37, 41, 0.06);
        box-shadow: 0px 0px 34px 4px rgba(33, 37, 41, 0.06);
        position: relative;
        z-index: 99;
    }

    .candidate-list:hover a.candidate-list-favourite {
        color: #e74c3c;
        -webkit-box-shadow: -1px 4px 10px 1px rgba(24, 111, 201, 0.1);
        box-shadow: -1px 4px 10px 1px rgba(24, 111, 201, 0.1);
    }

    .candidate-list .candidate-list-image {
        margin-right: 25px;
        -webkit-box-flex: 0;
        -ms-flex: 0 0 80px;
        flex: 0 0 80px;
        border: none;
    }

    .candidate-list .candidate-list-image img {
        width: 80px;
        height: 80px;
        -o-object-fit: cover;
        object-fit: cover;
    }

    .candidate-list-title {
        margin-bottom: 5px;
    }

    .candidate-list-details ul {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-bottom: 0px;
    }

    .candidate-list-details ul li {
        margin: 5px 10px 5px 0px;
        font-size: 13px;
    }

    .candidate-list .candidate-list-favourite-time {
        margin-left: auto;
        text-align: center;
        font-size: 13px;
        -webkit-box-flex: 0;
        -ms-flex: 0 0 90px;
        flex: 0 0 90px;
    }

    .candidate-list .candidate-list-favourite-time span {
        display: block;
        margin: 0 auto;
    }

    .candidate-list .candidate-list-favourite-time .candidate-list-favourite {
        display: inline-block;
        position: relative;
        height: 40px;
        width: 40px;
        line-height: 40px;
        border: 1px solid #eeeeee;
        border-radius: 100%;
        text-align: center;
        -webkit-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
        margin-bottom: 20px;
        font-size: 16px;
        color: #646f79;
    }

    .candidate-list .candidate-list-favourite-time .candidate-list-favourite:hover {
        background: #ffffff;
        color: #e74c3c;
    }

    .candidate-banner .candidate-list:hover {
        position: inherit;
        -webkit-box-shadow: inherit;
        box-shadow: inherit;
        z-index: inherit;
    }

    .bg-white {
        background-color: #ffffff !important;
    }

    .p-4 {
        padding: 1.5rem !important;
    }

    .mb-0, .my-0 {
        margin-bottom: 0 !important;
    }

    .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
    }

    .user-dashboard-info-box .candidates-list .thumb {
        margin-right: 20px;
    }
</style>
