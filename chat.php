<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/db_config.php';

// Get user information if logged in
$user_info = null;
if (isset($_SESSION['user_id'])) {
    $sql = "SELECT username FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_info = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Us</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .chat-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            height: 500px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .chat-header {
            background: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-header h3 {
            margin: 0;
            font-size: 1.1em;
        }

        .close-chat {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1.2em;
        }

        .chat-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background: #f9f9f9;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .message-content {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 15px;
            margin: 5px 0;
        }

        .bot-message {
            align-items: flex-start;
        }

        .bot-message .message-content {
            background: #e9ecef;
            color: #333;
        }

        .user-message {
            align-items: flex-end;
        }

        .user-message .message-content {
            background: #4CAF50;
            color: white;
        }

        .chat-input {
            padding: 15px;
            background: white;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }

        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
        }

        .chat-input button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .chat-input button:hover {
            background: #45a049;
        }

        .chat-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 999;
            transition: transform 0.3s;
        }

        .chat-icon:hover {
            transform: scale(1.1);
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="chat-icon" id="chatIcon">
        <i class="fas fa-robot"></i>
    </div>

    <div class="chat-container hidden" id="chatContainer">
        <div class="chat-header">
            <h3><i class="fas fa-robot"></i> AI Assistant</h3>
            <button class="close-chat" id="closeChat">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message bot-message">
                <div class="message-content">
                    Hello! I'm your AI assistant. How can I help you today?
                </div>
            </div>
        </div>
        <div class="chat-input">
            <input type="text" id="messageInput" placeholder="Type your message...">
            <button id="sendMessage">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <script>
        const chatIcon = document.getElementById('chatIcon');
        const chatContainer = document.getElementById('chatContainer');
        const closeChat = document.getElementById('closeChat');
        const messageInput = document.getElementById('messageInput');
        const sendMessage = document.getElementById('sendMessage');
        const chatMessages = document.getElementById('chatMessages');

        // Common responses for different types of questions
        const responses = {
            shipping: "We offer standard shipping (3-5 business days) and express shipping (1-2 business days). Free shipping is available on orders over $50.",
            returns: "You can return items within 30 days of delivery. Please ensure items are unused and in original packaging.",
            payment: "We accept all major credit cards, PayPal, and bank transfers.",
            contact: "You can reach our customer service team at support@example.com or call us at 1-800-123-4567.",
            hours: "Our customer service is available Monday to Friday, 9 AM to 6 PM EST.",
            website: "FREE CHOISE is your one-stop destination for all your shopping needs. We offer a wide range of products across various categories with competitive prices and excellent customer service.",
            categories: "We have multiple categories including Electronics, Fashion, Home & Living, Sports, and more. You can browse all categories from our homepage.",
            products: "We offer a wide range of products with competitive prices. You can find featured products on our homepage and browse all products by category.",
            account: "You can create an account to enjoy benefits like order tracking, saved addresses, and faster checkout. New customers get 10% off on their first purchase!",
            cart: "You can add products to your cart and proceed to checkout. You need to be logged in to make a purchase.",
            special: "We offer special deals including 10% off for new customers and free shipping on orders over $50.",
            default: "I'm sorry, I don't have information about that. Please contact our customer service for assistance."
        };

        // Function to add a message to the chat
        function addMessage(content, isUser = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isUser ? 'user-message' : 'bot-message'}`;
            messageDiv.innerHTML = `
                <div class="message-content">
                    ${content}
                </div>
            `;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Function to get AI response
        function getAIResponse(message) {
            message = message.toLowerCase();
            
            if (message.includes('shipping') || message.includes('delivery')) {
                return responses.shipping;
            } else if (message.includes('return') || message.includes('refund')) {
                return responses.returns;
            } else if (message.includes('payment') || message.includes('pay') || message.includes('credit card')) {
                return responses.payment;
            } else if (message.includes('contact') || message.includes('email') || message.includes('phone')) {
                return responses.contact;
            } else if (message.includes('hours') || message.includes('time') || message.includes('available')) {
                return responses.hours;
            } else if (message.includes('website') || message.includes('about us') || message.includes('what is')) {
                return responses.website;
            } else if (message.includes('category') || message.includes('categories')) {
                return responses.categories;
            } else if (message.includes('product') || message.includes('items')) {
                return responses.products;
            } else if (message.includes('account') || message.includes('register') || message.includes('sign up')) {
                return responses.account;
            } else if (message.includes('cart') || message.includes('checkout') || message.includes('buy')) {
                return responses.cart;
            } else if (message.includes('special') || message.includes('offer') || message.includes('discount')) {
                return responses.special;
            } else {
                return responses.default;
            }
        }

        // Event listeners
        chatIcon.addEventListener('click', () => {
            chatContainer.classList.remove('hidden');
            chatIcon.classList.add('hidden');
        });

        closeChat.addEventListener('click', () => {
            chatContainer.classList.add('hidden');
            chatIcon.classList.remove('hidden');
        });

        sendMessage.addEventListener('click', sendUserMessage);
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendUserMessage();
            }
        });

        function sendUserMessage() {
            const message = messageInput.value.trim();
            if (message) {
                addMessage(message, true);
                messageInput.value = '';
                
                // Simulate AI thinking
                setTimeout(() => {
                    const response = getAIResponse(message);
                    addMessage(response);
                }, 1000);
            }
        }
    </script>
</body>
</html> 