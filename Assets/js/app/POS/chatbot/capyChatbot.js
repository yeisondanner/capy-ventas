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

    const INACTIVITY_TIME = 5 * 60 * 1000;//1 * 60 * 1000; // 10 minutos

    let inactivityTimer = null;
    let isBotBusy = false;
    let activeConversationId = null;
    let conversations = [];
    let chatState = "closed";
    let recognition = null;
    let isRecording = false;
    let listeningInterval = null;
    let dotCount = 0;

    //INIT
    initSpeechRecognition();
    bindUIEvents();

    //funciones
    function bindUIEvents(){
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

    }

    //funciones de inactividad
    function resetInactivityTimer() {
        clearTimeout(inactivityTimer);
        if(chatState !== "closed" && activeConversationId) {
            inactivityTimer = setTimeout(handleInactivity, INACTIVITY_TIME);
        }
    }

    function pauseInactivityTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = null;
    }

    function resumeInactivityTimer() {
        if (chatState !== "closed" && activeConversationId) {
            resetInactivityTimer();
        }
    }

    function handleInactivity() {
        const text = "Hemos esperado mucho tiempo y no recibimos ninguna respuesta. Si deseas continuar inicia un nuevo mensaje.";
        const convo = conversations.find(c => c.id === activeConversationId);

        if(chatState === "closed") return;
        if (!convo || convo.ended) return;

        convo.ended = true;
        convo.messages.push({
            sender: "bot",
            text: text
        });
        appendBotMessage(text);
        //handleGlobalClick();
        disableChatInput();
        setTimeout(() => {
            enableFullChatInput();
            closeChat();
            activeConversationId = null;
        }, 4000);
    }


    ///funciones speechrecognition /voz----> microfono
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
        if (isBotBusy) return;
        const convo = conversations.find(c=> c.id === activeConversationId);
        if (!convo || convo.ended) return;

        pauseInactivityTimer();
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

        resumeInactivityTimer();
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


    ///chat UI
    function openChat() {
        chatbot.style.display = "flex";
        chatState = "list";
        showListView();
        //toggleHeaderButtons(false);
        //toggle.style.display = "none";
        setToggleIcon("bi-chevron-down");
        // showListView();
        //chatState = "open";
        clearTimeout(inactivityTimer);
        resetInactivityTimer();
    }

    function closeChat() {
        chatbot.style.display = "none";
        //toggle.style.display = "flex";
        setToggleIcon("bi-chat-left-text-fill");
        toggleHeaderButtons(false);
        chatState = "closed";
        clearTimeout(inactivityTimer);
    }

    function showListView() {
        listView.style.display = "flex";
        messages.style.display = "none";
        chatbotInputContainer.style.display = "none";
        toggleHeaderButtons(false);
        setToggleIcon("bi-chevron-down");
        chatState = "list";
    }

    function showChatView() {
        listView.style.display = "none";
        messages.style.display = "block";
        chatbotInputContainer.style.display = "flex";
        toggleHeaderButtons(true);
        setToggleIcon("bi-chevron-down");
        chatState = "chat";
        const inicio = messages.querySelector(".inicio-message");
        if (inicio && messages.querySelectorAll(".message").length === 0) {
            inicio.style.display = "block";
        }
    }

    function toggleHeaderButtons(show){
        backBtn.style.display = show ? "flex" : "none";
        closeBtn.style.display = show ? "flex" : "none";
    }

    function enableFullChatInput() {
        input.disabled = false;
        sendBtn.disabled = false;

        micBtn.disabled = false;
        micBtn.style.opacity = "1";
        micBtn.style.pointerEvents = "auto";
        micBtn.style.display = "flex";

        stopBtn.style.display = "none";

        input.placeholder = "Escribe tu mensaje...";
        input.classList.remove("input-disabled");
    }

    function disableChatInput() {
        input.disabled = true;
        sendBtn.classList.add("disabled");
        micBtn.disabled = true;
        micBtn.style.opacity = "0.5";
        micBtn.style.pointerEvents = "none";
        input.placeholder = "El chat ha finalizado";
        input.classList.add("input-disabled");
    }

    function lockInput(){
        isBotBusy = true;
        input.disabled = true;
        sendBtn.classList.add("disabled");
        micBtn.classList.add("disabled");
        input.placeholder = "Capybot está escribiendo...";
    }

    function unlockInput(){
        isBotBusy = false;
        input.disabled = false;
        sendBtn.classList.remove("disabled");
        micBtn.classList.remove("disabled");
        input.placeholder = "Escribe tu mensaje";
        input.focus();
    }

    function autoResizeInput() {
        input.style.height = "20px";
        input.style.height = Math.min(input.scrollHeight, 120) + "px";
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
        enableFullChatInput();

        messages.querySelectorAll(".message").forEach(m => m.remove());
        setStartDay();
        input.focus();
        resetInactivityTimer();
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
            enableFullChatInput();
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

    ///messages-API
    async function sendMessage() {
        resetInactivityTimer();
        const convo = conversations.find(c => c.id === activeConversationId);

        if (isBotBusy || !activeConversationId || !convo || convo.ended) return;

        const text = input.value.trim();
        if (!text) return;
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
        pauseInactivityTimer();

        try {
            const history = convo.messages.map(m =>
                `${m.sender === "user" ? "Usuario" : "CapyBot"}: ${m.text}`
            );
            const res = await fetch("https://capy-ai-api.onrender.com/chat", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    question: text,
                    history:history
                })
            });

            const data = await res.json();
            typingMsg.remove();

            appendBotMessage(data.reply, true);
            clearTimeout(inactivityTimer);
            convo.messages.push({ sender: "bot", text: data.reply });
            convo.time = getTime();
            renderConversationList();

        } catch {
            typingMsg.remove();
            appendBotMessage("No puedo conectar con el servidor.", true);
        }
        unlockInput();
        isBotBusy = false;
        resumeInactivityTimer();
    }

    function appendUserMessage(text) {
        const msg = document.createElement("div");
        msg.className = "message user";
        msg.innerHTML = `
        <div class="bubble-container">
            <div class="bubble">${text}</div>
            <div class="timestamp">${getTime()}</div>
        </div>
    `;
        messages.appendChild(msg);
        scrollBottom();
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
            const formatted = line
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

            bubble.innerHTML = linkify(formatted);

            bubbleContainer.appendChild(bubble);
        });

        messages.appendChild(block);
        scrollBottom();
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


    //timer
    function getTime() {
        return new Date().toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit"
        });
    }

    function getTimestamp() {
        const now = new Date();
        return now.toLocaleTimeString("en-US", {
            hour: "2-digit",
            minute: "2-digit",
            hour12: true
        }).toLowerCase();
    }

    //link
    function linkify (text){
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        return text.replace(urlRegex, url =>
            `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`
        );
    }

    //others
    function scrollBottom() {
        messages.scrollTop = messages.scrollHeight;
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


    //conversaciones



});
