<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @include('layouts.headerLink')
</head>

<body
    class="font-sans bg-gradient-to-br from-blue-50 via-indigo-50 to-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-lg w-80 max-w-full">
        <h2 class="text-2xl font-bold text-blue-700 mb-3 text-center">Login</h2>
        <!-- Email Input -->
        <div class="relative mb-5">
            <label for="email">
                Email
            </label>
            <input type="email"
                class="peer w-full px-3 py-3 border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none"
                id="email" name="email" placeholder="user@gmail.com" autocomplete="off" required />

        </div>

        <!-- Password Input -->
        <div class="relative mb-6">
            <label for="password">
                Password
            </label>
            <input type="password" id="password" name="password"
                class="peer w-full px-3 py-3 border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none bg-transparent placeholder-shown:bg-white"
                placeholder="password@123$/" required autocomplete="off" />
            <span id="togglePwd"
                class="absolute right-3 top-half cursor-pointer text-gray-400 hover:text-blue-600 text-lg select-none">
                &#128065;
            </span>
        </div>
        <button type="button" id="btnLogin"
            class="w-full py-3 bg-blue-600 hover:bg-blue-700 transition text-white font-semibold rounded-md shadow-sm mt-2">
            Login
        </button>
    </div>

    <script type="module">
        import {
            httpRequest,
            showToast
        } from '/js/httpClient.js';

        const emailInput = document.getElementById("email");
        const passwordInput = document.getElementById("password");
        const loginBtn = document.getElementById("btnLogin");

        loginBtn.addEventListener("click", () => {
            login();
        });

        document.getElementById("togglePwd").addEventListener("click", () => {
            passwordInput.type = passwordInput.type === "password" ? "text" : "password";
        });

        async function login() {
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            if (!email) {
                showToast('warning', 'Enter email..!');
                emailInput.focus();
                return;
            }
            if (!password) {
                showToast('warning', 'Enter password..!');
                passwordInput.focus();
                return;
            }

            try {
                const url = `/api/auth/login`;
                const options = {
                    method: "POST",
                    body: {
                        email,
                        password
                    }
                };

                loginBtn.textContent = "Loading...";
                loginBtn.disabled = true;

                const res = await httpRequest(url, options);
                if (res.httpStatus >= 200 && res.httpStatus < 300) {

                    showToast('success', res.message);
                    setTimeout(() => {

                        const params = new URLSearchParams(window.location.search);
                        const source = params.get('source');

                        if (source === 'cart') {
                            location.href = @json(url('/')) + '/cart';
                        } else {
                            location.href = @json(route('homePage'));
                        }

                    }, 350);
                }

            } catch (err) {
                console.error("Login error:", err);
            } finally {
                loginBtn.textContent = "Login";
                loginBtn.disabled = false;
            }
        }
    </script>
</body>

</html>
