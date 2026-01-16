<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playwrite+PE:wght@400;500;600&display=swap" rel="stylesheet">

<div id="chatbot">
    <div id="chatbot-header">
        <button id="back-btn">
            <i class="bi bi-chevron-double-left"></i>
        </button>
        <div id="chatbot-name">
            <div id="img-chatbot-name">
                <img src="Assets/carpincho.png" alt="Capy Bot">
            </div>
            <span>CapyBot</span>
        </div>
        <div id= "cont-close-btn">
            <button id="close-chat">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <div id="chatbot-list-conversation">
        <div id="conversation-list"></div>
        <div class="footer-list">
            <button id="new-conversation">Nueva Conversación</button>
            <span class="creado-por">creado por capyventas</span>
        </div>
    </div>

    <div class="chatbot-messages" id="chatbot-messages">
        <div class="inicio-message">
           Aqui inicia tu conversación con nosotros. Envía un mensaje para empezar.
        </div>
        <div class = "inicio-time" id = "inicio-time"></div>
    </div>

    <div class="chatbot-input">
        <textarea id="chat-input" placeholder="Escribe tu mensaje..."></textarea>

        <button id="microphone-btn" title="hablar" >
            <i class="bi bi-mic-fill"></i>
        </button>
        <button id="stop-record-btn" title="Detener" style="display: none">
            <i class="bi bi-stop-circle-fill"></i>
        </button>
        <button id="send-btn">
            <img src="Assets/icons/ChatBot/icon_send.png" alt="Capy Bot">
        </button>
    </div>
</div>

<div id="messages-list"></div>

<div id="welcome-message">
    <button id="welcome-close">
        <i class="bi bi-x-lg"></i>
    </button>
    <div class="welcome-avatar">
        <img src="Assets/icons/ChatBot/icon_message_avatar2.jpeg" alt="Capy Bot">
    </div>
    <div class="welcome-text">
        <strong>¡Hola! Soy CapyBot</strong><br>
        <span class="welcome-desc">
            Tu Asistente virtual.
            ¿Qué te gustaría hacer hoy?
        </span>
    </div>
</div>

<div id="chatbot-toggle">
    <i class="bi bi-chat-left-text-fill"></i>
</div>
