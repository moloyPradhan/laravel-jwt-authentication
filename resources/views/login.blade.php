<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>

    @include('layouts.headerLink')
</head>

<body
    style="font-family: Arial, sans-serif; background-color: #f5f5f5; height: 100vh; display: flex; justify-content: center; align-items: center;">
    <div
        style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 300px;">
        <h2 style="text-align: center; margin-bottom: 20px;">Login</h2>

        <label for="email">Email</label><br>
        <input type="email" class="outline-none" id="email" name="email" placeholder="Enter your email"
            style="width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 5px;"
            required>

        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" placeholder="Enter your password" required
            class="outline-none"
            style="width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 5px;">

        <button type="button" id="btnLogin"
            style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Login
        </button>
    </div>


    <script type="module">
        import {
            httpRequest,
            showToast
        } from '/js/httpClient.js';

        document.getElementById("btnLogin").addEventListener("click", (event) => {
            login()
        });

        async function login() {
            const email = document.getElementById("email").value
            const password = document.getElementById("password").value

            if (!email) {
                showToast('warning', 'Enter email..!');
                return;
            }

            if (!password) {
                showToast('warning', 'Enter password..!');
                return;
            }

            try {
                const url = `/api/auth/login`;
                const options = {
                    method: "POST",
                    body: {
                        email,
                        password
                    },
                };

                const res = await httpRequest(url, options);

                if (res.httpStatus >= 200 && res.httpStatus < 300) {
                    showToast('success', res.message);
                    setTimeout(() => {
                        location.href = @json(route('homePage'))
                    }, 200);
                }

            } catch (err) {
                console.error("Upload error:", err);
            }
        }
    </script>
</body>


</html>
