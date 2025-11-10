<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    Home Page

    <a href="{{ route('loginPage') }}">Login</a>
    <a href="{{ route('userChatList') }}">Chats</a>
    <a href="/check-cookie">Check</a>


    <script>
        async function load() {
            try {
                const res = await fetch("http://127.0.0.1:8000/api/auth/profile", {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    credentials: "include",
                });

                const data = await res.json();
                console.log(data);
            } catch (error) {
                console.log("Login error response:", error);
            }
        }

        load()
    </script>
</body>

</html>
