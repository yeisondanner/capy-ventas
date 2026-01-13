document.addEventListener("DOMContentLoaded", () => {

    const chatbot = document.getElementById("chatbot");
    const toggle = document.getElementById("chatbot-toggle");
    const closeBtn = document.getElementById("close-chat");
    const welcomeMessage = document.getElementById("welcome-message");
    const welcomeClose = document.getElementById("welcome-close");
    const sendBtn = document.getElementById("send-btn");
    const input = document.getElementById("chat-input");
    const messages = document.getElementById("chatbot-messages");
    const chatInput = document.getElementById("chat-input");

    let isChatOpen = false;
    let inactivityTimer = null;
    let isBotBusy = false;
    let chatHistory = [];

    const INACTIVITY_TIME = 10 * 60 * 1000; // 10 minutos

    //
    toggle.addEventListener("click", openChat);

    closeBtn.addEventListener("click", closeChat);

    welcomeClose.addEventListener("click", () => {
        if (welcomeMessage) welcomeMessage.style.display = "none";
        toggle.style.display = "flex";
    });

    sendBtn.addEventListener("click", () => sendMessage());

    input.addEventListener("keypress", e => {
        resetInactivityTimer();
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    chatInput.addEventListener("input", () => {
        chatInput.style.height = "auto";
        chatInput.style.height = chatInput.scrollHeight + "px";
    });

    document.addEventListener("click", handleGlobalClick);

    // función para abrir chat
    function openChat() {
        chatbot.style.display = "flex";
        toggle.style.display = "none";
        isChatOpen = true;
        welcomeMessage.style.display = "none";
        input.focus();
        resetInactivityTimer();
    }

    // función para cerrar chat
    function closeChat() {
        chatbot.style.display = "none";
        toggle.style.display = "flex";
        isChatOpen = false;
        clearTimeout(inactivityTimer);
        showChatRatingWithRestart();
    }

    //función para inactividad
    function resetInactivityTimer() {
        if (!isChatOpen) return;
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(handleInactivity, INACTIVITY_TIME);
    }

    function handleInactivity() {
        if (!isChatOpen || isBotBusy) return;
        appendBotMessage(
            "Este chat ha estado inactivo por un momento. Estuvimos esperando tu respuesta.\n" +
            "Recuerda que estamos disponibles las 24 horas del día"
        );
        showChatRatingWithRestart();
    }

    async function sendMessage(text = null) {
        if (isBotBusy) return;

        const question = text || input.value.trim();
        if (!question) return;

        isBotBusy = true;
        clearTimeout(inactivityTimer);

        appendUserMessage(question);
        input.value = "";

        const typingMsg = createTypingMessage();
        messages.appendChild(typingMsg);
        scrollBottom();

        try {
            const res = await fetch("https://capy-ai-api.onrender.com/chat", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ question, history: chatHistory })
            });
            if (!res.ok) throw new Error(`Error en el servidor: ${res.status}`);

            const data = await res.json();
            chatHistory = data.history || [];

            typingMsg.remove();
            appendBotMessage(data.reply);
            isBotBusy = false;

        } catch (error) {
            console.error(error);
            typingMsg.querySelector(".bubble").innerHTML = "No puedo conectar con el servidor.";
            isBotBusy = false;
            resetInactivityTimer();
        }

        scrollBottom();
    }

    function createTypingMessage() {
        const msg = document.createElement("div");
        msg.className = "message bot";
        msg.dataset.typing = "true";
        msg.innerHTML = `
            <div class="avatar">
                <img src="Assets/capi_chatbot.png">
            </div>
            <div class="bot-content">
                <span class="bot-name">CapyBot</span>
                <div class="bubble typing">
                    <span></span><span></span><span></span>
                </div>
                <div class="timestamp">${getTimestamp()}</div>
            </div>
        `;
        return msg;
    }

    function appendUserMessage(text) {
        addMessage(text, "user");
    }

    function appendBotMessage(text, showAvatar = true) {
        addMessage(text, "bot", showAvatar);
    }

    function addMessage(text, sender, showAvatar = false) {
        const lines = text.split(/\r?\n/).filter(line => line.trim() !== "");
        let avatarShown = showAvatar;

        lines.forEach((line, index) => {
            const msg = document.createElement("div");
            msg.className = `message ${sender}`;

            const timestamp = (index === lines.length - 1) ? `<div class="timestamp">${getTimestamp()}</div>` : '';

            if (sender === "bot") {
                if (avatarShown) {
                    msg.innerHTML = `
                        <div class="avatar">
                            <img src="Assets/capi_chatbot.png">
                        </div>
                        <div class="bot-content">
                            <span class="bot-name">CapyBot</span>
                            <div class="bubble">${marked.parse(line)}</div>
                            ${timestamp}
                        </div>`;
                    avatarShown = false;
                } else {
                    msg.innerHTML = `
                        <div class="bot-content no-avatar">
                            <div class="bubble">${marked.parse(line)}</div>
                            ${timestamp}
                        </div>`;
                }
            } else {
                msg.innerHTML = `
                    <div class="bubble-container">
                        <div class="bubble">${line}</div>
                        ${timestamp}
                    </div>`;
            }

            messages.appendChild(msg);
            scrollBottom();
        });
    }

    function scrollBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    function getTimestamp() {
        const now = new Date();
        return now.toLocaleTimeString("en-US", {
            hour: "2-digit",
            minute: "2-digit",
            hour12: true
        }).toLowerCase();
    }

    function showChatRatingWithRestart() {
        if (document.getElementById("chat-rating")) return;

        const rating = document.createElement("div");
        rating.id = "chat-rating";
        rating.className = "chat-rating";
        rating.innerHTML = `
            <p>¿Te he ayudado?</p>
            <div class="rating-buttons">
                <i class="rating-btn bi bi-hand-thumbs-up-fill" data-rate="like"></i>
                <i class="rating-btn bi bi-hand-thumbs-down-fill" data-rate="dislike"></i>
            </div>
        `;

        messages.appendChild(rating);
        scrollBottom();
        disableChatInput();
    }

    function disableChatInput() {
        input.disabled = true;
        sendBtn.disabled = true;
        input.placeholder = "El chat ha finalizado";
        input.classList.add("input-disabled");
    }

    function showChatEnded() {
        const end = document.createElement("div");
        end.className = "chat-ended";
        end.innerHTML = `
            <p><strong>Tu chat ha terminado.</strong></p>
            <p>
                <span class="new-chat-link">Para iniciar un nuevo chat, haz clic aquí</span>.
            </p>
        `;
        messages.appendChild(end);
        scrollBottom();
    }

    function handleGlobalClick(e) {
        if (e.target.classList.contains("rating-btn")) {
            const rate = e.target.dataset.rate;
            appendBotMessage(rate === "like" ? "¡Gracias por tu feedback! 💙" : "Gracias, seguiremos mejorando 🙌");
            document.getElementById("chat-rating")?.remove();
            showChatEnded();
        }

        if (e.target.classList.contains("new-chat-link")) {
            location.reload();
        }
    }

});
