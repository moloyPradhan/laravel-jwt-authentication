<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chat</title>

    <style>
        .messageContainer {
            background-color: burlywood;
            height: 50vh;
            margin-bottom: 10px;
            padding: 10px
        }
    </style>
</head>

<body>

    <div id="messageContainer" class="messageContainer">

    </div>

    <div>
        <input type="text" id="msgInput">
        <button id="sendBtn">Send</button>
    </div>

    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script>
        const userId = @json($userId);
        const friendId = @json($friendId);

        console.log("Current User ID:", userId);
        console.log("Friend User ID:", friendId);

        const roomId = `R_${[userId, friendId].sort().join('_')}`;

        const socket = io("http://localhost:6001");

        socket.on("connect", () => {
            console.log("Connected to socket:", socket.id);

            // Register current user
            socket.emit("register", userId);

            // Join chat room
            socket.emit("joinRoom", roomId);
        });

        // Receive messages
        socket.on("receiveMessage", (data) => {
            if (data.roomId === roomId) {
                console.log("💬 New message:", data.message);
                // append to chat UI

                document.getElementById("messageContainer").innerHTML += `<div>${data.message}</div>`
            }
        });

        // Send message (to Laravel)
        async function sendMessage(text) {
            const res = await fetch("http://127.0.0.1:8000/api/send-message", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    room_id: roomId,
                    from_user: userId,
                    message: text,
                }),
            });

            const data = await res.json();
            console.log("Message sent response:", data);

            // Clear input box
            document.getElementById("msgInput").value = "";
        }

        // Example button trigger
        document.getElementById("sendBtn").addEventListener("click", () => {
            const text = document.getElementById("msgInput").value;
            sendMessage(text);
        });
    </script>

</body>

</html>
