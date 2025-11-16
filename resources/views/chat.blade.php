<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chat</title>

    <style>
        body {
            background: #f3f4f6;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .chatBox {
            background: #ffffff;
            height: 70vh;
            border-radius: 10px;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .message {
            max-width: 60%;
            padding: 12px 15px;
            border-radius: 12px;
            font-size: 15px;
            line-height: 1.4;
            word-break: break-word;
        }

        .sent {
            align-self: flex-end;
            background: #0c8c32;
            color: #fff;
            border-bottom-right-radius: 0;
        }

        .received {
            align-self: flex-start;
            background: #3b45e0;
            color: #fff;
            border-bottom-left-radius: 0;
        }

        #bottomSpace {
            height: 5px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .inputArea {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        .inputArea input {
            flex: 1;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
        }

        .inputArea button {
            background: #2563eb;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            color: #fff;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div id="messageContainer" class="chatBox"></div>
    <div class="inputArea">
        <input type="text" id="msgInput">
        <button id="sendBtn">Send</button>
    </div>

    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>

    <script type="module">
        import {
            httpRequest
        } from '/js/httpClient.js';

        const userId = @json($userId);
        const friendId = @json($friendId);
        const roomId = `R_${[userId, friendId].sort().join('_')}`;

        async function renderMessages() {
            try {
                const url = `/api/messages/${roomId}`;
                const res = await httpRequest(url);
                const messages = res?.data?.messages || [];

                let html = "";

                messages.forEach((item) => {
                    html += `<div class="message ${item.type}">${item.message}</div>`;
                });


                document.getElementById("messageContainer").innerHTML = html;

                scrollToBottom();
            } catch (err) {
                console.log("Error :", err.message);
            }
        }

        renderMessages();

        function scrollToBottom() {
            const box = document.getElementById("messageContainer");
            box.scrollTop = box.scrollHeight;
        }


        const socket = io("http://localhost:6001");

        socket.on("connect", () => {
            socket.emit("register", userId);
            socket.emit("joinRoom", roomId);
        });

        socket.on("receiveMessage", (data) => {
            if (data.roomId === roomId) {

                if (String(data.fromUser) !== String(userId)) {
                    document.getElementById("messageContainer").innerHTML +=
                        `<div class="message received">${data.message}</div>`;
                }
                scrollToBottom();
            }
        });


        async function sendMessage(text) {
            if (!text.trim()) return;

            document.getElementById("messageContainer").innerHTML +=
                `<div class="message sent">${text}</div>`;

            scrollToBottom();

            await fetch("/api/send-message", {
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

            document.getElementById("msgInput").value = "";
        }


        document.getElementById("sendBtn").addEventListener("click", () => {
            const text = document.getElementById("msgInput").value;
            sendMessage(text);
        });
    </script>

</body>

</html>
