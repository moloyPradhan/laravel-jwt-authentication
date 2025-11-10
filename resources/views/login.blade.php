<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body
    style="font-family: Arial, sans-serif; background-color: #f5f5f5; height: 100vh; display: flex; justify-content: center; align-items: center;">
    <div
        style="background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 300px;">
        <h2 style="text-align: center; margin-bottom: 20px;">Login</h2>

        <label for="email">Email</label><br>
        <input type="email" id="email" name="email" placeholder="Enter your email"
            value="moloypradhan50@gmail.com" required
            style="width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 5px;">

        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" placeholder="Enter your password" required
            style="width: 100%; padding: 10px; margin: 8px 0 16px 0; border: 1px solid #ccc; border-radius: 5px;">

        <button type="button" onclick="login()"
            style="width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Login
        </button>
    </div>


    <script>
        async function login() {

            const email = document.getElementById("email").value
            const password = document.getElementById("password").value

            try {
                const res = await fetch("http://127.0.0.1:8000/api/auth/login", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    credentials: "include",
                    body: JSON.stringify({
                        email: email,
                        password: password,
                    }),
                });

                const data = await res.json();

                if (data.httpStatus == 200) {
                    location.href = '{{ route('homePage') }}';

                }

            } catch (error) {
                console.log("Login error response:", error);

            }
        }

    </script>
</body>


</html>
