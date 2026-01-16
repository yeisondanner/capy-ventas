document.addEventListener("DOMContentLoaded", () => {

    const chatbot = document.getElementById("chatbot");
    const toggle = document.getElementById("chatbot-toggle");
    const closeBtn = document.getElementById("close-chat");
    const welcomeMessage = document.getElementById("welcome-message");
    const welcomeClose = document.getElementById("welcome-close");
    const sendBtn = document.getElementById("send-btn");
    const input = document.getElementById("chat-input");
    const messages = document.getElementById("chatbot-messages");
    const chatbotInputContainer = document.querySelector(".chatbot-input");
    const listConversation = document.getElementById("conversation-list");
    const backBtn = document.getElementById("back-btn");
    const newConversationBtn = document.getElementById("new-conversation");
    const listView = document.getElementById("chatbot-list-conversation");
    const micBtn = document.getElementById("microphone-btn");
    const stopBtn = document.getElementById("stop-record-btn");

    let isChatOpen = false;
    let inactivityTimer = null;
    let isBotBusy = false;
    let currentView = "closed";
    let activeConversationId = null;
    let conversations = [];
    let chatState = "closed";
    let recognition = null;
    let isRecording = false;
    let listeningInterval = null;
    let dotCount = 0;

    const INACTIVITY_TIME = 10 * 60 * 1000;//1 * 60 * 1000; // 10 minutos

    initSpeechRecognition();

    //botones
    toggle.addEventListener("click", () => {
        if (welcomeMessage) welcomeMessage.style.display = "none";
        if (chatState === "closed") {
            openChat();
        } else {
            closeChat();
        }
    });

    closeBtn.addEventListener("click", closeChat);

    backBtn.addEventListener("click", showListView);

    newConversationBtn.addEventListener("click", createNewConversation);

    sendBtn.addEventListener("click", sendMessage);

    input.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    input.addEventListener("input", () => {
        resetInactivityTimer();
        autoResizeInput();
    });

    if (welcomeClose && welcomeMessage) {
        welcomeClose.addEventListener("click", () => {
            welcomeMessage.style.display = "none";
        });
    }

    function resetInactivityTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(handleInactivity, INACTIVITY_TIME);
    }
    function handleInactivity() {
        const text = "Hemos esperado mucho tiempo y no recibimos ninguna respuesta. Si deseas continuar inicia un nuevo mensaje.";
        const convo = conversations.find(c => c.id === activeConversationId);

        if(chatState === "closed") return;
        if (!convo || convo.ended) return;

        convo.ended = true;

        convo.messages.push({
            sender: "bot",
            text
        });

        appendBotMessage();
        //handleGlobalClick();

        disableChatInput();

        setTimeout(() => {
            closeChat();
            activeConversationId = null;
        }, 3000);
    }

    function initSpeechRecognition() {

        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

        if (!SpeechRecognition) {
            micBtn.style.display = "none";
            return;
        }

        recognition = new SpeechRecognition();
        recognition.lang = "es-PE";
        recognition.interimResults = false;
        recognition.continuous = false;

        micBtn.addEventListener("click", starRecording);
        stopBtn.addEventListener("click", stopRecording);
        recognition.onresult = handleSpeechResult;
        recognition.onend = handleSpeechEnd;
        input.placeholder = "...";

    }

    function starRecording(){
        resetInactivityTimer();
        if(isRecording) return;
        isRecording = true;
        input.disabled = true;
        micBtn.style.display = "none";
        stopBtn.style.display = "flex";
        startListeningPlaceholder();
        recognition.start();
    }
    function stopRecording() {
        if (!isRecording) return;
        recognition.stop();
        //startListeningPlaceholder();

    }
    function handleSpeechResult(event) {
        const transcript = Array.from(event.results)
            .map(r => r[0].transcript)
            .join(" ");

        input.value = transcript;
        input.disabled = false;
        autoResizeInput();
        resetInactivityTimer();
    }

    function handleSpeechEnd() {
        isRecording = false;
        stopListeningPlaceholder();
        stopBtn.style.display = "none";
        micBtn.style.display = "flex";
        input.disabled = false;
        sendBtn.disabled = false;
        micBtn.disabled = false;
        input.placeholder = "Escribe tu mensaje"
        input.focus();
    }

    function startListeningPlaceholder(){
        dotCount = 0;
        input.placeholder = "Escuchando";
        listeningInterval = setInterval(() => {
            dotCount = (dotCount+ 1) % 4;
            input.placeholder = "Escuchando" + ".".repeat(dotCount);
        }, 500);
    }
    function stopListeningPlaceholder() {
        clearInterval(listeningInterval);
        listeningInterval = null;
        input.placeholder = "Escribiendo tu mensaje ...";
    }

    function autoResizeInput() {
        input.style.height = "20px";
        input.style.height = Math.min(input.scrollHeight, 120) + "px";
    }

    //funciones
    function openChat() {
        chatbot.style.display = "flex";
        showListView();
        hideHeaderButtons();
        //toggle.style.display = "none";
        setToggleIcon("bi-chevron-down");
       // showListView();
        chatState = "open";
        resetInactivityTimer();
    }

    function closeChat() {
        chatbot.style.display = "none";
        //toggle.style.display = "flex";
        setToggleIcon("bi-chat-left-text-fill");
        hideHeaderButtons();
        chatState = "closed";
        clearTimeout(inactivityTimer);
    }
    function hideHeaderButtons(){
        backBtn.style.display = "none";
        closeBtn.style.display = "none"
    }

    function showHeaderButtons(){
        backBtn.style.display = "flex";
        closeBtn.style.display = "flex";
    }

    function showListView() {
        listView.style.display = "flex";
        messages.style.display = "none";
        chatbotInputContainer.style.display = "none";
        hideHeaderButtons();
        setToggleIcon("bi-chevron-down");
        chatState = "list";
    }

    function showChatView() {
        listView.style.display = "none";
        messages.style.display = "block";
        chatbotInputContainer.style.display = "flex";
        showHeaderButtons();
        setToggleIcon("bi-chevron-down");
        chatState = "chat";
        const inicio = messages.querySelector(".inicio-message");
        if (inicio && messages.querySelectorAll(".message").length === 0) {
            inicio.style.display = "block";
        }
    }

    function enableChatInput() {
        input.disabled = false;
        sendBtn.disabled = false;
        input.placeholder = "Escribe tu mensaje...";
        input.classList.remove("input-disabled");
    }

    function setToggleIcon(icon) {
        const i = toggle.querySelector("i");
        i.className =  `bi ${icon}`;
    }

    function setStartDay() {
        const timeEl = document.getElementById("inicio-time");
        if (!timeEl) return;

        const now = new Date();
        timeEl.textContent = now.toLocaleTimeString("es-PE", {
            day:"2-digit",
            month:"2-digit",
            year:"numeric",
            hour:"2-digit",
            minute: "2-digit",
            hour12: true
        });
    }
    function lockInput(){
        isBotBusy = true;
        input.disabled = true;
        sendBtn.disabled = true;
        micBtn.disabled = true;
        input.placeholder = "Capybot está escribiendo...";
    }

    function unlockInput(){
        isBotBusy = false;
        input.disabled = false;
        sendBtn.disabled = false;
        micBtn.disabled = false;
        input.placeholder = "Escribe tu mensaje";
        input.focus();
    }

    //conversaciones
    function createNewConversation() {
        const id = Date.now();

        const convo = {
            id,
            title: "CapyBot",
            messages: [],
            time: getTime(),
            ended:false
        };
        conversations.unshift(convo);
        activeConversationId = id;

        renderConversationList();
        showChatView();
        enableChatInput();

        messages.querySelectorAll(".message").forEach(m => m.remove());
        setStartDay();
        input.focus();
        resetInactivityTimer();
    }

    function renderConversationList() {
        listConversation.innerHTML = "";

        conversations.forEach(c => {
            const item = document.createElement("div");
            item.className = "conversation-item";
            if (c.id === activeConversationId) item.classList.add("active");

            const lastMsg = c.messages.at(-1)?.text || "Nueva conversación";
            const cleanPreview = lastMsg.replace(/\*\*(.*?)\*\*/g, '$1');

            item.innerHTML = `
            <div class="conv-avatar">
                <img src="Assets/icon-chat.jpg">
            </div>
            <div class="conv-content">
                <div class="conv-header">
                    <span class="conv-title">${c.title}</span>
                    <span class="conv-time">${c.time}</span>
                </div>
                <div class="conv-preview">${cleanPreview}</div>
            </div>
            <button class="delete-conv-btn" data-id="${c.id}">
               <i class="bi bi-trash-fill"></i>
            </button>
        `;

            item.onclick = (e) => {
                if (!e.target.closest('.delete-conv-btn')) {
                    openConversation(c.id);
                }
            };

            const delBtn = item.querySelector(".delete-conv-btn");
            delBtn.onclick = (e) => {
                e.stopPropagation();
                deleteConversation(c.id);
            };

            listConversation.appendChild(item);
        });
    }

    function openConversation(id) {
        const convo = conversations.find(c => c.id === id);
        if (!convo) return;

        activeConversationId = id;
        messages.querySelectorAll(".message").forEach(m => m.remove());

        convo.messages.forEach((m, index) => {
            if (m.sender === "user") {
                appendUserMessage(m.text);
            } else {
                appendBotMessage(m.text, index === 0);
            }
        });

        showChatView();
        resetInactivityTimer();

        if(convo.ended){
            disableChatInput();
        }else{
            enableChatInput();
            resetInactivityTimer();
        }
    }
    function deleteConversation(id) {
        conversations = conversations.filter(c => c.id !== id);

        if (activeConversationId === id) {
            activeConversationId = null;
            showListView();
        }
        renderConversationList();
    }

    //mensajes
    async function sendMessage() {
        resetInactivityTimer();
        if (isBotBusy || !activeConversationId) return;

        const text = input.value.trim();
        if (!text) return;

        const convo = conversations.find(c => c.id === activeConversationId);
        if (!convo) return;
        appendUserMessage(text);
        convo.messages.push({ sender: "user", text });
        convo.time = getTime();
        renderConversationList();
        input.value = "";
        input.style.height = "40px";

        const typingMsg = createTypingMessage();
        messages.appendChild(typingMsg);
        scrollBottom();
        lockInput();

        try {
            const res = await fetch("https://capy-ai-api.onrender.com/chat", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ question: text })
            });

            const data = await res.json();
            typingMsg.remove();

            appendBotMessage(data.reply, true);
            clearTimeout();
            convo.messages.push({ sender: "bot", text: data.reply });
            convo.time = getTime();
            renderConversationList();

        } catch {
            typingMsg.remove();
            appendBotMessage("No puedo conectar con el servidor.", true);
        }
        unlockInput();
        isBotBusy = false;
    }

    function getTime() {
        return new Date().toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit"
        });
    }

    function createTypingMessage() {
        const msg = document.createElement("div");
        msg.className = "message bot";
        msg.dataset.typing = "true";
        msg.innerHTML = `
            <div class="avatar">
                <img src="Assets/icon-chat.jpg">
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

    function appendBotMessage(text) {
        const convo = conversations.find(c => c.id === activeConversationId);
        if (!convo) return;

        const block = document.createElement("div");
        block.className = "message bot";

        block.innerHTML = `
        <div class="avatar-container">
           <img src="Assets/icons/ChatBot/icon_message_avatar2.jpeg" alt="Capy Bot">
        </div>
        <div class="bot-content">
            <span class="bot-name">CapyBot</span>
            <div class="bubble-container"></div>
            <div class="timestamp">${getTime()}</div>
        </div>
    `;

        const bubbleContainer = block.querySelector(".bubble-container");

        const lines = text.split(/\r?\n/).filter(line => line.trim() !== "");
        lines.forEach((line) => {
            const bubble = document.createElement("div");
            bubble.className = "bubble";
            bubble.innerHTML = line.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            bubbleContainer.appendChild(bubble);
        });

        messages.appendChild(block);
        scrollBottom();
    }

    function addMessage(text, sender) {
        const lines = text.split(/\r?\n/).filter(line => line.trim() !== "");

        lines.forEach((line) => {
            const msg = document.createElement("div");
            msg.className = `message ${sender}`;

            const timestampHTML = `<div class="timestamp">${getTime()}</div>`;
            const formattedLine = line.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            if (sender === "bot") {
                msg.innerHTML = `
                <div class="bot-content">
                    <span class="bot-name">CapyBot</span>
                    <div class="bubble">${formattedLine}</div>
                    ${timestampHTML}
                </div>
            `;
            } else {
                msg.innerHTML = `
                <div class="bubble-container">
                    <div class="bubble">${formattedLine}</div>
                    ${timestampHTML}
                </div>
            `;
            }

            messages.appendChild(msg);
        });

        scrollBottom();
    }

    /*function addMessage(text, sender, showAvatar = false) {
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
    }*/

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
