<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Hàng đợi nhận bằng') }}
        </h2>
    </x-slot>
    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white bg-whitedark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 dark:text-gray-100">
                    <div id="find">
                        <input id="txtStudentId" type="text" placeholder="Nhập mssv/mshv"
                            style="width: 300px; border-radius: 5px;">
                        <x-primary-button id="btnSubmit">
                            {{ __('Thêm') }}
                        </x-primary-button>
                    </div>
                    <div id="div-message" style="color: red; font-weight: bold; margin-top : 10px; display: none;">
                        <p id="message"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white bg-whitedark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 dark:text-gray-100">
                    <table class="table table-hover" id="queueTable">
                        <thead>
                            <tr>
                                <th>MSSV</th>
                                <th>Họ và tên</th>
                                <th>Loại bằng</th>
                                <th>Chuyên ngành</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="{{env('SOCKET_URL')}}/socket.io/socket.io.js"></script>
    <script>
        var app_url = "{{env('APP_URL')}}";
        var socket_url = "{{env('SOCKET_URL')}}";
    </script>
    <script src="{{ asset('assets/js/socket.js') }}"></script>
    <script src="{{ asset('assets/js/scan.js') }}" type='module'></script>
</x-app-layout>
