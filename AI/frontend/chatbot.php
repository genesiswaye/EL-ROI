<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" href="chatbot.css">

</head>

<body>

<div id="chat-container">

    <div id="chat-header">

        StudentLancer AI

    </div>

    <div id="chat-messages"></div>

    <div id="suggestions">

        <button onclick="sendSuggestion('How does escrow work?')">
            Escrow
        </button>

        <button onclick="sendSuggestion('How do withdrawals work?')">
            Withdrawals
        </button>

        <button onclick="sendSuggestion('What is my wallet balance?')">
            Wallet
        </button>

    </div>

    <div id="chat-input-area">

        <input
            type="text"
            id="message"
            placeholder="Ask StudentLancer AI..."
        >

        <button onclick="sendMessage()">

            Send

        </button>

    </div>

</div>

<script src="chatbot.js"></script>

</body>
</html>