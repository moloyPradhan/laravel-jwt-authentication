@extends('layouts.profile')

@section('title', 'Chat List')

@section('content')
    <p>Start chat with others {{ $authUser['name'] ?? 'User' }}!</p>

    <div id="containerListUser">

    </div>


    @push('scripts')
        <script type="module">
            import {
                httpRequest
            } from '/js/httpClient.js';

            async function listOtherUser() {
                try {
                    const res = await httpRequest("/api/users/others");
                    const users = res?.data?.otherUsers || [];

                    let html = "";

                    users.forEach((item) => {
                        html += `
                            <div>
                                <a href="/chat/${item.uid}">${item.name}</a>     
                            </div>
                        `;
                    });

                    document.getElementById("containerListUser").innerHTML = html;
                } catch (err) {
                    console.log("Error :", err.message);
                }
            }

            listOtherUser();
        </script>
    @endpush


@endsection
