<?php
session_start();

require_once 'includes/db.php';
require_once 'includes/helpers.php';

if (!is_logged_in() || ($_SESSION['role'] ?? '') !== 'alumni') {
    header("Location: login.php");
    exit;
}

$alumni_name = $_SESSION['name'] ?? 'Alumni';
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Chat with Students</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    background: #f4f7fb;
    font-family: "Segoe UI", Arial, sans-serif;
    color: #172033;
}

.chat-wrapper {
    min-height: 100vh;
    padding: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-app {
    width: 100%;
    max-width: 1250px;
    height: 760px;
    background: white;
    border-radius: 24px;
    overflow: hidden;
    display: flex;
    box-shadow: 0 20px 60px rgba(20, 30, 60, 0.12);
}

.sidebar {
    width: 350px;
    background: #ffffff;
    border-right: 1px solid #edf0f5;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 26px 24px 20px;
}

.sidebar-title h4 {
    margin: 0;
    font-weight: 700;
}

.sidebar-title span {
    font-size: 13px;
    color: #7b8494;
}

.search-box {
    margin-top: 20px;
    position: relative;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 13px;
    color: #8b94a5;
}

.search-box input {
    width: 100%;
    border: 1px solid #e4e8ef;
    background: #f7f9fc;
    border-radius: 12px;
    padding: 11px 14px 11px 42px;
    outline: none;
}

.search-box input:focus {
    border-color: #10b981;
    background: white;
}

.contacts {
    flex: 1;
    overflow-y: auto;
    padding: 8px 12px 20px;
}

.contact {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 13px 12px;
    margin-bottom: 5px;
    border-radius: 15px;
    cursor: pointer;
    transition: 0.2s;
}

.contact:hover {
    background: #f5f8fb;
}

.contact.active {
    background: #eafaf4;
}

.avatar {
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #0f766e);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

.contact-info {
    min-width: 0;
    flex: 1;
}

.contact-name {
    font-weight: 600;
    font-size: 15px;
}

.contact-preview {
    font-size: 13px;
    color: #8a93a3;
    margin-top: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.chat-header {
    height: 82px;
    border-bottom: 1px solid #edf0f5;
    padding: 15px 25px;
    display: flex;
    align-items: center;
    gap: 13px;
}

.chat-header-info {
    flex: 1;
}

.chat-header-name {
    font-weight: 700;
    font-size: 16px;
}

.chat-header-status {
    font-size: 12px;
    color: #10a875;
    margin-top: 2px;
}

.back-btn {
    display: none;
    border: none;
    background: none;
    font-size: 22px;
}

.chat-window {
    flex: 1;
    overflow-y: auto;
    padding: 28px;
    background: #f7f9fc;
}

.empty-chat {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #8b94a5;
    text-align: center;
}

.empty-chat-icon {
    width: 75px;
    height: 75px;
    border-radius: 50%;
    background: #e9f8f3;
    color: #10a875;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    margin-bottom: 18px;
}

.empty-chat h5 {
    color: #374151;
    font-weight: 700;
}

.message-row {
    display: flex;
    margin-bottom: 15px;
}

.message-row.me {
    justify-content: flex-end;
}

.message-bubble {
    max-width: 70%;
    padding: 11px 15px;
    border-radius: 17px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;
}

.message-row.me .message-bubble {
    background: linear-gradient(135deg, #10b981, #0f9f78);
    color: white;
    border-bottom-right-radius: 5px;
}

.message-row.other .message-bubble {
    background: white;
    color: #263142;
    border: 1px solid #edf0f5;
    border-bottom-left-radius: 5px;
}

.message-time {
    display: block;
    font-size: 10px;
    margin-top: 5px;
    opacity: 0.65;
}

.composer {
    padding: 16px 20px;
    border-top: 1px solid #edf0f5;
    background: white;
    display: flex;
    gap: 10px;
    align-items: flex-end;
}

.composer textarea {
    flex: 1;
    resize: none;
    border: 1px solid #e1e6ed;
    background: #f7f9fc;
    border-radius: 15px;
    padding: 12px 15px;
    min-height: 48px;
    max-height: 120px;
    outline: none;
    font-size: 14px;
}

.composer textarea:focus {
    border-color: #10b981;
    background: white;
}

.emoji-btn,
.send-btn {
    width: 46px;
    height: 46px;
    border-radius: 13px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.emoji-btn {
    background: #f1f4f8;
    color: #647084;
}

.send-btn {
    background: linear-gradient(135deg, #10b981, #0f766e);
    color: white;
}

.send-btn:disabled {
    opacity: 0.45;
}

.empty-contacts {
    text-align: center;
    color: #8a93a3;
    padding: 40px 20px;
}

@media (max-width: 768px) {

    .chat-wrapper {
        padding: 0;
    }

    .chat-app {
        height: 100vh;
        border-radius: 0;
    }

    .sidebar {
        width: 100%;
    }

    .chat-area {
        display: none;
    }

    .chat-app.mobile-chat-open .sidebar {
        display: none;
    }

    .chat-app.mobile-chat-open .chat-area {
        display: flex;
    }

    .back-btn {
        display: block;
    }

    .message-bubble {
        max-width: 82%;
    }
}

</style>

</head>

<body>

<div class="chat-wrapper">

<div class="chat-app" id="chatApp">

    <div class="sidebar">

        <div class="sidebar-header">

            <div class="sidebar-title">

                <h4>Student Connect</h4>

                <span>
                    Guide and connect with students
                </span>

            </div>

            <div class="search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search students..."
                >

            </div>

        </div>

        <div
            class="contacts"
            id="contacts">

            <div class="empty-contacts">
                Loading students...
            </div>

        </div>

    </div>


    <div class="chat-area">

        <div class="chat-header">

            <button
                class="back-btn"
                id="backBtn">

                <i class="bi bi-arrow-left"></i>

            </button>

            <div
                class="avatar"
                id="headerAvatar">

                <i class="bi bi-person"></i>

            </div>

            <div class="chat-header-info">

                <div
                    class="chat-header-name"
                    id="headerName">

                    Select a student

                </div>

                <div
                    class="chat-header-status"
                    id="headerStatus">

                    Start a conversation

                </div>

            </div>

        </div>


        <div
            class="chat-window"
            id="chatWindow">

            <div class="empty-chat">

                <div class="empty-chat-icon">

                    <i class="bi bi-chat-dots"></i>

                </div>

                <h5>Start a conversation</h5>

                <p>
                    Select a student from the left to
                    provide guidance and mentorship.
                </p>

            </div>

        </div>


        <div class="composer">

            <button
                class="emoji-btn"
                id="emojiBtn">

                <i class="bi bi-emoji-smile"></i>

            </button>

            <textarea
                id="messageInput"
                placeholder="Type your message..."
                disabled></textarea>

            <button
                class="send-btn"
                id="sendBtn"
                disabled>

                <i class="bi bi-send-fill"></i>

            </button>

        </div>

    </div>

</div>

</div>


<script>

const currentUserId = <?php echo (int)$_SESSION['user_id']; ?>;

let activeUserId = 0;
let latestMessageId = 0;
let contactsCache = [];


function getInitials(name) {

    if (!name) return "?";

    const parts =
        name.trim().split(/\s+/);

    if (parts.length === 1) {
        return parts[0]
            .substring(0, 2)
            .toUpperCase();
    }

    return (
        parts[0][0] +
        parts[parts.length - 1][0]
    ).toUpperCase();
}


function escapeHtml(text) {

    const div =
        document.createElement("div");

    div.textContent = text ?? "";

    return div.innerHTML;
}


function formatTime(dateString) {

    if (!dateString) return "";

    const date =
        new Date(
            dateString.replace(" ", "T")
        );

    if (isNaN(date.getTime())) {
        return "";
    }

    return date.toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit"
    });
}


async function fetchContacts() {

    try {

        const response =
            await fetch(
                "chat_actions.php?action=get_contacts&role=student"
            );

        const data =
            await response.json();

        if (!data.success) return;

        contactsCache =
            data.contacts;

        renderContacts();

    } catch (error) {

        console.error(error);

    }
}


function renderContacts() {

    const container =
        document.getElementById("contacts");

    const search =
        document
            .getElementById("searchInput")
            .value
            .toLowerCase()
            .trim();

    const filtered =
        contactsCache.filter(user =>
            user.name
                .toLowerCase()
                .includes(search) ||

            user.email
                .toLowerCase()
                .includes(search)
        );

    if (filtered.length === 0) {

        container.innerHTML = `
            <div class="empty-contacts">
                <i class="bi bi-person-x fs-3"></i>
                <div class="mt-2">
                    No students found
                </div>
            </div>
        `;

        return;
    }

    container.innerHTML = "";

    filtered.forEach(user => {

        const div =
            document.createElement("div");

        div.className =
            "contact " +
            (activeUserId == user.id
                ? "active"
                : "");

        const preview =
            user.last_message ||
            "Start a conversation";

        div.innerHTML = `
            <div class="avatar">
                ${escapeHtml(
                    getInitials(user.name)
                )}
            </div>

            <div class="contact-info">

                <div class="contact-name">
                    ${escapeHtml(user.name)}
                </div>

                <div class="contact-preview">
                    ${escapeHtml(preview)}
                </div>

            </div>
        `;

        div.addEventListener(
            "click",
            () => openChat(user)
        );

        container.appendChild(div);

    });
}


function openChat(user) {

    activeUserId =
        parseInt(user.id);

    latestMessageId = 0;

    document
        .getElementById("headerName")
        .textContent = user.name;

    document
        .getElementById("headerStatus")
        .textContent =
        "Student • Available for conversation";

    document
        .getElementById("headerAvatar")
        .textContent =
        getInitials(user.name);

    document
        .getElementById("messageInput")
        .disabled = false;

    document
        .getElementById("sendBtn")
        .disabled = false;

    document
        .getElementById("chatWindow")
        .innerHTML = "";

    renderContacts();

    document
        .getElementById("chatApp")
        .classList.add(
            "mobile-chat-open"
        );

    fetchMessages(true);

    document
        .getElementById("messageInput")
        .focus();
}


async function fetchMessages(initial = false) {

    if (!activeUserId) return;

    let url =
        `chat_actions.php?action=get_messages&other_id=${activeUserId}`;

    if (!initial && latestMessageId > 0) {

        url +=
            `&since_id=${latestMessageId}`;
    }

    try {

        const response =
            await fetch(url);

        const data =
            await response.json();

        if (!data.success) return;

        data.messages.forEach(message => {

            appendMessage(message);

            latestMessageId =
                Math.max(
                    latestMessageId,
                    parseInt(message.id)
                );

        });

        scrollToBottom();

    } catch (error) {

        console.error(error);

    }
}


function appendMessage(message) {

    const chatWindow =
        document.getElementById(
            "chatWindow"
        );

    const isMe =
        parseInt(message.sender_id)
        === currentUserId;

    const row =
        document.createElement("div");

    row.className =
        "message-row " +
        (isMe ? "me" : "other");

    const bubble =
        document.createElement("div");

    bubble.className =
        "message-bubble";

    /*
    Safe insertion:
    textContent prevents HTML/script injection.
    */

    bubble.textContent =
        message.message;

    const time =
        document.createElement("span");

    time.className =
        "message-time";

    time.textContent =
        formatTime(
            message.created_at
        );

    bubble.appendChild(time);

    row.appendChild(bubble);

    chatWindow.appendChild(row);
}


async function sendMessage() {

    if (!activeUserId) return;

    const input =
        document.getElementById(
            "messageInput"
        );

    const message =
        input.value.trim();

    if (!message) return;

    const formData =
        new FormData();

    formData.append(
        "other_id",
        activeUserId
    );

    formData.append(
        "message",
        message
    );

    try {

        const response =
            await fetch(
                "chat_actions.php?action=send_message",
                {
                    method: "POST",
                    body: formData
                }
            );

        const data =
            await response.json();

        if (data.success) {

            input.value = "";

            await fetchMessages(false);

            fetchContacts();

            input.focus();

        } else {

            alert(
                data.error ||
                "Message could not be sent"
            );

        }

    } catch (error) {

        console.error(error);

        alert(
            "Something went wrong."
        );
    }
}


function scrollToBottom() {

    const chatWindow =
        document.getElementById(
            "chatWindow"
        );

    chatWindow.scrollTop =
        chatWindow.scrollHeight;
}


document
    .getElementById("searchInput")
    .addEventListener(
        "input",
        renderContacts
    );


document
    .getElementById("messageInput")
    .addEventListener(
        "keydown",
        function(event) {

            if (
                event.key === "Enter" &&
                !event.shiftKey
            ) {

                event.preventDefault();

                sendMessage();
            }

        }
    );


document
    .getElementById("sendBtn")
    .addEventListener(
        "click",
        sendMessage
    );


document
    .getElementById("emojiBtn")
    .addEventListener(
        "click",
        function() {

            const input =
                document.getElementById(
                    "messageInput"
                );

            input.value += " 😊";

            input.focus();

        }
    );


document
    .getElementById("backBtn")
    .addEventListener(
        "click",
        function() {

            document
                .getElementById("chatApp")
                .classList.remove(
                    "mobile-chat-open"
                );

        }
    );


fetchContacts();


setInterval(
    () => {

        fetchMessages(false);

        fetchContacts();

    },
    3000
);

</script>

</body>

</html>